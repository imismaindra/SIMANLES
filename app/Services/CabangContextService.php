<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class CabangContextService
{
    /**
     * Mengambil konteks cabang aktif, daftar seluruh cabang, dan role tampilan.
     *
     * @return array{
     *     viewRole: string,
     *     daftarCabang: list<array<string, mixed>>,
     *     currentCabang: array<string, mixed>,
     *     cabangId: string
     * }
     */
    public function getContext(?Request $request = null): array
    {
        $req = $request ?? request();
        $viewRole = $req->query('role', 'cabang');
        $selectedCabangParam = $req->query('cabang_id', 'cab-ponokawan');

        $cabangs = Cabang::withCount(['siswa' => fn ($q) => $q->where('status', 'aktif')])->get();

        if ($cabangs->isEmpty()) {
            $fallback = [
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

            return [
                'viewRole' => $viewRole,
                'daftarCabang' => [$fallback],
                'currentCabang' => $fallback,
                'cabangId' => 'cab-ponokawan',
            ];
        }

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
        })->values()->all();

        $currentCabang = collect($daftarCabang)->firstWhere('id', $cabangId) ?? $daftarCabang[0];

        return [
            'viewRole' => $viewRole,
            'daftarCabang' => $daftarCabang,
            'currentCabang' => $currentCabang,
            'cabangId' => $cabangId,
        ];
    }
}

