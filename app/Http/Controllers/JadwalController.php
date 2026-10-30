<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Services\JadwalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JadwalController extends Controller
{
    /**
     * Buat jadwal baru dengan validasi bentrok lintas cabang.
     */
    public function store(Request $request, JadwalService $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'guru_id' => 'required|string|exists:guru,id',
            'siswa_id' => 'nullable|string|exists:siswa,id',
            'cabang_id' => 'required|string|exists:cabang,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        try {
            $jadwal = $service->createJadwal($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal berhasil ditambahkan tanpa bentrok.',
                    'data' => $jadwal,
                ], 201);
            }

            return back()->with('success', 'Jadwal mengajar berhasil ditambahkan.');
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Update jadwal.
     */
    public function update(Request $request, string $id, JadwalService $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'guru_id' => 'sometimes|string|exists:guru,id',
            'siswa_id' => 'nullable|string|exists:siswa,id',
            'cabang_id' => 'sometimes|string|exists:cabang,id',
            'hari' => 'sometimes|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'sometimes|date_format:H:i',
            'jam_selesai' => 'sometimes|date_format:H:i',
        ]);

        try {
            $jadwal = $service->updateJadwal($id, $validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal berhasil diperbarui.',
                    'data' => $jadwal,
                ]);
            }

            return back()->with('success', 'Jadwal berhasil diperbarui.');
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Hapus jadwal.
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}

