<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\PembayaranSpp;
use App\Models\QrToken;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\TarifSpp;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\PresensiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display the official LKP Sang Siroju Lillah Multi-Cabang Admin Dashboard.
     * Follows KONSEP_SISTEM_MANAJEMEN_LES.md specification.
     */
    public function index(Request $request): View
    {
        $viewRole = $request->query('role', 'cabang'); // 'cabang' | 'pusat'
        $selectedCabangParam = $request->query('cabang_id', 'cab-ponokawan');

        // 1. Ambil Data Cabang
        $cabangs = Cabang::withCount(['siswa' => fn ($q) => $q->where('status', 'aktif')])
            ->get();

        if ($cabangs->isEmpty()) {
            // Fallback default jika database kosong
            $selectedCabang = [
                'id' => 'cab-ponokawan',
                'kode' => 'CAB-PNK',
                'nama' => 'Cabang Ponokawan',
                'alamat' => 'Jl. Raya Ponokawan No. 12, Krian, Sidoarjo',
                'pimpinan' => 'Pimpinan Cabang Ponokawan',
                'status' => 'aktif',
                'siswa_aktif' => 80,
                'guru_aktif' => 12,
                'kas_bersih' => 12550000,
                'utilisasi_slot' => 97.2,
            ];
            $daftarCabang = [$selectedCabang];
            $currentCabang = $selectedCabang;
            $cabangId = 'cab-ponokawan';
        } else {
            $matched = $cabangs->first(fn ($c) => $c->kode === $selectedCabangParam || $c->id === $selectedCabangParam)
                ?? $cabangs->firstWhere('id', 'cab-ponokawan')
                ?? $cabangs->first();
            $cabangId = $matched->id;

            $daftarCabang = $cabangs->map(function ($c) {
                $guruCount = $c->guru()->count();
                $siswaCount = $c->siswa_count;
                $slotTotal = max(1, $guruCount * 6);
                $utilisasi = round(($siswaCount / $slotTotal) * 100, 1);
                $kasMasuk = (float) Transaksi::where('cabang_id', $c->id)->where('tipe', 'masuk')->sum('nominal');
                $kasKeluar = (float) Transaksi::where('cabang_id', $c->id)->where('tipe', 'keluar')->sum('nominal');
                $pimpinanUser = User::where('cabang_id', $c->id)->where('role', 'cabang')->first();

                return [
                    'id' => $c->id,
                    'kode' => $c->kode,
                    'nama' => $c->nama,
                    'alamat' => $c->alamat,
                    'pimpinan' => $pimpinanUser ? $pimpinanUser->name : 'Pimpinan '.$c->nama,
                    'status' => $c->status,
                    'siswa_aktif' => $siswaCount,
                    'guru_aktif' => $guruCount,
                    'kas_bersih' => $kasMasuk - $kasKeluar,
                    'utilisasi_slot' => min(100.0, $utilisasi),
                ];
            })->all();

            $currentCabang = collect($daftarCabang)->firstWhere('id', $cabangId) ?? $daftarCabang[0];
        }

        // 2. Master Tarif SPP Global & Biaya Pendaftaran
        $tarifSpps = TarifSpp::all();
        if ($tarifSpps->isNotEmpty()) {
            $masterTarifSpp = $tarifSpps->map(fn ($t) => [
                'jenjang' => $t->jenjang,
                'kelas_label' => "Kelas {$t->kelas_min} s/d {$t->kelas_max}",
                'nominal' => (float) $t->nominal,
            ])->all();
        } else {
            $masterTarifSpp = [
                ['jenjang' => 'TK', 'kelas_label' => 'Kelas 0 (TK A & TK B)', 'nominal' => 150000],
                ['jenjang' => 'SD', 'kelas_label' => 'Kelas 1 s/d 6', 'nominal' => 150000],
                ['jenjang' => 'SMP', 'kelas_label' => 'Kelas 7 s/d 9', 'nominal' => 200000],
                ['jenjang' => 'SMA_SMK', 'kelas_label' => 'Kelas 10 s/d 12', 'nominal' => 225000],
            ];
        }

        $biayaPendaftaranBaku = (float) Setting::get('biaya_pendaftaran', '100000');

        // 3. KPI Ringkasan Cabang
        $totalSiswaAktif = Siswa::where('cabang_id', $cabangId)->where('status', 'aktif')->count();
        $unassignedCount = Siswa::where('cabang_id', $cabangId)->where('status', 'aktif')->whereNull('guru_id')->count();
        $tkCount = Siswa::where('cabang_id', $cabangId)->where('status', 'aktif')->where('jenjang', 'TK')->count();
        $sdCount = Siswa::where('cabang_id', $cabangId)->where('status', 'aktif')->where('jenjang', 'SD')->count();
        $smpCount = Siswa::where('cabang_id', $cabangId)->where('status', 'aktif')->where('jenjang', 'SMP')->count();
        $smaCount = Siswa::where('cabang_id', $cabangId)->where('status', 'aktif')->where('jenjang', 'SMA_SMK')->count();

        $branchGurus = Guru::whereHas('cabang', fn ($q) => $q->where('cabang.id', $cabangId))->get();
        $totalGuru = $branchGurus->count();
        $kapasitasSlotTotal = $totalGuru * 6;
        $slotTerpakai = Siswa::whereIn('guru_id', $branchGurus->pluck('id'))->where('status', 'aktif')->count();
        $slotTersedia = max(0, $kapasitasSlotTotal - $slotTerpakai);

        $guruPenuhCount = $branchGurus->filter(fn ($g) => $g->active_students_count >= 6)->count();
        $guruTersediaCount = max(0, $totalGuru - $guruPenuhCount);

        $kasMasukBulanIni = (float) Transaksi::where('cabang_id', $cabangId)->where('tipe', 'masuk')->sum('nominal');
        $kasKeluarBulanIni = (float) Transaksi::where('cabang_id', $cabangId)->where('tipe', 'keluar')->sum('nominal');

        $sppTerbayar = PembayaranSpp::where('cabang_id', $cabangId)->where('status', 'lunas')->count();
        $sppTertunggak = PembayaranSpp::where('cabang_id', $cabangId)->where('status', 'belum_bayar')->count();
        $nominalTertunggak = (float) PembayaranSpp::where('cabang_id', $cabangId)->where('status', 'belum_bayar')->sum('nominal');

        $presensiSummary = app(PresensiService::class)->getHariIniSummary($cabangId);

        $kpi = [
            'siswa_aktif' => [
                'total' => $totalSiswaAktif,
                'belum_ada_guru' => $unassignedCount,
                'tk' => $tkCount,
                'sd' => $sdCount,
                'smp' => $smpCount,
                'sma_smk' => $smaCount,
            ],
            'kuota_guru' => [
                'total_guru' => $totalGuru,
                'kapasitas_slot_total' => $kapasitasSlotTotal,
                'slot_terpakai' => $slotTerpakai,
                'slot_tersedia' => $slotTersedia,
                'guru_penuh' => $guruPenuhCount,
                'guru_tersedia' => $guruTersediaCount,
                'rasio_maksimal' => 6,
            ],
            'presensi_guru_hari_ini' => [
                'total_bertugas' => $presensiSummary['totalGuruBertugas'],
                'sudah_datang' => $presensiSummary['sudahDatang'],
                'sudah_pulang' => $presensiSummary['sudahPulang'],
                'sedang_mengajar' => $presensiSummary['sedangMengajar'],
                'belum_hadir' => $presensiSummary['belumHadir'],
                'izin_sakit' => $presensiSummary['izinSakit'],
                'persentase_kehadiran' => $presensiSummary['persentaseKehadiran'],
            ],
            'buku_kas_bulan_ini' => [
                'pemasukan' => $kasMasukBulanIni,
                'pengeluaran' => $kasKeluarBulanIni,
                'saldo_bersih' => $kasMasukBulanIni - $kasKeluarBulanIni,
                'spp_terbayar' => $sppTerbayar,
                'spp_tertunggak' => $sppTertunggak,
                'nominal_tertunggak' => $nominalTertunggak,
            ],
        ];

        // 4. Sesi Presensi QR Dinamis (Ambil aktif atau generate riil)
        $qrTokenModel = QrToken::where('cabang_id', $cabangId)->latest('created_at')->first();
        if (! $qrTokenModel || $qrTokenModel->isExpired()) {
            $qrTokenModel = app(PresensiService::class)->generateQrToken($cabangId, 'auto', 60);
        }

        $qrTokenSession = [
            'token' => $qrTokenModel->token,
            'tipe' => $qrTokenModel->tipe,
            'expires_in_seconds' => max(1, Carbon::now()->diffInSeconds($qrTokenModel->expires_at, false)),
            'dibuat_pukul' => $qrTokenModel->created_at ? $qrTokenModel->created_at->format('H:i:s').' WIB' : Carbon::now()->format('H:i:s').' WIB',
            'lokasi_layar' => 'Meja Resepsionis Cabang',
        ];

        // 5. Siswa Belum Punya Guru
        $unassignedDb = Siswa::where('cabang_id', $cabangId)
            ->where('status', 'aktif')
            ->whereNull('guru_id')
            ->get();

        if ($unassignedDb->isNotEmpty()) {
            $unassignedStudents = $unassignedDb->map(fn ($s) => [
                'id' => $s->id,
                'nama' => $s->nama,
                'jenjang' => $s->jenjang,
                'kelas' => $s->kelas,
                'tanggal_daftar' => $s->created_at ? $s->created_at->translatedFormat('d M Y') : '30 Okt 2026',
                'no_telp_ortu' => $s->no_telp_ortu,
                'nama_ibu' => $s->nama_ibu,
                'biaya_daftar' => 100000,
                'diskon' => 0,
                'tagihan_spp_pertama' => TarifSpp::getRate($s->jenjang, $s->kelas),
            ])->all();
        } else {
            $unassignedStudents = [];
        }

        // 6. Daftar Guru & Status Kuota Slot (Maks 6 Siswa)
        $bgColors = [
            'bg-slate-900 text-white',
            'bg-blue-900 text-white',
            'bg-emerald-900 text-white',
            'bg-indigo-950 text-white',
            'bg-amber-950 text-white',
            'bg-rose-950 text-white',
        ];

        $teachersSlotStatus = $branchGurus->map(function ($g, $idx) use ($bgColors) {
            $activeCount = $g->active_students_count;
            $sisaSlot = max(0, 6 - $activeCount);
            $initials = collect(explode(' ', $g->nama))->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('');

            return [
                'id' => $g->id,
                'nama' => $g->nama,
                'email' => $g->email,
                'no_telp' => $g->no_telp,
                'jenjang_kualifikasi' => $g->jenjang_list,
                'siswa_count' => $activeCount,
                'sisa_slot' => $sisaSlot,
                'status_slot' => $sisaSlot === 0 ? 'penuh' : 'tersedia',
                'inisial' => $initials ?: 'GR',
                'warna' => $bgColors[$idx % count($bgColors)],
            ];
        })->all();

        // 7. Jadwal Sesi Mengajar Hari Ini
        $jadwals = Jadwal::with(['guru', 'siswa'])->where('cabang_id', $cabangId)->get();
        if ($jadwals->isNotEmpty()) {
            $todaySchedules = $jadwals->map(fn ($j) => [
                'id' => $j->id,
                'jam_mulai' => substr($j->jam_mulai, 0, 5),
                'jam_selesai' => substr($j->jam_selesai, 0, 5),
                'guru' => $j->guru ? $j->guru->nama : 'Belum Ditentukan',
                'siswa' => $j->siswa ? "{$j->siswa->nama} (Kelas {$j->siswa->kelas} {$j->siswa->jenjang})" : 'Sesi Terbuka',
                'jenjang' => $j->siswa ? $j->siswa->jenjang : 'SD',
                'ruangan' => 'Bilik Les Belajar',
                'status_presensi' => 'hadir',
                'jam_scan' => substr($j->jam_mulai, 0, 5).' WIB',
            ])->all();
        } else {
            $todaySchedules = [];
        }

        // 8. Riwayat Pembayaran SPP & Kwitansi Resmi
        $sppDb = PembayaranSpp::with('siswa')->where('cabang_id', $cabangId)->latest('created_at')->take(6)->get();
        if ($sppDb->isNotEmpty()) {
            $recentPayments = $sppDb->map(fn ($p) => [
                'id' => $p->id,
                'no_kwitansi' => $p->no_kwitansi ?? 'KW/'.$currentCabang['kode'].'/'.now()->year.'/'.now()->month.'/'.substr($p->id, 0, 4),
                'siswa' => $p->siswa ? $p->siswa->nama : 'Siswa',
                'jenjang' => $p->siswa ? $p->siswa->jenjang : 'SD',
                'kelas' => $p->siswa ? $p->siswa->kelas : 5,
                'bulan' => $p->nama_bulan.' '.$p->tahun,
                'nominal' => (float) $p->nominal,
                'status' => $p->status,
                'tanggal_bayar' => $p->tanggal_bayar ? $p->tanggal_bayar->translatedFormat('d M Y') : '-',
                'nama_pembayar' => $p->nama_pembayar ?? '-',
                'no_wa_ortu' => $p->siswa ? $p->siswa->no_telp_ortu : '081234567890',
                'wa_link' => $p->generateWhatsAppLink(),
            ])->all();
        } else {
            $recentPayments = [];
        }

        // 9. Mutasi Buku Kas Cabang
        $transaksiDb = Transaksi::where('cabang_id', $cabangId)->latest('tanggal')->take(6)->get();
        if ($transaksiDb->isNotEmpty()) {
            $recentKasTransactions = $transaksiDb->map(fn ($t) => [
                'tipe' => $t->tipe,
                'kategori' => $t->kategori,
                'nominal' => (float) $t->nominal,
                'keterangan' => $t->keterangan,
                'tanggal' => $t->tanggal ? $t->tanggal->translatedFormat('d M Y') : now()->translatedFormat('d M Y'),
            ])->all();
        } else {
            $recentKasTransactions = [];
        }

        return view('admin.dashboard', compact(
            'viewRole',
            'daftarCabang',
            'currentCabang',
            'masterTarifSpp',
            'biayaPendaftaranBaku',
            'kpi',
            'unassignedStudents',
            'teachersSlotStatus',
            'todaySchedules',
            'recentPayments',
            'recentKasTransactions',
            'qrTokenSession'
        ));
    }
}
