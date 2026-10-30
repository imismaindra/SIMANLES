<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\Setting;
use App\Models\Siswa;
use App\Services\CabangContextService;
use App\Services\SiswaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * Halaman Data & Manajemen Siswa.
     */
    public function index(Request $request, CabangContextService $contextService): View
    {
        $context = $contextService->getContext($request);
        $cabangId = $context['cabangId'];

        $query = Siswa::with(['guru', 'cabang']);

        // Filter Cabang
        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->query('cabang_id'));
        } elseif ($context['viewRole'] !== 'pusat') {
            $query->where('cabang_id', $cabangId);
        }

        // Filter Pencarian
        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nama_ibu', 'like', "%{$search}%")
                    ->orWhere('no_telp_ortu', 'like', "%{$search}%");
            });
        }

        // Filter Jenjang
        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->query('jenjang'));
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // Filter Guru
        if ($request->filled('guru_filter')) {
            if ($request->query('guru_filter') === 'unassigned') {
                $query->whereNull('guru_id');
            } elseif ($request->query('guru_filter') === 'assigned') {
                $query->whereNotNull('guru_id');
            }
        }

        $siswaList = $query->orderBy('nama', 'asc')->paginate(15)->withQueryString();

        // Rekapitulasi Statistik
        $baseStatQuery = Siswa::query();
        if ($request->filled('cabang_id')) {
            $baseStatQuery->where('cabang_id', $request->query('cabang_id'));
        } elseif ($context['viewRole'] !== 'pusat') {
            $baseStatQuery->where('cabang_id', $cabangId);
        }

        $totalSiswa = (clone $baseStatQuery)->count();
        $totalAktif = (clone $baseStatQuery)->where('status', 'aktif')->count();
        $totalBelumAdaGuru = (clone $baseStatQuery)->where('status', 'aktif')->whereNull('guru_id')->count();
        $totalCuti = (clone $baseStatQuery)->where('status', 'cuti')->count();
        $totalKeluar = (clone $baseStatQuery)->where('status', 'keluar')->count();

        // Breakdown per jenjang
        $countTk = (clone $baseStatQuery)->where('status', 'aktif')->where('jenjang', 'TK')->count();
        $countSd = (clone $baseStatQuery)->where('status', 'aktif')->where('jenjang', 'SD')->count();
        $countSmp = (clone $baseStatQuery)->where('status', 'aktif')->where('jenjang', 'SMP')->count();
        $countSma = (clone $baseStatQuery)->where('status', 'aktif')->where('jenjang', 'SMA_SMK')->count();

        // Daftar Guru Cabang untuk dropdown penugasan / form
        $daftarGuru = Guru::withCount(['siswa' => fn ($q) => $q->where('status', 'aktif')])
            ->whereHas('cabang', fn ($q) => $q->where('cabang.id', $cabangId))
            ->get();

        $biayaPendaftaranBaku = (float) Setting::get('biaya_pendaftaran', '100000');
        $currentCabang = $context['currentCabang'];

        return view('admin.siswa.index', compact(
            'siswaList',
            'totalSiswa',
            'totalAktif',
            'totalBelumAdaGuru',
            'totalCuti',
            'totalKeluar',
            'countTk',
            'countSd',
            'countSmp',
            'countSma',
            'daftarGuru',
            'biayaPendaftaranBaku',
            'currentCabang',
            'context'
        ));
    }

    /**
     * Daftarkan siswa baru.
     */
    public function store(Request $request, SiswaService $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'cabang_id' => 'nullable|string|exists:cabang,id',
            'nama' => 'required|string|max:150',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'nama_ibu' => 'nullable|string|max:150',
            'no_telp_ortu' => 'nullable|string|max:30',
            'jenjang' => 'nullable|in:TK,SD,SMP,SMA_SMK',
            'kelas' => 'required|integer|min:0|max:12',
            'diskon' => 'nullable|numeric|min:0',
            'guru_id' => 'nullable|string|exists:guru,id',
        ]);

        $siswa = $service->daftar($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil didaftarkan.',
                'data' => $siswa,
            ], 201);
        }

        return back()->with('success', "Siswa {$siswa->nama} berhasil didaftarkan.");
    }

    /**
     * Perbarui status siswa (aktif, cuti, keluar).
     */
    public function updateStatus(Request $request, string $id, SiswaService $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:aktif,cuti,keluar',
        ]);

        $siswa = $service->updateStatus($id, $validated['status']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Status siswa berhasil diubah menjadi {$siswa->status}.",
                'data' => $siswa,
            ]);
        }

        return back()->with('success', "Status siswa {$siswa->nama} diubah menjadi {$siswa->status}.");
    }

    /**
     * Kenaikan kelas massal akhir tahun ajaran.
     */
    public function kenaikanKelas(Request $request, SiswaService $service): JsonResponse|RedirectResponse
    {
        $cabangId = $request->input('cabang_id');
        $result = $service->kenaikanKelasMassal($cabangId);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Kenaikan kelas selesai: {$result['promoted']} naik kelas, {$result['graduated']} lulus.",
                'data' => $result,
            ]);
        }

        return back()->with('success', "Kenaikan kelas massal berhasil ({$result['promoted']} siswa naik, {$result['graduated']} lulus).");
    }

    /**
     * Soft delete siswa.
     */
    public function destroy(Request $request, string $id, SiswaService $service): JsonResponse|RedirectResponse
    {
        $service->softDelete($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil dihapus (soft delete).',
            ]);
        }

        return back()->with('success', 'Data siswa berhasil dihapus.');
    }
}
