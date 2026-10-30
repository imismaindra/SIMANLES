<?php

namespace App\Http\Controllers;

use App\Services\GuruService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    /**
     * Tugaskan siswa ke guru (dengan proteksi kapasitas 6 siswa dan kualifikasi jenjang).
     */
    public function assign(Request $request, GuruService $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'guru_id' => 'required|string|exists:guru,id',
            'siswa_id' => 'required|string|exists:siswa,id',
        ]);

        try {
            $siswa = $service->assignSiswa($validated['guru_id'], $validated['siswa_id']);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Siswa berhasil ditugaskan ke guru.',
                    'data' => $siswa,
                ]);
            }

            return back()->with('success', 'Siswa berhasil ditugaskan ke guru.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Lepas siswa dari guru.
     */
    public function unassign(Request $request, string $siswaId, GuruService $service): JsonResponse|RedirectResponse
    {
        $siswa = $service->unassignSiswa($siswaId);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bimbingan siswa berhasil dilepas.',
                'data' => $siswa,
            ]);
        }

        return back()->with('success', 'Bimbingan siswa berhasil dilepas.');
    }

    /**
     * Batch assign sejumlah siswa ke satu guru.
     */
    public function batchAssign(Request $request, GuruService $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'guru_id' => 'required|string|exists:guru,id',
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'string|exists:siswa,id',
        ]);

        $result = $service->batchAssign($validated['guru_id'], $validated['siswa_ids']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        }

        return back()->with('success', "{$result['assigned']} siswa berhasil ditugaskan.");
    }

    /**
     * Algoritma 1-Klik Auto-Assign.
     */
    public function autoAssign(Request $request, GuruService $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'cabang_id' => 'required|string|exists:cabang,id',
        ]);

        $result = $service->autoAssign($validated['cabang_id']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Auto-assign selesai: {$result['assigned_count']} siswa dialokasikan, {$result['unassigned_count']} belum dialokasikan.",
                'data' => $result,
            ]);
        }

        return back()->with('success', "Auto-assign berhasil! {$result['assigned_count']} siswa berhasil dialokasikan ke guru.");
    }
}

