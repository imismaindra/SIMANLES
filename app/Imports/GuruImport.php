<?php

namespace App\Imports;

use App\Models\Cabang;
use App\Services\AuditService;
use App\Services\GuruService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToCollection, WithHeadingRow
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
        $guruService = app(GuruService::class);
        $cabang = Cabang::find($this->cabangId);
        $cabangNama = $cabang ? $cabang->nama : $this->cabangId;

        foreach ($rows as $row) {
            $nama = trim((string) ($row['nama'] ?? $row['nama_guru'] ?? $row['name'] ?? ''));
            if ($nama === '') {
                continue;
            }

            $email = trim((string) ($row['email'] ?? ''));
            if ($email === '') {
                $slug = strtolower(preg_replace('/[^a-z0-9]/i', '', $nama));
                $email = "{$slug}.".mt_rand(100, 999).'@lespintar.id';
            }

            $nip = trim((string) ($row['nip'] ?? $row['no_pegawai'] ?? 'NIP-'.mt_rand(10000, 99999)));
            $noTelp = trim((string) ($row['no_telp'] ?? $row['telp'] ?? $row['telepon'] ?? '08'.mt_rand(100000000, 999999999)));
            $tglLahir = trim((string) ($row['tanggal_lahir'] ?? '1995-05-15'));
            $alamat = trim((string) ($row['alamat'] ?? 'Sidoarjo'));

            $rawKualifikasi = (string) ($row['kualifikasi'] ?? $row['jenjang'] ?? 'SD');
            $kualifikasiList = array_filter(array_map('trim', explode(',', str_replace(['/', ';'], ',', $rawKualifikasi))));
            if (empty($kualifikasiList)) {
                $kualifikasiList = ['SD'];
            }

            // Normalisasi penulisan jenjang
            $normalizedKualifikasi = [];
            foreach ($kualifikasiList as $k) {
                $kUpper = strtoupper($k);
                if (str_contains($kUpper, 'TK') || str_contains($kUpper, 'PAUD')) {
                    $normalizedKualifikasi[] = 'TK';
                } elseif (str_contains($kUpper, 'SMP')) {
                    $normalizedKualifikasi[] = 'SMP';
                } elseif (str_contains($kUpper, 'SMA') || str_contains($kUpper, 'SMK')) {
                    $normalizedKualifikasi[] = 'SMA_SMK';
                } else {
                    $normalizedKualifikasi[] = 'SD';
                }
            }
            $normalizedKualifikasi = array_values(array_unique($normalizedKualifikasi));

            $guruService->createGuru([
                'nama' => $nama,
                'email' => $email,
                'no_pegawai' => $nip,
                'no_telp' => $noTelp,
                'tanggal_lahir' => $tglLahir,
                'alamat' => $alamat,
                'jenjang' => $normalizedKualifikasi[0] ?? 'SD',
                'kualifikasi' => $normalizedKualifikasi,
                'cabang_id' => $this->cabangId,
            ]);

            $this->importedCount++;
        }

        if ($this->importedCount > 0) {
            AuditService::log(
                'Import Guru Excel',
                "Berhasil mengimpor {$this->importedCount} data guru ke {$cabangNama}."
            );
        }
    }
}
