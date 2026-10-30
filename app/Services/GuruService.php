<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\Siswa;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GuruService
{
    /**
     * Tugaskan siswa ke seorang guru pengajar dengan validasi ketat:
     * 1. Kompatibilitas jenjang pendidikan
     * 2. Batas maksimal 6 siswa aktif per guru (dengan proteksi race-condition)
     *
     * @throws Exception
     */
    public function assignSiswa(string $guruId, string $siswaId): Siswa
    {
        return DB::transaction(function () use ($guruId, $siswaId) {
            $siswa = Siswa::where('id', $siswaId)->lockForUpdate()->firstOrFail();
            $guru = Guru::where('id', $guruId)->lockForUpdate()->firstOrFail();

            // 1. Validasi kecocokan jenjang
            if (! $guru->canTeachJenjang($siswa->jenjang)) {
                $jenjangListStr = implode('/', $guru->jenjang_list);
                throw ValidationException::withMessages([
                    'guru_id' => "Guru {$guru->nama} ({$jenjangListStr}) tidak memiliki kualifikasi untuk membimbing jenjang {$siswa->jenjang}.",
                ]);
            }

            // 2. Proteksi kuota maksimal 6 siswa aktif di seluruh cabang
            $currentActiveCount = Siswa::where('guru_id', $guruId)
                ->where('id', '!=', $siswaId)
                ->where('status', 'aktif')
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->count();

            if ($currentActiveCount >= 6) {
                throw new Exception("Kapasitas guru {$guru->nama} telah penuh (Maksimal 6 siswa aktif).");
            }

            $siswa->update(['guru_id' => $guruId]);

            return $siswa;
        });
    }

    /**
     * Lepas bimbingan siswa dari guru pengajar.
     */
    public function unassignSiswa(string $siswaId): Siswa
    {
        $siswa = Siswa::findOrFail($siswaId);
        $siswa->update(['guru_id' => null]);

        return $siswa;
    }

    /**
     * Batch assign sejumlah siswa ke satu guru.
     *
     * @param  list<string>  $siswaIds
     * @return array{assigned: int, failed: list<string>}
     */
    public function batchAssign(string $guruId, array $siswaIds): array
    {
        $assigned = 0;
        $failed = [];

        foreach ($siswaIds as $id) {
            try {
                $this->assignSiswa($guruId, $id);
                $assigned++;
            } catch (Exception $e) {
                $failed[] = "ID {$id}: ".$e->getMessage();
            }
        }

        return [
            'assigned' => $assigned,
            'failed' => $failed,
        ];
    }

    /**
     * Algoritma Cerdas Auto-Assign (Load-Balancing 1-Klik):
     * 1. Ambil seluruh siswa aktif di cabang yang belum memiliki guru.
     * 2. Urutkan alokasi dari jenjang yang lebih spesifik/langka (SMA_SMK, SMP, TK, SD).
     * 3. Cari guru cabang yang kompeten dan sisa slotnya masih ada (< 6).
     * 4. Alokasikan ke guru dengan beban terendah terlebih dahulu (least-loaded first).
     *
     * @return array{assigned_count: int, unassigned_count: int, assignments: list<array<string, mixed>>}
     */
    public function autoAssign(string $cabangId): array
    {
        return DB::transaction(function () use ($cabangId) {
            // Ambil guru yang terafiliasi dengan cabang ini dan berstatus aktif
            $gurus = Guru::whereHas('cabang', function ($q) use ($cabangId) {
                $q->where('cabang.id', $cabangId);
            })->where('is_active', true)->lockForUpdate()->get();

            // Peta sisa kuota tiap guru
            $slots = [];
            $canTeach = [];

            foreach ($gurus as $g) {
                $activeCount = Siswa::where('guru_id', $g->id)
                    ->where('status', 'aktif')
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->count();

                $slots[$g->id] = $activeCount;
                $canTeach[$g->id] = $g->jenjang_list;
            }

            // Ambil siswa belum berguru di cabang ini
            $unassignedStudents = Siswa::where('cabang_id', $cabangId)
                ->where('status', 'aktif')
                ->whereNull('deleted_at')
                ->whereNull('guru_id')
                ->lockForUpdate()
                ->get();

            $assignedCount = 0;
            $unassignedCount = 0;
            $assignments = [];

            // Urutkan prioritas jenjang: SMA_SMK, SMP, TK, SD
            $jenjangOrder = ['SMA_SMK', 'SMP', 'TK', 'SD'];

            foreach ($jenjangOrder as $targetJenjang) {
                $group = $unassignedStudents->where('jenjang', $targetJenjang);

                foreach ($group as $siswa) {
                    // Cari guru yang memenuhi syarat dan kuotanya < 6, urutkan beban terendah
                    $eligibleGurus = $gurus->filter(function ($g) use ($canTeach, $targetJenjang, $slots) {
                        return in_array($targetJenjang, $canTeach[$g->id], true) && $slots[$g->id] < 6;
                    })->sortBy(function ($g) use ($slots) {
                        return $slots[$g->id];
                    });

                    $bestGuru = $eligibleGurus->first();

                    if ($bestGuru) {
                        $siswa->update(['guru_id' => $bestGuru->id]);
                        $slots[$bestGuru->id]++;
                        $assignedCount++;

                        $assignments[] = [
                            'siswa_id' => $siswa->id,
                            'siswa_nama' => $siswa->nama,
                            'jenjang' => $siswa->jenjang,
                            'guru_id' => $bestGuru->id,
                            'guru_nama' => $bestGuru->nama,
                            'slot_terisi' => $slots[$bestGuru->id],
                        ];
                    } else {
                        $unassignedCount++;
                    }
                }
            }

            return [
                'assigned_count' => $assignedCount,
                'unassigned_count' => $unassignedCount,
                'assignments' => $assignments,
            ];
        });
    }
}
