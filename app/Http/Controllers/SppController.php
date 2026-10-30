<?php

namespace App\Http\Controllers;

use App\Services\SppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SppController extends Controller
{
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
