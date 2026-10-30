<?php

namespace App\Http\Controllers;

use App\Models\PembayaranSpp;
use App\Models\Siswa;
use App\Services\CabangContextService;
use App\Services\SppService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SppController extends Controller
{
    /**
     * Halaman Tagihan & Pembayaran SPP Siswa.
     */
    public function index(Request $request, CabangContextService $contextService): View
    {
        $context = $contextService->getContext($request);
        $cabangId = $context['cabangId'];

        $bulanSekarang = Carbon::now()->month;
        $tahunSekarang = Carbon::now()->year;

        $selectedBulan = (int) $request->query('bulan', $bulanSekarang);
        $selectedTahun = (int) $request->query('tahun', $tahunSekarang);
        $selectedStatus = $request->query('status', 'semua');

        $query = PembayaranSpp::with(['siswa', 'cabang'])
            ->where('bulan', $selectedBulan)
            ->where('tahun', $selectedTahun);

        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->query('cabang_id'));
        } elseif ($context['viewRole'] !== 'pusat') {
            $query->where('cabang_id', $cabangId);
        }

        if ($selectedStatus !== 'semua' && in_array($selectedStatus, ['belum_bayar', 'lunas'])) {
            $query->where('status', $selectedStatus);
        }

        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nama_ibu', 'like', "%{$search}%");
            });
        }

        $sppList = $query->latest('id')->paginate(15)->withQueryString();

        // Statistik Periode Ini
        $baseStatQuery = PembayaranSpp::where('bulan', $selectedBulan)
            ->where('tahun', $selectedTahun);

        if ($request->filled('cabang_id')) {
            $baseStatQuery->where('cabang_id', $request->query('cabang_id'));
        } elseif ($context['viewRole'] !== 'pusat') {
            $baseStatQuery->where('cabang_id', $cabangId);
        }

        $totalTagihan = (clone $baseStatQuery)->count();
        $totalLunasCount = (clone $baseStatQuery)->where('status', 'lunas')->count();
        $totalBelumBayarCount = (clone $baseStatQuery)->where('status', 'belum_bayar')->count();

        $nominalLunas = (float) (clone $baseStatQuery)->where('status', 'lunas')->sum('nominal');
        $nominalBelumBayar = (float) (clone $baseStatQuery)->where('status', 'belum_bayar')->sum('nominal');
        $nominalTotal = $nominalLunas + $nominalBelumBayar;

        $persentaseLunas = $nominalTotal > 0 ? round(($nominalLunas / $nominalTotal) * 100, 1) : 0;

        $namaBulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $currentCabang = $context['currentCabang'];

        return view('admin.spp.index', compact(
            'sppList',
            'selectedBulan',
            'selectedTahun',
            'selectedStatus',
            'totalTagihan',
            'totalLunasCount',
            'totalBelumBayarCount',
            'nominalLunas',
            'nominalBelumBayar',
            'nominalTotal',
            'persentaseLunas',
            'namaBulanList',
            'currentCabang',
            'context'
        ));
    }

    /**
     * Tampilan Slip Kwitansi Resmi Siap Cetak.
     */
    public function kwitansi(string $id): View
    {
        $spp = PembayaranSpp::with(['siswa', 'cabang'])->findOrFail($id);

        return view('admin.spp.kwitansi', compact('spp'));
    }

    /**
     * Generate tagihan SPP bulanan idempoten.
     */
    public function generateBilling(Request $request, SppService $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2040',
            'cabang_id' => 'nullable|string|exists:cabang,id',
        ]);

        $result = $service->generateMonthlyBilling(
            (int) $validated['bulan'],
            (int) $validated['tahun'],
            $validated['cabang_id'] ?? null
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Tagihan SPP: {$result['created']} dibuat, {$result['skipped']} dilewati (sudah ada).",
                'data' => $result,
            ]);
        }

        return back()->with('success', "Generate tagihan SPP selesai ({$result['created']} dibuat, {$result['skipped']} dilewati).");
    }

    /**
     * Pelunasan SPP & penerbitan nomor kwitansi resmi.
     */
    public function bayar(Request $request, string $id, SppService $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'nama_pembayar' => 'nullable|string|max:150',
            'tanggal_bayar' => 'nullable|date',
        ]);

        $spp = $service->bayar($id, $validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pelunasan SPP berhasil. Nomor Kwitansi: {$spp->no_kwitansi}",
                'data' => [
                    'spp' => $spp,
                    'no_kwitansi' => $spp->no_kwitansi,
                    'wa_link' => $spp->generateWhatsAppLink(),
                ],
            ]);
        }

        return back()->with('success', "Pelunasan SPP berhasil. Kwitansi: {$spp->no_kwitansi}");
    }

    /**
     * Tandai kwitansi telah dikirimkan via WhatsApp.
     */
    public function markSent(Request $request, string $id, SppService $service): JsonResponse|RedirectResponse
    {
        $spp = $service->markSent($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pengiriman kwitansi WhatsApp dicatat.',
                'data' => $spp,
            ]);
        }

        return back()->with('success', 'Status pengiriman kwitansi WhatsApp dicatat.');
    }
}
