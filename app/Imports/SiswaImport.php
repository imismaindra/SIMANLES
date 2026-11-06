<?php

namespace App\Imports;

use App\Models\Cabang;
use App\Services\AuditService;
use App\Services\SiswaService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;

    public function __construct(
        public string $cabangId
    ) {}

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     */
    public function collection(Collection $rows): void
    {
        $siswaService = app(SiswaService::class);
        $cabang = Cabang::find($this->cabangId);
        $cabangNama = $cabang ? $cabang->nama : $this->cabangId;

        foreach ($rows as $row) {
            $nama = trim((string) ($row['nama'] ?? $row['nama_siswa'] ?? $row['name'] ?? ''));
            if ($nama === '') {
                continue;
            }

            $rawKelas = $row['kelas'] ?? $row['grade'] ?? 0;
            $rawJenjang = (string) ($row['jenjang'] ?? '');

            $parsed = $this->determineJenjangAndKelas($rawKelas, $rawJenjang);

            $siswaService->daftar([
                'cabang_id' => $this->cabangId,
                'nama' => $nama,
                'jenjang' => $parsed['jenjang'],
                'kelas' => $parsed['kelas'],
                'tempat_lahir' => (string) ($row['tempat_lahir'] ?? $row['tempat'] ?? 'Sidoarjo'),
                'tanggal_lahir' => ! empty($row['tanggal_lahir']) ? (string) $row['tanggal_lahir'] : now()->subYears(10)->toDateString(),
                'alamat' => (string) ($row['alamat'] ?? 'Alamat siswa'),
                'nama_ibu' => (string) ($row['nama_ibu'] ?? $row['ibu'] ?? 'Wali Siswa'),
                'no_telp_ortu' => (string) ($row['no_telp_ortu'] ?? $row['no_telp'] ?? $row['telepon'] ?? ''),
                'diskon' => (float) ($row['diskon'] ?? 0),
            ]);

            $this->importedCount++;
        }

        if ($this->importedCount > 0) {
            AuditService::log(
                'Import Siswa Excel',
                "Berhasil mengimpor {$this->importedCount} data siswa ke {$cabangNama}."
            );
        }
    }

    /**
     * Tentukan jenjang & kelas dari input row.
     *
     * @return array{jenjang: string, kelas: int}
     */
    public function determineJenjangAndKelas(mixed $rawKelas, string $rawJenjang = ''): array
    {
        $strKelas = strtolower(trim((string) $rawKelas));
        $strJenjang = strtolower(trim($rawJenjang));

        if ($strKelas === '' || str_contains($strKelas, 'tk') || str_contains($strJenjang, 'tk') || str_contains($strKelas, 'paud')) {
            return ['jenjang' => 'TK', 'kelas' => 0];
        }

        $kNum = (int) preg_replace('/[^0-9]/', '', $strKelas);
        if ($kNum <= 0) {
            if (str_contains($strJenjang, 'smp')) {
                $kNum = 7;
            } elseif (str_contains($strJenjang, 'sma') || str_contains($strJenjang, 'smk')) {
                $kNum = 10;
            } else {
                $kNum = 1;
            }
        }

        if ($kNum === 0) {
            return ['jenjang' => 'TK', 'kelas' => 0];
        }
        if ($kNum <= 6) {
            return ['jenjang' => 'SD', 'kelas' => $kNum];
        }
        if ($kNum <= 9) {
            return ['jenjang' => 'SMP', 'kelas' => $kNum];
        }

        return ['jenjang' => 'SMA_SMK', 'kelas' => min(12, $kNum)];
    }
}
