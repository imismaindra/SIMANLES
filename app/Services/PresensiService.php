<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Cabang;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\QrToken;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PresensiService
{
    /**
     * Generate token QR dinamis (default 60 detik) untuk sesi auto, datang, atau pulang.
     * Token baru secara otomatis menggantikan token lama cabang tersebut.
     */
    public function generateQrToken(string $cabangId, string $tipe = 'auto', int $durationSeconds = 60): QrToken
    {
        return DB::transaction(function () use ($cabangId, $tipe, $durationSeconds) {
            $duration = max(10, $durationSeconds);

            // Bersihkan token kedaluwarsa yang sudah lewat lebih dari 10 menit
            QrToken::where('expires_at', '<', Carbon::now()->subMinutes(10))->delete();

            // Hapus token aktif sebelumnya untuk cabang ini agar selalu fresh
            QrToken::where('cabang_id', $cabangId)->delete();

            $tokenString = 'SIROJU-'.mt_rand(1000, 9999);
            while (QrToken::where('token', $tokenString)->exists()) {
                $tokenString = 'SIROJU-'.mt_rand(1000, 9999);
            }

            return QrToken::create([
                'token' => $tokenString,
                'cabang_id' => $cabangId,
                'tipe' => $tipe,
                'expires_at' => Carbon::now()->addSeconds($duration),
            ]);
        });
    }

    /**
     * Proses scan QR presensi mandiri oleh guru:
     * 1. Normalisasi token (mendukung format plain SIROJU-XXXX maupun URL scan)
     * 2. Cek validitas & kedaluwarsa token QR (dengan grace period 45 detik untuk toleransi koneksi HP)
     * 3. Cek apakah guru terdaftar di cabang yang menayangkan QR
     * 4. Smart Auto-Detect: otomatis mencatat jam_datang atau jam_pulang sesuai status hari ini
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
    public function scan(string $token, string $guruId, ?string $requestedTipe = null): array
    {
        // Normalisasi token: ekstrak token SIROJU-XXXX bila dikirim sebagai URL lengkap
        $rawToken = trim($token);
        if (preg_match('/SIROJU-[\w\d]+/i', $rawToken, $matches)) {
            $normalizedToken = strtoupper($matches[0]);
        } else {
            $normalizedToken = strtoupper($rawToken);
        }

        // Grace period 45 detik agar guru yang memuat halaman di HP tidak langsung gagal
        $qr = QrToken::where('token', $normalizedToken)
            ->where('expires_at', '>', Carbon::now()->subSeconds(45))
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

        return DB::transaction(function () use ($guru, $qr, $cabangNama, $today, $now, $requestedTipe) {
            $absensi = Absensi::where('guru_id', $guru->id)
                ->whereDate('tanggal', $today)
                ->lockForUpdate()
                ->first();

            // Tentukan tipe aksi: apakah diminta spesifik atau otomatis
            $effectiveTipe = $requestedTipe;

            if (! $effectiveTipe || $effectiveTipe === 'auto') {
                if ($qr->tipe === 'auto') {
                    // Auto-detect berdasarkan kondisi kehadiran hari ini
                    if (! $absensi || ! $absensi->jam_datang) {
                        $effectiveTipe = 'datang';
                    } elseif (! $absensi->jam_pulang) {
                        // Proteksi debounce minimal 1 menit agar tidak sengaja dobel scan
                        if ($absensi->jam_datang && $absensi->jam_datang->diffInSeconds($now) < 60) {
                            $jamFormat = $absensi->jam_datang->format('H:i');
                            throw new Exception("Anda baru saja melakukan absensi datang pada pukul {$jamFormat} WIB. Silakan lakukan absensi pulang setelah selesai mengajar.");
                        }
                        $effectiveTipe = 'pulang';
                    } else {
                        $datangStr = $absensi->jam_datang ? $absensi->jam_datang->format('H:i') : '-';
                        $pulangStr = $absensi->jam_pulang ? $absensi->jam_pulang->format('H:i') : '-';
                        throw new Exception("Anda sudah menyelesaikan absensi datang ({$datangStr} WIB) dan pulang ({$pulangStr} WIB) untuk hari ini.");
                    }
                } else {
                    $effectiveTipe = $qr->tipe;
                }
            }

            if ($effectiveTipe === 'datang') {
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
                        'status' => 'hadir',
                        'metode' => 'qr',
                    ]);
                } else {
                    $absensi->update([
                        'jam_datang' => $now,
                        'status' => 'hadir',
                        'metode' => 'qr',
                    ]);
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

                $absensi->update([
                    'jam_pulang' => $now,
                ]);
                $jam = $now->format('H:i:s');
            }

            $diffMinutes = null;
            if ($absensi->jam_datang && $absensi->jam_pulang) {
                $diffMinutes = (int) $absensi->jam_datang->diffInMinutes($absensi->jam_pulang);
            }

            return [
                'ok' => true,
                'tipe' => $effectiveTipe,
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
     * Catat kehadiran manual oleh Admin (bisa untuk izin, sakit, alpa, atau input telat).
     */
    public function recordManual(
        string $cabangId,
        string $guruId,
        string $tanggal,
        string $status = 'hadir',
        ?string $jamDatang = null,
        ?string $jamPulang = null,
        ?string $keterangan = null
    ): Absensi {
        $guru = Guru::findOrFail($guruId);

        $absensi = Absensi::where('guru_id', $guruId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        $data = [
            'cabang_id' => $cabangId,
            'guru_id' => $guruId,
            'user_id' => $guru->user_id,
            'tanggal' => $tanggal,
            'status' => $status,
            'keterangan' => $keterangan,
            'metode' => 'manual',
        ];

        if ($status === 'hadir') {
            if ($jamDatang) {
                $data['jam_datang'] = Carbon::parse("{$tanggal} {$jamDatang}");
            } elseif (! $absensi?->jam_datang) {
                $data['jam_datang'] = Carbon::parse("{$tanggal} ".Carbon::now()->format('H:i:s'));
            }

            if ($jamPulang) {
                $data['jam_pulang'] = Carbon::parse("{$tanggal} {$jamPulang}");
            }
        } else {
            // Jika izin, sakit, atau alpa, jam datang/pulang opsional / null
            if ($jamDatang) {
                $data['jam_datang'] = Carbon::parse("{$tanggal} {$jamDatang}");
            }
            if ($jamPulang) {
                $data['jam_pulang'] = Carbon::parse("{$tanggal} {$jamPulang}");
            }
        }

        if (! $absensi) {
            return Absensi::create($data);
        }

        $absensi->update($data);

        return $absensi->fresh();
    }

    /**
     * Hitung ringkasan statistik kehadiran hari ini untuk suatu cabang dengan sinkronisasi Jadwal.
     *
     * @return array{
     *     totalGuruBertugas: int,
     *     totalGuruCabang: int,
     *     totalGuruTerjadwal: int,
     *     sudahDatang: int,
     *     sudahPulang: int,
     *     sedangMengajar: int,
     *     izinSakit: int,
     *     belumHadir: int,
     *     persentaseKehadiran: float,
     *     hariIni: string
     * }
     */
    public function getHariIniSummary(string $cabangId): array
    {
        $today = Carbon::today()->toDateString();

        $dayMap = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
        $todayHari = $dayMap[Carbon::today()->dayOfWeekIso] ?? 'Senin';

        // 1. Guru yang memiliki jadwal mengajar di cabang ini pada hari ini
        $guruTerjadwalIds = Jadwal::where('cabang_id', $cabangId)
            ->where('hari', $todayHari)
            ->pluck('guru_id')
            ->unique()
            ->all();
        $totalGuruTerjadwal = count($guruTerjadwalIds);

        // 2. Total seluruh guru yang teralokasi di cabang ini
        $totalGuruCabang = Guru::whereHas('cabang', fn ($q) => $q->where('cabang.id', $cabangId))->count();

        // Jika ada jadwal terdaftar hari ini, gunakan total jadwal sebagai acuan target hadir
        // Jika belum ada jadwal yang disetting sama sekali, fallback ke total guru cabang
        $totalGuruBertugas = $totalGuruTerjadwal > 0 ? $totalGuruTerjadwal : $totalGuruCabang;

        $absensiHariIni = Absensi::where('cabang_id', $cabangId)
            ->whereDate('tanggal', $today)
            ->get();

        $sudahDatang = $absensiHariIni->whereNotNull('jam_datang')->count();
        $sudahPulang = $absensiHariIni->whereNotNull('jam_pulang')->count();
        $sedangMengajar = max(0, $sudahDatang - $sudahPulang);

        // Guru izin/sakit
        $izinSakit = $absensiHariIni->whereIn('status', ['izin', 'sakit', 'alpa'])->count();

        $belumHadir = max(0, $totalGuruBertugas - $sudahDatang - $izinSakit);
        $persentaseKehadiran = $totalGuruBertugas > 0 ? round(($sudahDatang / $totalGuruBertugas) * 100, 1) : 0.0;

        return [
            'totalGuruBertugas' => $totalGuruBertugas,
            'totalGuruCabang' => $totalGuruCabang,
            'totalGuruTerjadwal' => $totalGuruTerjadwal,
            'sudahDatang' => $sudahDatang,
            'sudahPulang' => $sudahPulang,
            'sedangMengajar' => $sedangMengajar,
            'izinSakit' => $izinSakit,
            'belumHadir' => $belumHadir,
            'persentaseKehadiran' => $persentaseKehadiran,
            'hariIni' => $todayHari,
        ];
    }

    /**
     * Cek status presensi guru hari ini (untuk memudahkan tampilan scan mobile).
     *
     * @return array{
     *     guru_id: string,
     *     guru_nama: string,
     *     has_datang: bool,
     *     has_pulang: bool,
     *     jam_datang: ?string,
     *     jam_pulang: ?string,
     *     status: string,
     *     next_action: string,
     *     status_label: string
     * }
     */
    public function getGuruTodayStatus(string $guruId, string $cabangId): array
    {
        $guru = Guru::findOrFail($guruId);
        $today = Carbon::today()->toDateString();

        $absensi = Absensi::where('guru_id', $guruId)
            ->whereDate('tanggal', $today)
            ->first();

        $hasDatang = ! empty($absensi?->jam_datang);
        $hasPulang = ! empty($absensi?->jam_pulang);

        if (! $hasDatang) {
            $nextAction = 'datang';
            $statusLabel = 'Belum Presensi Datang';
        } elseif (! $hasPulang) {
            $nextAction = 'pulang';
            $statusLabel = 'Sedang Mengajar (Masuk: '.$absensi->jam_datang->format('H:i').' WIB)';
        } else {
            $nextAction = 'selesai';
            $statusLabel = 'Selesai Mengajar (Pulang: '.$absensi->jam_pulang->format('H:i').' WIB)';
        }

        return [
            'guru_id' => $guru->id,
            'guru_nama' => $guru->nama,
            'has_datang' => $hasDatang,
            'has_pulang' => $hasPulang,
            'jam_datang' => $absensi?->jam_datang?->format('H:i:s'),
            'jam_pulang' => $absensi?->jam_pulang?->format('H:i:s'),
            'status' => $absensi?->status ?? 'belum_hadir',
            'next_action' => $nextAction,
            'status_label' => $statusLabel,
        ];
    }
}
