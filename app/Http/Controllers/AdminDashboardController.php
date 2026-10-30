<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the official LKP Sang Siroju Lillah Multi-Cabang Admin Dashboard.
     * Follows KONSEP_SISTEM_MANAJEMEN_LES.md specification.
     */
    public function index(Request $request): View
    {
        // View Scope: 'cabang' (default Pimpinan Cabang) or 'pusat' (Pimpinan Pusat)
        $viewRole = $request->query('role', 'cabang'); // 'cabang' | 'pusat'
        $selectedCabangId = $request->query('cabang_id', 'CAB01');

        $daftarCabang = [
            [
                'id' => 'CAB01',
                'kode' => 'CAB01',
                'nama' => 'Cabang Yogyakarta Diponegoro',
                'alamat' => 'Jl. Pangeran Diponegoro No. 45, Gowongan, Jetis, Kota Yogyakarta',
                'pimpinan' => 'Ahmad Fauzi, M.Pd.',
                'status' => 'aktif',
                'siswa_aktif' => 134,
                'guru_aktif' => 24,
                'kas_bersih' => 12550000,
                'utilisasi_slot' => 89.6,
            ],
            [
                'id' => 'CAB02',
                'kode' => 'CAB02',
                'nama' => 'Cabang Surabaya Rungkut',
                'alamat' => 'Jl. Rungkut Madya No. 88, Rungkut Kidul, Surabaya',
                'pimpinan' => 'Siti Nurjanah, S.Pd.',
                'status' => 'aktif',
                'siswa_aktif' => 118,
                'guru_aktif' => 21,
                'kas_bersih' => 10840000,
                'utilisasi_slot' => 93.6,
            ],
            [
                'id' => 'CAB03',
                'kode' => 'CAB03',
                'nama' => 'Cabang Jakarta Selatan Tebet',
                'alamat' => 'Jl. Tebet Barat Dalam Raya No. 12, Tebet, Jakarta Selatan',
                'pimpinan' => 'Rian Hidayat, M.Si.',
                'status' => 'aktif',
                'siswa_aktif' => 142,
                'guru_aktif' => 25,
                'kas_bersih' => 15200000,
                'utilisasi_slot' => 94.7,
            ],
        ];

        // Active Branch Data
        $currentCabang = collect($daftarCabang)->firstWhere('id', $selectedCabangId) ?? $daftarCabang[0];

        // Master Tarif SPP Global (Ketentuan Pusat)
        $masterTarifSpp = [
            ['jenjang' => 'TK', 'kelas_label' => 'Kelas 0 (TK A & TK B)', 'nominal' => 120000],
            ['jenjang' => 'SD', 'kelas_label' => 'Kelas 1 s/d 6', 'nominal' => 150000],
            ['jenjang' => 'SMP', 'kelas_label' => 'Kelas 7 s/d 9', 'nominal' => 180000],
            ['jenjang' => 'SMA_SMK', 'kelas_label' => 'Kelas 10 s/d 12', 'nominal' => 220000],
        ];
        $biayaPendaftaranBaku = 100000;

        // KPI Ringkasan Cabang Aktif
        $kpi = [
            'siswa_aktif' => [
                'total' => $currentCabang['siswa_aktif'],
                'belum_ada_guru' => 5, // Siswa perlu auto-assign / penugasan
                'tk' => 12,
                'sd' => 58,
                'smp' => 42,
                'sma_smk' => 22,
            ],
            'kuota_guru' => [
                'total_guru' => $currentCabang['guru_aktif'],
                'kapasitas_slot_total' => $currentCabang['guru_aktif'] * 6, // 24 * 6 = 144 slot
                'slot_terpakai' => 129,
                'slot_tersedia' => 15,
                'guru_penuh' => 18, // 6/6 siswa
                'guru_tersedia' => 6, // < 6 siswa
                'rasio_maksimal' => 6,
            ],
            'presensi_guru_hari_ini' => [
                'total_bertugas' => 16,
                'sudah_datang' => 14,
                'belum_hadir' => 1,
                'izin_sakit' => 1,
                'persentase_kehadiran' => 93.3,
            ],
            'buku_kas_bulan_ini' => [
                'pemasukan' => 26850000,
                'pengeluaran' => 14300000,
                'saldo_bersih' => 12550000,
                'spp_terbayar' => 120, // siswa
                'spp_tertunggak' => 14, // siswa belum bayar
                'nominal_tertunggak' => 2380000,
            ],
        ];

        // Sesi Presensi QR Dinamis (Batas 15-30 detik)
        $qrTokenSession = [
            'token' => 'SIROJU-8492',
            'tipe' => 'datang', // 'datang' | 'pulang'
            'expires_in_seconds' => 18,
            'dibuat_pukul' => '14:15:30 WIB',
            'lokasi_layar' => 'Meja Resepsionis Lantai 1',
        ];

        // Siswa yang Belum Memiliki Guru (Siap untuk 1-Klik Auto-Assign)
        $unassignedStudents = [
            [
                'id' => 'SIS-00130',
                'nama' => 'Arkan Naufal Pratama',
                'jenjang' => 'SD',
                'kelas' => 5,
                'tanggal_daftar' => '30 Okt 2026',
                'no_telp_ortu' => '081234567801',
                'nama_ibu' => 'Ibu Dewi Lestari',
                'biaya_daftar' => 100000,
                'diskon' => 20000,
                'tagihan_spp_pertama' => 150000,
            ],
            [
                'id' => 'SIS-00131',
                'nama' => 'Clara Michelle Sudiro',
                'jenjang' => 'SMP',
                'kelas' => 8,
                'tanggal_daftar' => '30 Okt 2026',
                'no_telp_ortu' => '082198765432',
                'nama_ibu' => 'Ibu Natalia',
                'biaya_daftar' => 100000,
                'diskon' => 0,
                'tagihan_spp_pertama' => 180000,
            ],
            [
                'id' => 'SIS-00132',
                'nama' => 'Dimas Bagus Hermawan',
                'jenjang' => 'SD',
                'kelas' => 3,
                'tanggal_daftar' => '29 Okt 2026',
                'no_telp_ortu' => '085711223344',
                'nama_ibu' => 'Ibu Wulandari',
                'biaya_daftar' => 100000,
                'diskon' => 0,
                'tagihan_spp_pertama' => 150000,
            ],
            [
                'id' => 'SIS-00133',
                'nama' => 'Fathia Rahma Azzahra',
                'jenjang' => 'TK',
                'kelas' => 0,
                'tanggal_daftar' => '29 Okt 2026',
                'no_telp_ortu' => '081399887766',
                'nama_ibu' => 'Ibu Siti Khodijah',
                'biaya_daftar' => 100000,
                'diskon' => 50000,
                'tagihan_spp_pertama' => 120000,
            ],
            [
                'id' => 'SIS-00134',
                'nama' => 'Gilang Ramadhan',
                'jenjang' => 'SMA_SMK',
                'kelas' => 11,
                'tanggal_daftar' => '28 Okt 2026',
                'no_telp_ortu' => '081900112233',
                'nama_ibu' => 'Ibu Nurhayati',
                'biaya_daftar' => 100000,
                'diskon' => 0,
                'tagihan_spp_pertama' => 220000,
            ],
        ];

        // Daftar Guru & Status Slot (Kapasitas Maksimal 6 Siswa)
        $teachersSlotStatus = [
            [
                'id' => 'GUR-01',
                'nama' => 'Budi Santoso, S.Pd.',
                'email' => 'budi.santoso@simanles.sch.id',
                'no_telp' => '081234567890',
                'jenjang_kualifikasi' => ['SD', 'SMP'],
                'siswa_count' => 6,
                'sisa_slot' => 0,
                'status_slot' => 'penuh',
                'inisial' => 'BS',
                'warna' => 'bg-slate-900 text-white',
            ],
            [
                'id' => 'GUR-02',
                'nama' => 'Sarah Az-Zahra, M.Pd.',
                'email' => 'sarah.azzahra@simanles.sch.id',
                'no_telp' => '081234567891',
                'jenjang_kualifikasi' => ['SMP', 'SMA_SMK'],
                'siswa_count' => 5,
                'sisa_slot' => 1,
                'status_slot' => 'tersedia',
                'inisial' => 'SA',
                'warna' => 'bg-blue-900 text-white',
            ],
            [
                'id' => 'GUR-03',
                'nama' => 'Dian Maharani, S.Pd.',
                'email' => 'dian.maharani@simanles.sch.id',
                'no_telp' => '081234567892',
                'jenjang_kualifikasi' => ['SD'],
                'siswa_count' => 4,
                'sisa_slot' => 2,
                'status_slot' => 'tersedia',
                'inisial' => 'DM',
                'warna' => 'bg-emerald-900 text-white',
            ],
            [
                'id' => 'GUR-04',
                'nama' => 'Fadhil Ramadhan, S.Si.',
                'email' => 'fadhil.r@simanles.sch.id',
                'no_telp' => '081234567893',
                'jenjang_kualifikasi' => ['SMP', 'SMA_SMK'],
                'siswa_count' => 6,
                'sisa_slot' => 0,
                'status_slot' => 'penuh',
                'inisial' => 'FR',
                'warna' => 'bg-indigo-950 text-white',
            ],
            [
                'id' => 'GUR-05',
                'nama' => 'Lestari Handayani, S.Pd.',
                'email' => 'lestari.h@simanles.sch.id',
                'no_telp' => '081234567894',
                'jenjang_kualifikasi' => ['TK', 'SD'],
                'siswa_count' => 3,
                'sisa_slot' => 3,
                'status_slot' => 'tersedia',
                'inisial' => 'LH',
                'warna' => 'bg-amber-950 text-white',
            ],
            [
                'id' => 'GUR-06',
                'nama' => 'Ahmad Zakaria, M.Sc.',
                'email' => 'ahmad.zakaria@simanles.sch.id',
                'no_telp' => '081234567895',
                'jenjang_kualifikasi' => ['SMA_SMK'],
                'siswa_count' => 5,
                'sisa_slot' => 1,
                'status_slot' => 'tersedia',
                'inisial' => 'AZ',
                'warna' => 'bg-teal-950 text-white',
            ],
        ];

        // Jadwal Mengajar Hari Ini (Jumat) & Deteksi Bentrok Lintas Cabang
        $todaySchedules = [
            [
                'id' => 'JAD-01',
                'jam_mulai' => '13:30',
                'jam_selesai' => '15:00',
                'guru' => 'Budi Santoso, S.Pd.',
                'siswa' => 'Rangga Wijaya (Kelas 5 SD)',
                'jenjang' => 'SD',
                'ruangan' => 'Bilik Les 01',
                'status_presensi' => 'hadir',
                'jam_scan' => '13:18 WIB',
            ],
            [
                'id' => 'JAD-02',
                'jam_mulai' => '14:00',
                'jam_selesai' => '15:30',
                'guru' => 'Sarah Az-Zahra, M.Pd.',
                'siswa' => 'Kayla Anandita (Kelas 8 SMP)',
                'jenjang' => 'SMP',
                'ruangan' => 'Bilik Les 02',
                'status_presensi' => 'hadir',
                'jam_scan' => '13:42 WIB',
            ],
            [
                'id' => 'JAD-03',
                'jam_mulai' => '15:30',
                'jam_selesai' => '17:00',
                'guru' => 'Dian Maharani, S.Pd.',
                'siswa' => 'Alvino Putra (Kelas 4 SD)',
                'jenjang' => 'SD',
                'ruangan' => 'Bilik Les 03',
                'status_presensi' => 'hadir',
                'jam_scan' => '15:10 WIB',
            ],
            [
                'id' => 'JAD-04',
                'jam_mulai' => '16:00',
                'jam_selesai' => '17:30',
                'guru' => 'Ahmad Zakaria, M.Sc.',
                'siswa' => 'Jonathan Christian (Kelas 12 SMA)',
                'jenjang' => 'SMA_SMK',
                'ruangan' => 'Bilik Les 04',
                'status_presensi' => 'menunggu_scan',
                'jam_scan' => '-',
            ],
            [
                'id' => 'JAD-05',
                'jam_mulai' => '16:30',
                'jam_selesai' => '18:00',
                'guru' => 'Lestari Handayani, S.Pd.',
                'siswa' => 'Kinara Putri (Kelas TK B)',
                'jenjang' => 'TK',
                'ruangan' => 'Bilik Ceria TK',
                'status_presensi' => 'menunggu_scan',
                'jam_scan' => '-',
            ],
            [
                'id' => 'JAD-06',
                'jam_mulai' => '18:30',
                'jam_selesai' => '20:00',
                'guru' => 'Fadhil Ramadhan, S.Si.',
                'siswa' => 'Farhan Maulana (Kelas 11 SMA)',
                'jenjang' => 'SMA_SMK',
                'ruangan' => 'Bilik Les 01',
                'status_presensi' => 'terjadwal',
                'jam_scan' => '-',
            ],
        ];

        // Riwayat Pembayaran SPP & Format Kwitansi Resmi Sesuai Dokumen
        // Format: KW/{KODE_CABANG}/{TAHUN}/{BULAN_2_DIGIT}/{NOMOR_URUT_4_DIGIT}
        $recentPayments = [
            [
                'id' => 'PAY-001',
                'no_kwitansi' => 'KW/CAB01/2026/10/0042',
                'siswa' => 'Nabila Putri Santoso',
                'jenjang' => 'SD',
                'kelas' => 5,
                'bulan' => 'Oktober 2026',
                'nominal' => 150000,
                'status' => 'lunas',
                'tanggal_bayar' => '30 Okt 2026',
                'nama_pembayar' => 'Bpk. Hendra Santoso',
                'no_wa_ortu' => '081234567890',
                'wa_link' => 'https://wa.me/6281234567890?text='.urlencode('Halo Bpk. Hendra Santoso, terima kasih telah melunasi SPP LKP Sang Siroju Lillah Bulan Oktober 2026 atas nama Nabila Putri Santoso. No Kwitansi: KW/CAB01/2026/10/0042 sebesar Rp 150.000.'),
            ],
            [
                'id' => 'PAY-002',
                'no_kwitansi' => 'KW/CAB01/2026/10/0041',
                'siswa' => 'Rizky Pratama',
                'jenjang' => 'SMP',
                'kelas' => 8,
                'bulan' => 'Oktober 2026',
                'nominal' => 180000,
                'status' => 'lunas',
                'tanggal_bayar' => '30 Okt 2026',
                'nama_pembayar' => 'Ibu Ratna Kumala',
                'no_wa_ortu' => '081987654321',
                'wa_link' => 'https://wa.me/6281987654321?text='.urlencode('Halo Ibu Ratna, terima kasih telah melunasi SPP LKP Sang Siroju Lillah Bulan Oktober 2026 atas nama Rizky Pratama. No Kwitansi: KW/CAB01/2026/10/0041 sebesar Rp 180.000.'),
            ],
            [
                'id' => 'PAY-003',
                'no_kwitansi' => 'KW/CAB01/2026/10/0040',
                'siswa' => 'Amanda Aurelia',
                'jenjang' => 'SMA_SMK',
                'kelas' => 11,
                'bulan' => 'Oktober 2026',
                'nominal' => 220000,
                'status' => 'lunas',
                'tanggal_bayar' => '29 Okt 2026',
                'nama_pembayar' => 'Bpk. Nugroho',
                'no_wa_ortu' => '081344556677',
                'wa_link' => 'https://wa.me/6281344556677?text='.urlencode('Halo Bpk. Nugroho, terima kasih telah melunasi SPP LKP Sang Siroju Lillah atas nama Amanda Aurelia. No Kwitansi: KW/CAB01/2026/10/0040.'),
            ],
            [
                'id' => 'PAY-004',
                'no_kwitansi' => 'INV/CAB01/2026/10/0043',
                'siswa' => 'Bagas Pamungkas',
                'jenjang' => 'SMP',
                'kelas' => 9,
                'bulan' => 'Oktober 2026',
                'nominal' => 180000,
                'status' => 'belum_bayar',
                'tanggal_bayar' => '-',
                'nama_pembayar' => '-',
                'no_wa_ortu' => '085712341234',
                'wa_link' => 'https://wa.me/6285712341234?text='.urlencode('Pengingat: Tagihan SPP LKP Sang Siroju Lillah Bulan Oktober 2026 an. Bagas Pamungkas sebesar Rp 180.000 belum terselesaikan. Terima kasih.'),
            ],
        ];

        // Mutasi Buku Kas Terakhir Cabang
        $recentKasTransactions = [
            [
                'tipe' => 'masuk',
                'kategori' => 'SPP',
                'keterangan' => 'Pelunasan SPP Oktober 2026 (Nabila Putri Santoso)',
                'nominal' => 150000,
                'tanggal' => '30 Okt 2026',
            ],
            [
                'tipe' => 'masuk',
                'kategori' => 'Pendaftaran',
                'keterangan' => 'Pendaftaran Siswa Baru (Arkan Naufal - Diskon Rp 20.000)',
                'nominal' => 80000,
                'tanggal' => '30 Okt 2026',
            ],
            [
                'tipe' => 'keluar',
                'kategori' => 'Modul',
                'keterangan' => 'Cetak 30 Modul Latihan Soal UTS Semester Ganjil',
                'nominal' => 450000,
                'tanggal' => '29 Okt 2026',
            ],
            [
                'tipe' => 'keluar',
                'kategori' => 'Operasional',
                'keterangan' => 'Pembayaran Listrik & Internet Indihome Cabang',
                'nominal' => 850000,
                'tanggal' => '28 Okt 2026',
            ],
        ];

        return view('admin.dashboard', compact(
            'viewRole',
            'daftarCabang',
            'currentCabang',
            'masterTarifSpp',
            'biayaPendaftaranBaku',
            'kpi',
            'qrTokenSession',
            'unassignedStudents',
            'teachersSlotStatus',
            'todaySchedules',
            'recentPayments',
            'recentKasTransactions'
        ));
    }
}
