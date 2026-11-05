<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\QrToken;
use App\Services\CabangContextService;
use App\Services\PresensiService;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
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
            $activeQr = $presensiService->generateQrToken($cabangId, 'datang', 25);
        }

        // Generate scan URL & Real SVG QR Code
        $scanUrl = route('presensi.scanner', ['token' => $activeQr->token, 'cabang_id' => $cabangId]);
        $qrSvg = $this->renderQrSvg($scanUrl, 220);

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
        $summary = $presensiService->getHariIniSummary($cabangId);
        $totalGuruBertugas = $summary['totalGuruBertugas'];
        $sudahDatang = $summary['sudahDatang'];
        $sudahPulang = $summary['sudahPulang'];
        $belumHadir = $summary['belumHadir'];
        $persentaseKehadiran = $summary['persentaseKehadiran'];
        $currentCabang = $context['currentCabang'];

        return view('admin.presensi.index', compact(
            'activeQr',
            'qrSvg',
            'scanUrl',
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
     * Halaman Scanner Mandiri untuk Guru (Mobile/Desktop Web).
     */
    public function scannerPage(Request $request, CabangContextService $contextService): View
    {
        $context = $contextService->getContext($request);
        $cabangId = $context['cabangId'];
        $token = $request->query('token');

        $guruList = Guru::whereHas('cabang', fn ($q) => $q->where('cabang.id', $cabangId))
            ->orderBy('nama', 'asc')
            ->get();

        // Jika tidak ada di cabang ini (misal pusat), ambil semua guru aktif
        if ($guruList->isEmpty()) {
            $guruList = Guru::where('is_active', true)->orderBy('nama', 'asc')->get();
        }

        $currentCabang = $context['currentCabang'];

        return view('admin.presensi.scan', compact('token', 'guruList', 'currentCabang', 'context'));
    }

    /**
     * Polling data realtime presensi hari ini untuk layar cabang/kiosk.
     */
    public function liveData(Request $request, PresensiService $service, CabangContextService $contextService): JsonResponse
    {
        $context = $contextService->getContext($request);
        $cabangId = $context['cabangId'];
        $today = Carbon::today()->toDateString();

        $summary = $service->getHariIniSummary($cabangId);

        $absensiHariIni = Absensi::with('guru')
            ->where('cabang_id', $cabangId)
            ->where('tanggal', $today)
            ->orderBy('jam_datang', 'desc')
            ->get()
            ->map(function ($item) {
                $diffMinutes = null;
                if ($item->jam_datang && $item->jam_pulang) {
                    $diffMinutes = (int) $item->jam_datang->diffInMinutes($item->jam_pulang);
                }

                return [
                    'id' => $item->id,
                    'guru_nama' => $item->guru->nama ?? 'Guru',
                    'no_pegawai' => $item->guru->no_pegawai ?? '-',
                    'jam_datang' => $item->jam_datang ? $item->jam_datang->format('H:i:s') : null,
                    'jam_pulang' => $item->jam_pulang ? $item->jam_pulang->format('H:i:s') : null,
                    'durasi_menit' => $diffMinutes,
                    'status' => $item->jam_pulang ? 'Selesai' : ($item->jam_datang ? 'Mengajar' : '-'),
                ];
            });

        // Scan paling baru dalam 15 detik terakhir untuk notifikasi popup
        $latestScan = Absensi::with('guru')
            ->where('cabang_id', $cabangId)
            ->where('tanggal', $today)
            ->where('updated_at', '>=', Carbon::now()->subSeconds(15))
            ->latest('updated_at')
            ->first();

        $latestScanData = null;
        if ($latestScan) {
            $tipe = ($latestScan->jam_pulang && $latestScan->jam_pulang->diffInSeconds(Carbon::now()) < 15) ? 'pulang' : 'datang';
            $latestScanData = [
                'guru_nama' => $latestScan->guru->nama ?? 'Guru',
                'tipe' => $tipe,
                'jam' => $tipe === 'pulang' ? $latestScan->jam_pulang?->format('H:i:s') : $latestScan->jam_datang?->format('H:i:s'),
            ];
        }

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'absensiHariIni' => $absensiHariIni,
            'latestScan' => $latestScanData,
        ]);
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
        $duration = (int) ($validated['duration'] ?? 25);

        $qr = $service->generateQrToken($validated['cabang_id'], $tipe, $duration);
        $scanUrl = route('presensi.scanner', ['token' => $qr->token, 'cabang_id' => $qr->cabang_id]);
        $qrSvg = $this->renderQrSvg($scanUrl, 240);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $qr->token,
                'tipe' => $qr->tipe,
                'expires_at' => $qr->expires_at->toIso8601String(),
                'expires_in_seconds' => $duration,
                'qr_svg' => $qrSvg,
                'scan_url' => $scanUrl,
            ],
        ]);
    }

    /**
     * Proses scan QR token mandiri guru (API).
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

    /**
     * Helper render SVG QR Code.
     */
    private function renderQrSvg(string $content, int $size = 220): string
    {
        $fill = Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(15, 23, 42));
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1, null, null, $fill),
            new SvgImageBackEnd
        );
        $writer = new Writer($renderer);

        $svg = $writer->writeString($content);

        return trim(preg_replace('/<\?xml.*?\?>/i', '', $svg));
    }
}
