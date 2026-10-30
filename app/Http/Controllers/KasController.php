<?php

namespace App\Http\Controllers;

use App\Services\KasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KasController extends Controller
{
    /**
     * Catat mutasi kas masuk / keluar di cabang.
     */
    public function store(Request $request, KasService $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'cabang_id' => 'required|string|exists:cabang,id',
            'tipe' => 'required|in:masuk,keluar',
            'kategori' => 'required|string|max:80',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
            'tanggal' => 'nullable|date',
        ]);

        $transaksi = $service->catat(
            $validated['cabang_id'],
            $validated['tipe'],
            $validated['kategori'],
            (float) $validated['nominal'],
            $validated['keterangan'],
            $validated['tanggal'] ?? null
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi buku kas berhasil dicatat.',
                'data' => $transaksi,
            ], 201);
        }

        return back()->with('success', 'Transaksi kas berhasil dicatat.');
    }

    /**
     * Rekapitulasi Laba Rugi Cabang.
     */
    public function rekap(Request $request, KasService $service): JsonResponse
    {
        $cabangId = $request->query('cabang_id');
        $bulan = $request->query('bulan') ? (int) $request->query('bulan') : null;
        $tahun = $request->query('tahun') ? (int) $request->query('tahun') : null;

        if (! $cabangId) {
            return response()->json(['error' => 'cabang_id parameter wajib diisi.'], 400);
        }

        $rekap = $service->getRekapLabaRugi($cabangId, $bulan, $tahun);

        return response()->json([
            'success' => true,
            'data' => $rekap,
        ]);
    }
}

