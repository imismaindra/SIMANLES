<?php

namespace App\Http\Controllers;

use App\Services\PresensiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PresensiController extends Controller
{
    /**
     * Generate QR Token dinamis berumur pendek (15-30 detik).
     */
    public function generateQr(Request $request, PresensiService $service): JsonResponse
    {
        $validated = $request->validate([
            'cabang_id' => 'required|string|exists:cabang,id',
            'tipe' => 'nullable|in:datang,pulang',
            'duration' => 'nullable|integer|min:5|max:60',
        ]);

        $tipe = $validated['tipe'] ?? 'datang';
        $duration = (int) ($validated['duration'] ?? 20);

        $qr = $service->generateQrToken($validated['cabang_id'], $tipe, $duration);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $qr->token,
                'tipe' => $qr->tipe,
                'expires_at' => $qr->expires_at->toIso8601String(),
                'expires_in_seconds' => $duration,
            ],
        ]);
    }

    /**
     * Proses scan QR token mandiri guru.
     */
    public function scan(Request $request, PresensiService $service): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'guru_id' => 'required|string|exists:guru,id',
        ]);

        try {
            $result = $service->scan($validated['token'], $validated['guru_id']);

            return response()->json([
                'success' => true,
                'message' => "Absensi {$result['tipe']} berhasil dicatat untuk {$result['guru_nama']} pukul {$result['jam']} WIB.",
                'data' => $result,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

