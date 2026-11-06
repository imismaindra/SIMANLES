<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\PembayaranSpp;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\TarifSpp;
use App\Models\Transaksi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SiswaService
{
    /**
     * Daftarkan siswa baru dengan kalkulasi biaya otomatis,
     * pencatatan jurnal kas masuk pendaftaran, dan penerbitan tagihan SPP bulan berjalan.
     *
     * @param  array<string, mixed>  $data
     */
    public function daftar(array $data): Siswa
    {
        $cabangId = $data['cabang_id'] ?? null;
        if (! $cabangId) {
            $cabangId = Cabang::where('status', 'aktif')->value('id');
        }

        if (! $cabangId) {
            throw ValidationException::withMessages(['cabang_id' => 'Cabang tidak ditemukan atau belum ada cabang aktif.']);
        }

        $kelas = (int) ($data['kelas'] ?? 0);
        $jenjang = $data['jenjang'] ?? $this->resolveJenjangFromKelas($kelas);

        // Biaya pendaftaran default dari setting (default Rp 100.000)
        $biayaStandard = (float) Setting::get('biaya_pendaftaran', '100000');
        $diskon = min($biayaStandard, max(0.0, (float) ($data['diskon'] ?? 0)));
        $totalBayarPendaftaran = max(0.0, $biayaStandard - $diskon);

        return DB::transaction(function () use ($data, $cabangId, $jenjang, $kelas, $totalBayarPendaftaran) {
            $siswa = Siswa::create([
                'cabang_id' => $cabangId,
                'nama' => $data['nama'],
                'tempat_lahir' => $data['tempat_lahir'] ?? '',
                'tanggal_lahir' => $data['tanggal_lahir'] ?? now()->subYears(10)->toDateString(),
                'alamat' => $data['alamat'] ?? '',
                'nama_ibu' => $data['nama_ibu'] ?? 'Wali Siswa',
                'no_telp_ortu' => $data['no_telp_ortu'] ?? '',
                'jenjang' => $jenjang,
                'kelas' => $kelas,
                'status' => 'aktif',
                'guru_id' => $data['guru_id'] ?? null,
            ]);

            // 1. Catat kas masuk pendaftaran di transaksi cabang
            if ($totalBayarPendaftaran >= 0) {
                Transaksi::create([
                    'cabang_id' => $cabangId,
                    'tipe' => 'masuk',
                    'kategori' => 'Pendaftaran',
                    'nominal' => $totalBayarPendaftaran,
                    'keterangan' => 'Pendaftaran Siswa Baru: '.$siswa->nama,
                    'tanggal' => now()->toDateString(),
                    'referensi_id' => $siswa->id,
                ]);
            }

            // 2. Terbitkan tagihan SPP bulan berjalan
            $now = Carbon::now();
            $nominalSpp = TarifSpp::getRate($jenjang, $kelas);

            PembayaranSpp::firstOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'bulan' => $now->month,
                    'tahun' => $now->year,
                ],
                [
                    'cabang_id' => $cabangId,
                    'nominal' => $nominalSpp,
                    'status' => 'belum_bayar',
                ]
            );

            return $siswa;
        });
    }

    /**
     * Konversi angka kelas ke jenjang pendidikan standar.
     */
    public function resolveJenjangFromKelas(int $kelas): string
    {
        return match (true) {
            $kelas === 0 => 'TK',
            $kelas >= 1 && $kelas <= 6 => 'SD',
            $kelas >= 7 && $kelas <= 9 => 'SMP',
            default => 'SMA_SMK',
        };
    }

    /**
     * Kenaikan kelas massal akhir tahun ajaran.
     * Siswa kelas 12 yang naik otomatis berstatus 'keluar' (lulus).
     *
     * @return array{promoted: int, graduated: int}
     */
    public function kenaikanKelasMassal(?string $cabangId = null): array
    {
        return DB::transaction(function () use ($cabangId) {
            $query = Siswa::where('status', 'aktif');
            if ($cabangId) {
                $query->where('cabang_id', $cabangId);
            }

            $students = $query->get();
            $promoted = 0;
            $graduated = 0;

            foreach ($students as $siswa) {
                if ($siswa->kelas >= 12) {
                    $siswa->update([
                        'status' => 'keluar',
                        'guru_id' => null,
                    ]);
                    $graduated++;
                } else {
                    $newKelas = $siswa->kelas + 1;
                    $newJenjang = $this->resolveJenjangFromKelas($newKelas);
                    $siswa->update([
                        'kelas' => $newKelas,
                        'jenjang' => $newJenjang,
                    ]);
                    $promoted++;
                }
            }

            return [
                'promoted' => $promoted,
                'graduated' => $graduated,
            ];
        });
    }

    /**
     * Update status operasional siswa (aktif, cuti, keluar).
     */
    public function updateStatus(string $siswaId, string $status): Siswa
    {
        return DB::transaction(function () use ($siswaId, $status) {
            $siswa = Siswa::findOrFail($siswaId);

            $payload = ['status' => $status];
            if ($status === 'keluar') {
                $payload['guru_id'] = null;
            }

            $siswa->update($payload);

            return $siswa;
        });
    }

    /**
     * Update profil rincian data siswa.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateProfile(string $siswaId, array $data): Siswa
    {
        return DB::transaction(function () use ($siswaId, $data) {
            $siswa = Siswa::findOrFail($siswaId);

            $kelas = isset($data['kelas']) ? (int) $data['kelas'] : $siswa->kelas;
            $jenjang = $data['jenjang'] ?? $this->resolveJenjangFromKelas($kelas);

            $siswa->update([
                'nama' => $data['nama'] ?? $siswa->nama,
                'jenjang' => $jenjang,
                'kelas' => $kelas,
                'tempat_lahir' => $data['tempat_lahir'] ?? $siswa->tempat_lahir,
                'tanggal_lahir' => $data['tanggal_lahir'] ?? $siswa->tanggal_lahir,
                'alamat' => $data['alamat'] ?? $siswa->alamat,
                'nama_ibu' => $data['nama_ibu'] ?? $siswa->nama_ibu,
                'no_telp_ortu' => $data['no_telp_ortu'] ?? $siswa->no_telp_ortu,
            ]);

            return $siswa;
        });
    }

    /**
     * Soft delete siswa (audit trail kas dan SPP masa lalu tetap utuh).
     */
    public function softDelete(string $siswaId): bool
    {
        $siswa = Siswa::findOrFail($siswaId);

        return (bool) $siswa->delete();
    }
}
