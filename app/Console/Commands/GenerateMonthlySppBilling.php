<?php

namespace App\Console\Commands;

use App\Services\SppService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateMonthlySppBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spp:generate-monthly {bulan? : Bulan (1-12)} {tahun? : Tahun (contoh: 2026)} {--cabang= : ID Cabang tertentu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomasi pembuatan tagihan SPP bulanan idempoten untuk siswa aktif (Cron tgl 1 00:05)';

    /**
     * Execute the console command.
     */
    public function handle(SppService $sppService): int
    {
        $now = Carbon::now();
        $bulan = (int) ($this->argument('bulan') ?? $now->month);
        $tahun = (int) ($this->argument('tahun') ?? $now->year);
        $cabangId = $this->option('cabang');

        $this->info("Memproses tagihan SPP untuk Bulan {$bulan} / {$tahun}...");

        $result = $sppService->generateMonthlyBilling($bulan, $tahun, $cabangId);

        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Tagihan Dibuat', $result['created']],
                ['Tagihan Dilewati (Sudah Ada)', $result['skipped']],
            ]
        );

        $this->info('Proses pembuatan tagihan SPP bulanan selesai.');

        return Command::SUCCESS;
    }
}

