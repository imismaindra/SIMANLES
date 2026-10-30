<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Validation\ValidationException;

class JadwalService
{
    /**
     * Buat jadwal mengajar baru dengan validasi bentrok lintas cabang:
     * Guru tidak boleh memiliki sesi yang saling bertabrakan pada hari dan jam yang sama
     * di cabang mana pun.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    public function createJadwal(array $data): Jadwal
    {
        $this->validateConflict(
            $data['guru_id'],
            $data['hari'],
            $data['jam_mulai'],
            $data['jam_selesai'],
            $data['cabang_id'] ?? null
        );

        return Jadwal::create([
            'guru_id' => $data['guru_id'],
            'siswa_id' => $data['siswa_id'] ?? null,
            'cabang_id' => $data['cabang_id'],
            'hari' => $data['hari'],
            'jam_mulai' => $data['jam_mulai'],
            'jam_selesai' => $data['jam_selesai'],
        ]);
    }

    /**
     * Perbarui jadwal dengan validasi anti-bentrok.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    public function updateJadwal(string $id, array $data): Jadwal
    {
        $jadwal = Jadwal::findOrFail($id);

        $guruId = $data['guru_id'] ?? $jadwal->guru_id;
        $hari = $data['hari'] ?? $jadwal->hari;
        $jamMulai = $data['jam_mulai'] ?? $jadwal->jam_mulai;
        $jamSelesai = $data['jam_selesai'] ?? $jadwal->jam_selesai;
        $cabangId = $data['cabang_id'] ?? $jadwal->cabang_id;

        $this->validateConflict($guruId, $hari, $jamMulai, $jamSelesai, $cabangId, $id);

        $jadwal->update([
            'guru_id' => $guruId,
            'siswa_id' => array_key_exists('siswa_id', $data) ? $data['siswa_id'] : $jadwal->siswa_id,
            'cabang_id' => $cabangId,
            'hari' => $hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
        ]);

        return $jadwal;
    }

    /**
     * Deteksi irisan waktu (overlap) jadwal guru di seluruh cabang:
     * Bentrok terjadi jika: (jam_mulai_baru < jam_selesai_lama) AND (jam_selesai_baru > jam_mulai_lama)
     *
     * @throws ValidationException
     */
    public function validateConflict(
        string $guruId,
        string $hari,
        string $jamMulai,
        string $jamSelesai,
        ?string $cabangId = null,
        ?string $ignoreJadwalId = null
    ): void {
        if ($jamMulai >= $jamSelesai) {
            throw ValidationException::withMessages([
                'jam_mulai' => 'Jam mulai harus lebih awal daripada jam selesai.',
            ]);
        }

        $query = Jadwal::with('cabang')
            ->where('guru_id', $guruId)
            ->where('hari', $hari)
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                    ->where('jam_selesai', '>', $jamMulai);
            });

        if ($ignoreJadwalId) {
            $query->where('id', '!=', $ignoreJadwalId);
        }

        $conflict = $query->first();

        if ($conflict) {
            $guru = Guru::find($guruId);
            $guruNama = $guru ? $guru->nama : 'Guru';
            $cabangNama = $conflict->cabang ? $conflict->cabang->nama : 'Cabang lain';
            $jamRange = substr($conflict->jam_mulai, 0, 5).' - '.substr($conflict->jam_selesai, 0, 5);

            throw ValidationException::withMessages([
                'jam_mulai' => "Jadwal bentrok! {$guruNama} sudah memiliki sesi mengajar pada hari {$hari} pukul {$jamRange} di {$cabangNama}.",
            ]);
        }
    }
}
