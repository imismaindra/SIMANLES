<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\QrToken;
use App\Services\CabangContextService;
use App\Services\PresensiService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class PresensiController extends Controller
{
    /**
     * Halaman Presensi Guru & Layar QR Code Dinamis.
     */
    public function index(Request $request, CabangContextService $contextService, PresensiService $presensiService): View
    {
        $context = $contextService->getContext($request);
        $cabangId = $context['cabangId'];
        $today = Carbon::today()->toDateString();

        // Ambil atau generate QR Token aktif
        $activeQr = QrToken::where('cabang_id', $cabangId)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if (! $activeQr) {
            $activeQr = $presensiService->generateQrToken($cabangId, 'datang', 30);
        }

        // Daftar Guru di Cabang ini
        $guruCabang = Guru::whereHas('cabang', fn ($q) => $q->where('cabang.id', $cabangId))
            ->orderBy('nama', 'asc')
            ->get();

        // Absensi Hari Ini
        $absensiHariIni = Absensi::with('guru')
            ->where('cabang_id', $cabangId)
            ->where('tanggal', $today)
            ->orderBy('jam_datang', 'desc')
            ->get();

        // Riwayat Absensi 7 Hari Terakhir
        $riwayatAbsensi = Absensi::with('guru')
            ->where('cabang_id', $cabangId)
            ->where('tanggal', '<', $today)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_datang', 'desc')
            ->take(20)
            ->get();

        // Statistik
        $totalGuruBertugas = $guruCabang->count();
        $sudahDatang = $absensiHariIni->whereNotNull('jam_datang')->count();
        $sudahPulang = $absensiHariIni->whereNotNull('jam_pulang')->count();
        $belumHadir = max(0, $totalGuruBertugas - $sudahDatang);
        $persentaseKehadiran = $totalGuruBertugas > 0 ? round(($sudahDatang / $totalGuruBertugas) * 100, 1) : 0;
        $currentCabang = $context['currentCabang'];

        return view('admin.presensi.index', compact(
            'activeQr',
            'guruCabang',
            'absensiHariIni',
            'riwayatAbsensi',
            'totalGuruBertugas',
            'sudahDatang',
            'sudahPulang',
            'belumHadir',
            'persentaseKehadiran',
            'today',
            'currentCabang',
            'context'
        ));
    }

    /**
     * Proses scan QR dari form web admin / simulasi guru.
     */
    public function scanWeb(Request $request, PresensiService $service): RedirectResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'guru_id' => 'required|string|exists:guru,id',
        ]);

        try {
            $result = $service->scan($validated['token'], $validated['guru_id']);

            return back()->with('success', "Absensi {$result['tipe']} berhasil dicatat untuk {$result['guru_nama']} pukul {$result['jam']} WIB.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->withErrors(['token' => $e->getMessage()])->withInput();
        }
    }

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
