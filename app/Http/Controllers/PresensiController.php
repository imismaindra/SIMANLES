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
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PresensiController extends Controller
{
    /**
     * Halaman Presensi Guru, Layar QR Code Dinamis & Riwayat Kehadiran.
     */
    public function index(Request $request, CabangContextService $contextService, PresensiService $presensiService): View
    {
        $context = $contextService->getContext($request);
        $cabangId = $context['cabangId'];
        $today = Carbon::today()->toDateString();
        $filterTanggal = $request->query('tanggal', $today);

        // Ambil atau generate QR Token aktif (default mode: auto)
        $activeQr = QrToken::where('cabang_id', $cabangId)
            ->where('expires_at', '>', Carbon::now())
            ->latest('created_at')
            ->first();

        if (! $activeQr) {
            $activeQr = $presensiService->generateQrToken($cabangId, 'auto', 60);
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
            ->whereDate('tanggal', $today)
            ->orderBy('jam_datang', 'desc')
            ->get();

        // Riwayat Absensi (Bisa difilter tanggal, atau 14 hari terakhir)
        $riwayatQuery = Absensi::with('guru')
            ->where('cabang_id', $cabangId);

        if ($request->filled('guru_id')) {
            $riwayatQuery->where('guru_id', $request->query('guru_id'));
        }

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $riwayatQuery->whereBetween('tanggal', [$request->query('tanggal_dari'), $request->query('tanggal_sampai')]);
        } elseif ($request->filled('tanggal')) {
            $riwayatQuery->whereDate('tanggal', $request->query('tanggal'));
        } else {
            $riwayatQuery->whereDate('tanggal', '<', $today)
                ->orderBy('tanggal', 'desc');
        }

        $riwayatAbsensi = $riwayatQuery->orderBy('tanggal', 'desc')
            ->orderBy('jam_datang', 'desc')
            ->take(30)
            ->get();

        // Statistik Kehadiran Terintegrasi Jadwal
        $summary = $presensiService->getHariIniSummary($cabangId);
        $totalGuruBertugas = $summary['totalGuruBertugas'];
        $totalGuruCabang = $summary['totalGuruCabang'];
        $totalGuruTerjadwal = $summary['totalGuruTerjadwal'];
        $sudahDatang = $summary['sudahDatang'];
        $sudahPulang = $summary['sudahPulang'];
        $sedangMengajar = $summary['sedangMengajar'];
        $izinSakit = $summary['izinSakit'];
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
            'totalGuruCabang',
            'totalGuruTerjadwal',
            'sudahDatang',
            'sudahPulang',
            'sedangMengajar',
            'izinSakit',
            'belumHadir',
            'persentaseKehadiran',
            'today',
            'filterTanggal',
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
            ->whereDate('tanggal', $today)
            ->orderBy('jam_datang', 'desc')
            ->get()
            ->map(function ($item) {
                $diffMinutes = null;
                if ($item->jam_datang && $item->jam_pulang) {
                    $diffMinutes = (int) $item->jam_datang->diffInMinutes($item->jam_pulang);
                }

                $statusLabel = 'Hadir';
                if ($item->status && $item->status !== 'hadir') {
                    $statusLabel = ucfirst($item->status);
                } elseif ($item->jam_pulang) {
                    $statusLabel = 'Selesai';
                } elseif ($item->jam_datang) {
                    $statusLabel = 'Mengajar';
                }

                return [
                    'id' => $item->id,
                    'guru_nama' => $item->guru->nama ?? 'Guru',
                    'no_pegawai' => $item->guru->no_pegawai ?? '-',
                    'jam_datang' => $item->jam_datang ? $item->jam_datang->format('H:i:s') : null,
                    'jam_pulang' => $item->jam_pulang ? $item->jam_pulang->format('H:i:s') : null,
                    'durasi_menit' => $diffMinutes,
                    'status' => $statusLabel,
                    'raw_status' => $item->status ?? 'hadir',
                    'metode' => $item->metode ?? 'qr',
                    'keterangan' => $item->keterangan,
                ];
            });

        // Scan paling baru dalam 15 detik terakhir untuk notifikasi popup
        $latestScan = Absensi::with('guru')
            ->where('cabang_id', $cabangId)
            ->whereDate('tanggal', $today)
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
            'tipe' => 'nullable|string|in:auto,datang,pulang',
        ]);

        try {
            $result = $service->scan($validated['token'], $validated['guru_id'], $validated['tipe'] ?? null);

            return back()->with('success', "Presensi {$result['tipe']} berhasil dicatat untuk {$result['guru_nama']} pukul {$result['jam']} WIB.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->withErrors(['token' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Simpan presensi manual oleh admin (Hadir/Izin/Sakit/Alpa).
     */
    public function storeManual(Request $request, PresensiService $service, CabangContextService $contextService): RedirectResponse
    {
        $context = $contextService->getContext($request);
        $cabangId = $context['cabangId'];

        $validated = $request->validate([
            'guru_id' => 'required|string|exists:guru,id',
            'tanggal' => 'required|date',
            'status' => 'required|string|in:hadir,izin,sakit,alpa',
            'jam_datang' => 'nullable|date_format:H:i',
            'jam_pulang' => 'nullable|date_format:H:i',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            $guru = Guru::findOrFail($validated['guru_id']);
            $service->recordManual(
                cabangId: $cabangId,
                guruId: $validated['guru_id'],
                tanggal: $validated['tanggal'],
                status: $validated['status'],
                jamDatang: $validated['jam_datang'] ?? null,
                jamPulang: $validated['jam_pulang'] ?? null,
                keterangan: $validated['keterangan'] ?? null
            );

            return back()->with('success', "Presensi manual ({$validated['status']}) untuk {$guru->nama} berhasil disimpan.");
        } catch (Exception $e) {
            return back()->withErrors(['manual' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Generate QR Token dinamis berumur pendek (default 60 detik).
     */
    public function generateQr(Request $request, PresensiService $service): JsonResponse
    {
        $validated = $request->validate([
            'cabang_id' => 'required|string|exists:cabang,id',
            'tipe' => 'nullable|string|in:auto,datang,pulang',
            'duration' => 'nullable|integer|min:10|max:180',
        ]);

        $tipe = $validated['tipe'] ?? 'auto';
        $duration = (int) ($validated['duration'] ?? 60);

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
            'tipe' => 'nullable|string|in:auto,datang,pulang',
        ]);

        try {
            $result = $service->scan($validated['token'], $validated['guru_id'], $validated['tipe'] ?? null);

            return response()->json([
                'success' => true,
                'message' => "Presensi {$result['tipe']} berhasil dicatat untuk {$result['guru_nama']} pukul {$result['jam']} WIB.",
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
     * API cek status presensi guru hari ini.
     */
    public function guruStatus(Request $request, PresensiService $service, CabangContextService $contextService): JsonResponse
    {
        $context = $contextService->getContext($request);
        $cabangId = $context['cabangId'];
        $guruId = $request->query('guru_id');

        if (! $guruId) {
            return response()->json(['success' => false, 'message' => 'Guru ID required'], 400);
        }

        $status = $service->getGuruTodayStatus($guruId, $cabangId);

        return response()->json([
            'success' => true,
            'data' => $status,
        ]);
    }

    /**
     * Helper render SVG QR Code.
     */
    private function renderQrSvg(string $content, int $size = 220): string
    {
        $svg = (string) QrCode::size($size)
            ->color(15, 23, 42)
            ->backgroundColor(255, 255, 255)
            ->margin(1)
            ->generate($content);

        // The XML prolog is invalid when the SVG is embedded inline in HTML.
        return (string) preg_replace('/^<\?xml[^>]*\?>\s*/', '', $svg);
    }
}
