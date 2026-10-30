<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\QrToken;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PresensiService
{
    /**
     * Generate token QR dinamis berumur pendek (15 - 30 detik) untuk sesi datang atau pulang.
     * Token baru secara otomatis menggantikan token lama cabang tersebut.
     */
    public function generateQrToken(string $cabangId, string $tipe = 'datang', int $durationSeconds = 20): QrToken
    {
        return DB::transaction(function () use ($cabangId, $tipe, $durationSeconds) {
            // Hapus token sebelumnya untuk cabang dan tipe ini
            QrToken::where('cabang_id', $cabangId)
                ->where('tipe', $tipe)
                ->delete();

            $tokenString = 'SIROJU-'.mt_rand(1000, 9999);

            return QrToken::create([
                'token' => $tokenString,
                'cabang_id' => $cabangId,
                'tipe' => $tipe,
                'expires_at' => Carbon::now()->addSeconds($durationSeconds),
            ]);
        });
    }

    /**
     * Proses scan QR presensi mandiri oleh guru:
     * 1. Cek validitas & kedaluwarsa token QR
     * 2. Cek apakah guru terdaftar di cabang yang menayangkan QR
     * 3. Catat jam_datang atau jam_pulang hari ini
     *
     * @return array{ok: bool, tipe: string, guru_nama: string, jam: string}
     *
     * @throws Exception
     */
    public function scan(string $token, string $guruId): array
    {
        $qr = QrToken::where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $qr) {
            throw ValidationException::withMessages([
                'token' => 'Kode QR telah kedaluwarsa atau tidak valid. Silakan scan kode terbaru pada layar cabang.',
            ]);
        }

        $guru = Guru::findOrFail($guruId);

        // Verifikasi keanggotaan guru di cabang
        $isMember = DB::table('guru_cabang')
            ->where('guru_id', $guru->id)
            ->where('cabang_id', $qr->cabang_id)
            ->exists();

        if (! $isMember) {
            throw ValidationException::withMessages([
                'guru_id' => "Anda ({$guru->nama}) tidak terdaftar mengajar di cabang ini.",
            ]);
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        return DB::transaction(function () use ($guru, $qr, $today, $now) {
            $absensi = Absensi::where('guru_id', $guru->id)
                ->where('tanggal', $today)
                ->lockForUpdate()
                ->first();

            if ($qr->tipe === 'datang') {
                if ($absensi && $absensi->jam_datang) {
                    $jamDatang = $absensi->jam_datang->format('H:i');
                    throw new Exception("Anda sudah melakukan absensi datang hari ini pada pukul {$jamDatang} WIB.");
                }

                if (! $absensi) {
                    $absensi = Absensi::create([
                        'guru_id' => $guru->id,
                        'user_id' => $guru->user_id,
                        'cabang_id' => $qr->cabang_id,
                        'tanggal' => $today,
                        'jam_datang' => $now,
                    ]);
                } else {
                    $absensi->update(['jam_datang' => $now]);
                }

                $jam = $now->format('H:i:s');
            } else {
                // Sesi Pulang
                if (! $absensi || ! $absensi->jam_datang) {
                    throw new Exception('Anda belum melakukan absensi datang hari ini, tidak dapat melakukan absensi pulang.');
                }

                if ($absensi->jam_pulang) {
                    $jamPulang = $absensi->jam_pulang->format('H:i');
                    throw new Exception("Anda sudah melakukan absensi pulang hari ini pada pukul {$jamPulang} WIB.");
                }

                $absensi->update(['jam_pulang' => $now]);
                $jam = $now->format('H:i:s');
            }

            return [
                'ok' => true,
                'tipe' => $qr->tipe,
                'guru_nama' => $guru->nama,
                'jam' => $jam,
            ];
        });
    }
}
