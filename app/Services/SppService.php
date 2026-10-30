<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\PembayaranSpp;
use App\Models\Siswa;
use App\Models\TarifSpp;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class SppService
{
    /**
     * Otomasi pembuatan tagihan SPP bulanan yang bersifat idempoten.
     * Hanya ditujukan untuk siswa aktif (siswa cuti/keluar otomatis dilewati).
     *
     * @return array{created: int, skipped: int}
     */
    public function generateMonthlyBilling(int $bulan, int $tahun, ?string $cabangId = null): array
    {
        $query = Siswa::where('status', 'aktif')->whereNull('deleted_at');
        if ($cabangId) {
            $query->where('cabang_id', $cabangId);
        }

        $students = $query->get();
        $created = 0;
        $skipped = 0;

        foreach ($students as $siswa) {
            // Cek idempotensi
            $existing = PembayaranSpp::where('siswa_id', $siswa->id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->exists();

            if ($existing) {
                $skipped++;

                continue;
            }

            $nominal = TarifSpp::getRate($siswa->jenjang, $siswa->kelas);

            PembayaranSpp::create([
                'siswa_id' => $siswa->id,
                'cabang_id' => $siswa->cabang_id,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'nominal' => $nominal,
                'status' => 'belum_bayar',
            ]);

            $created++;
        }

        return [
            'created' => $created,
            'skipped' => $skipped,
        ];
    }

    /**
     * Pelunasan tagihan SPP:
     * 1. Terbitkan nomor kwitansi resmi: KW/{KODE_CABANG}/{TAHUN}/{BULAN_2_DIGIT}/{NOMOR_URUT_4_DIGIT}
     * 2. Catat kas masuk secara otomatis di buku kas cabang
     *
     * @param  array<string, mixed>  $payload
     */
    public function bayar(string $sppId, array $payload = []): PembayaranSpp
    {
        return DB::transaction(function () use ($sppId, $payload) {
            $spp = PembayaranSpp::with(['siswa', 'cabang'])->where('id', $sppId)->lockForUpdate()->firstOrFail();

            if ($spp->status === 'lunas') {
                return $spp;
            }

            $tanggalBayar = $payload['tanggal_bayar'] ?? now()->toDateString();
            $namaPembayar = $payload['nama_pembayar'] ?? ($spp->siswa ? $spp->siswa->nama_ibu : 'Orang Tua Siswa');

            // Generate nomor urut kwitansi resmi
            $kodeCabang = $spp->cabang ? ($spp->cabang->kode ?? 'CAB01') : 'CAB01';
            $bulan2Digit = str_pad((string) $spp->bulan, 2, '0', STR_PAD_LEFT);
            $tahunStr = (string) $spp->tahun;

            // Hitung nomor urut kwitansi di cabang dan tahun/bulan yang sama
            $lastCount = PembayaranSpp::where('cabang_id', $spp->cabang_id)
                ->where('tahun', $spp->tahun)
                ->where('bulan', $spp->bulan)
                ->whereNotNull('no_kwitansi')
                ->count();

            $nomorUrut4Digit = str_pad((string) ($lastCount + 1), 4, '0', STR_PAD_LEFT);
            $noKwitansi = "KW/{$kodeCabang}/{$tahunStr}/{$bulan2Digit}/{$nomorUrut4Digit}";

            $spp->update([
                'status' => 'lunas',
                'nama_pembayar' => $namaPembayar,
                'tanggal_bayar' => $tanggalBayar,
                'no_kwitansi' => $noKwitansi,
            ]);

            // Catat pemasukan ke kas cabang
            $namaSiswa = $spp->siswa ? $spp->siswa->nama : 'Siswa';
            Transaksi::create([
                'cabang_id' => $spp->cabang_id,
                'tipe' => 'masuk',
                'kategori' => 'SPP',
                'nominal' => $spp->nominal,
                'keterangan' => "Pelunasan SPP {$spp->nama_bulan} {$spp->tahun} an. {$namaSiswa} (Kwitansi: {$noKwitansi})",
                'tanggal' => $tanggalBayar,
                'referensi_id' => $spp->id,
            ]);

            return $spp->fresh(['siswa', 'cabang']);
        });
    }

    /**
     * Tandai bahwa kwitansi telah dikirimkan via WhatsApp orang tua.
     */
    public function markSent(string $sppId): PembayaranSpp
    {
        $spp = PembayaranSpp::findOrFail($sppId);
        $spp->update(['dikirim_at' => now()]);

        return $spp;
    }
}
