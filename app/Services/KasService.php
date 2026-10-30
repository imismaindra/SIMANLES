<?php

namespace App\Services;

use App\Models\Transaksi;

class KasService
{
    /**
     * Catat mutasi kas masuk atau keluar.
     */
    public function catat(
        string $cabangId,
        string $tipe,
        string $kategori,
        float $nominal,
        string $keterangan,
        ?string $tanggal = null,
        ?string $referensiId = null
    ): Transaksi {
        return Transaksi::create([
            'cabang_id' => $cabangId,
            'tipe' => $tipe,
            'kategori' => $kategori,
            'nominal' => $nominal,
            'keterangan' => $keterangan,
            'tanggal' => $tanggal ?? now()->toDateString(),
            'referensi_id' => $referensiId,
        ]);
    }

    /**
     * Hitung rekapitulasi laba rugi cabang pada periode tertentu.
     *
     * @return array{total_masuk: float, total_keluar: float, saldo_bersih: float, rincian_kategori: array<string, float>}
     */
    public function getRekapLabaRugi(string $cabangId, ?int $bulan = null, ?int $tahun = null): array
    {
        $query = Transaksi::where('cabang_id', $cabangId);

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }
        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        $transactions = $query->get();

        $totalMasuk = (float) $transactions->where('tipe', 'masuk')->sum('nominal');
        $totalKeluar = (float) $transactions->where('tipe', 'keluar')->sum('nominal');
        $saldoBersih = $totalMasuk - $totalKeluar;

        $rincianKategori = [];
        foreach ($transactions as $t) {
            $k = $t->kategori;
            $rincianKategori[$k] = ($rincianKategori[$k] ?? 0.0) + (float) $t->nominal;
        }

        return [
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'saldo_bersih' => $saldoBersih,
            'rincian_kategori' => $rincianKategori,
        ];
    }
}
