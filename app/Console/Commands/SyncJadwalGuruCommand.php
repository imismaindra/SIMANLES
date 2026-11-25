<?php

namespace App\Console\Commands;

use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Console\Command;

class SyncJadwalGuruCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:sync-guru';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi jadwal mengajar seluruh guru agar mengajar 5 hari dalam seminggu (Senin - Jumat)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai sinkronisasi jadwal mengajar 5 hari kerja (Senin - Jumat)...');

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $timeSlots = [
            'Bu Divani' => ['jam_mulai' => '13:00:00', 'jam_selesai' => '14:30:00'],
            'Bu Ruli' => ['jam_mulai' => '13:30:00', 'jam_selesai' => '15:00:00'],
            'Bu Nida' => ['jam_mulai' => '14:00:00', 'jam_selesai' => '15:30:00'],
            'Bu Annisa' => ['jam_mulai' => '14:30:00', 'jam_selesai' => '16:00:00'],
            'Bu Salina' => ['jam_mulai' => '15:00:00', 'jam_selesai' => '16:30:00'],
            'Bu Vita' => ['jam_mulai' => '15:30:00', 'jam_selesai' => '17:00:00'],
            'Bu Sofy' => ['jam_mulai' => '16:00:00', 'jam_selesai' => '17:30:00'],
            'Bu Yessi' => ['jam_mulai' => '16:30:00', 'jam_selesai' => '18:00:00'],
            'Bu Ria' => ['jam_mulai' => '17:00:00', 'jam_selesai' => '18:30:00'],
            'Bu Dina' => ['jam_mulai' => '18:00:00', 'jam_selesai' => '19:30:00'],
            'Bu Erika' => ['jam_mulai' => '18:30:00', 'jam_selesai' => '20:00:00'],
            'Bu Sasa' => ['jam_mulai' => '19:00:00', 'jam_selesai' => '20:30:00'],
        ];

        // Hapus jadwal hari Sabtu jika ada
        $deletedSabtu = Jadwal::where('hari', 'Sabtu')->delete();
        if ($deletedSabtu > 0) {
            $this->info("Menghapus {$deletedSabtu} jadwal hari Sabtu...");
        }

        $allGuru = Guru::with(['cabang', 'siswa'])->get();
        $totalCreated = 0;

        foreach ($allGuru as $index => $guru) {
            $slot = $timeSlots[$guru->nama] ?? null;

            // Fallback slot jika nama guru baru tidak ada di daftar predefined
            if (! $slot) {
                $startHour = 13 + ($index % 6);
                $slot = [
                    'jam_mulai' => sprintf('%02d:00:00', $startHour),
                    'jam_selesai' => sprintf('%02d:30:00', $startHour + 1),
                ];
            }

            // Ambil siswa pertama bimbingan guru jika ada untuk ditampilkan di jadwal
            $siswa = $guru->siswa->first();
            $cabangId = $guru->cabang_id ?? $guru->cabang->first()?->id;

            if (! $cabangId) {
                continue;
            }

            foreach ($hariList as $hari) {
                $jadwal = Jadwal::firstOrCreate(
                    [
                        'guru_id' => $guru->id,
                        'cabang_id' => $cabangId,
                        'hari' => $hari,
                    ],
                    [
                        'siswa_id' => $siswa?->id,
                        'jam_mulai' => $slot['jam_mulai'],
                        'jam_selesai' => $slot['jam_selesai'],
                    ]
                );

                if ($jadwal->wasRecentlyCreated) {
                    $totalCreated++;
                } else {
                    // Update jam jika belum seragam
                    $jadwal->update([
                        'siswa_id' => $jadwal->siswa_id ?? $siswa?->id,
                        'jam_mulai' => $slot['jam_mulai'],
                        'jam_selesai' => $slot['jam_selesai'],
                    ]);
                }
            }
        }

        $totalJadwal = Jadwal::count();
        $this->info("Selesai! Berhasil mensinkronkan jadwal mengajar. Total sesi aktif saat ini: {$totalJadwal} sesi (5 hari x seluruh guru).");

        return Command::SUCCESS;
    }
}
