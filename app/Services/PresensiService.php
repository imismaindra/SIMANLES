<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Cabang;
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
    public function generateQrToken(string $cabangId, string $tipe = 'datang', int $durationSeconds = 25): QrToken
    {
        return DB::transaction(function () use ($cabangId, $tipe, $durationSeconds) {
            // Bersihkan token kedaluwarsa yang sudah lewat lebih dari 5 menit
            QrToken::where('expires_at', '<', Carbon::now()->subMinutes(5))->delete();

            // Hapus token sebelumnya untuk cabang dan tipe ini
            QrToken::where('cabang_id', $cabangId)
                ->where('tipe', $tipe)
                ->delete();

            $tokenString = 'SIROJU-'.mt_rand(1000, 9999);
            while (QrToken::where('token', $tokenString)->exists()) {
                $tokenString = 'SIROJU-'.mt_rand(1000, 9999);
            }

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
     * 1. Normalisasi token (mendukung format plain SIROJU-XXXX maupun URL scan)
     * 2. Cek validitas & kedaluwarsa token QR
     * 3. Cek apakah guru terdaftar di cabang yang menayangkan QR
     * 4. Catat jam_datang atau jam_pulang hari ini
     *
     * @return array{
     *     ok: bool,
     *     tipe: string,
     *     guru_id: string,
     *     guru_nama: string,
     *     no_pegawai: ?string,
     *     cabang_id: string,
     *     cabang_nama: string,
     *     jam: string,
     *     tanggal: string,
     *     jam_datang: ?string,
     *     jam_pulang: ?string,
     *     durasi_menit: ?int
     * }
     *
     * @throws Exception
     */
    public function scan(string $token, string $guruId): array
    {
        // Normalisasi token: ekstrak token SIROJU-XXXX bila dikirim sebagai URL lengkap
        $rawToken = trim($token);
        if (preg_match('/SIROJU-[\w\d]+/i', $rawToken, $matches)) {
            $normalizedToken = strtoupper($matches[0]);
        } else {
            $normalizedToken = strtoupper($rawToken);
        }

        $qr = QrToken::where('token', $normalizedToken)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $qr) {
            throw ValidationException::withMessages([
                'token' => 'Kode QR telah kedaluwarsa atau tidak valid. Silakan scan kode terbaru pada layar cabang.',
            ]);
        }

        $guru = Guru::with('cabang')->findOrFail($guruId);

        // Verifikasi keanggotaan guru di cabang yang menayangkan QR
        $isMember = DB::table('guru_cabang')
            ->where('guru_id', $guru->id)
            ->where('cabang_id', $qr->cabang_id)
            ->exists();

        if (! $isMember) {
            throw ValidationException::withMessages([
                'guru_id' => "Anda ({$guru->nama}) tidak terdaftar mengajar di cabang ini.",
            ]);
        }

        $cabang = Cabang::find($qr->cabang_id);
        $cabangNama = $cabang ? $cabang->nama : 'Cabang';

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        return DB::transaction(function () use ($guru, $qr, $cabangNama, $today, $now) {
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

            $diffMinutes = null;
            if ($absensi->jam_datang && $absensi->jam_pulang) {
                $diffMinutes = (int) $absensi->jam_datang->diffInMinutes($absensi->jam_pulang);
            }

            return [
                'ok' => true,
                'tipe' => $qr->tipe,
                'guru_id' => $guru->id,
                'guru_nama' => $guru->nama,
                'no_pegawai' => $guru->no_pegawai,
                'cabang_id' => $qr->cabang_id,
                'cabang_nama' => $cabangNama,
                'jam' => $jam,
                'tanggal' => $today,
                'jam_datang' => $absensi->jam_datang ? $absensi->jam_datang->format('H:i:s') : null,
                'jam_pulang' => $absensi->jam_pulang ? $absensi->jam_pulang->format('H:i:s') : null,
                'durasi_menit' => $diffMinutes,
            ];
        });
    }

    /**
     * Hitung ringkasan statistik kehadiran hari ini untuk suatu cabang.
     *
     * @return array{
     *     totalGuruBertugas: int,
     *     sudahDatang: int,
     *     sudahPulang: int,
     *     belumHadir: int,
     *     persentaseKehadiran: float
     * }
     */
    public function getHariIniSummary(string $cabangId): array
    {
        $today = Carbon::today()->toDateString();

        $totalGuruBertugas = Guru::whereHas('cabang', fn ($q) => $q->where('cabang.id', $cabangId))->count();

        $absensiHariIni = Absensi::where('cabang_id', $cabangId)
            ->where('tanggal', $today)
            ->get();

        $sudahDatang = $absensiHariIni->whereNotNull('jam_datang')->count();
        $sudahPulang = $absensiHariIni->whereNotNull('jam_pulang')->count();
        $belumHadir = max(0, $totalGuruBertugas - $sudahDatang);
        $persentaseKehadiran = $totalGuruBertugas > 0 ? round(($sudahDatang / $totalGuruBertugas) * 100, 1) : 0.0;

        return [
            'totalGuruBertugas' => $totalGuruBertugas,
            'sudahDatang' => $sudahDatang,
            'sudahPulang' => $sudahPulang,
            'belumHadir' => $belumHadir,
            'persentaseKehadiran' => $persentaseKehadiran,
        ];
    }
}
