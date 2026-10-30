<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Services\CabangContextService;
use App\Services\KasService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KasController extends Controller
{
    /**
     * Halaman Buku Kas & Keuangan Cabang.
     */
    public function index(Request $request, CabangContextService $contextService, KasService $service): View
    {
        $context = $contextService->getContext($request);
        $cabangId = $context['cabangId'];

        $selectedBulan = $request->filled('bulan') ? (int) $request->query('bulan') : Carbon::now()->month;
        $selectedTahun = $request->filled('tahun') ? (int) $request->query('tahun') : Carbon::now()->year;
        $selectedTipe = $request->query('tipe', 'semua');
        $selectedKategori = $request->query('kategori', 'semua');

        $query = Transaksi::where('cabang_id', $cabangId);

        if ($selectedTahun) {
            $query->whereYear('tanggal', $selectedTahun);
        }
        if ($selectedBulan) {
            $query->whereMonth('tanggal', $selectedBulan);
        }
        if ($selectedTipe !== 'semua' && in_array($selectedTipe, ['masuk', 'keluar'])) {
            $query->where('tipe', $selectedTipe);
        }
        if ($selectedKategori !== 'semua' && ! empty($selectedKategori)) {
            $query->where('kategori', $selectedKategori);
        }

        $transaksiList = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Rekapitulasi Laba Rugi periode ini
        $rekap = $service->getRekapLabaRugi($cabangId, $selectedBulan, $selectedTahun);

        // Kategori-kategori yang sudah ada di database untuk filter
        $kategoriList = Transaksi::where('cabang_id', $cabangId)->pluck('kategori')->unique()->filter()->values()->all();

        $namaBulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('admin.kas.index', compact(
            'transaksiList',
            'rekap',
            'kategoriList',
            'selectedBulan',
            'selectedTahun',
            'selectedTipe',
            'selectedKategori',
            'namaBulanList',
            'context'
        ));
    }

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
