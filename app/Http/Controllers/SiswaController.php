<?php

namespace App\Http\Controllers;

use App\Services\SiswaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
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

