<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\PembayaranSpp;
use App\Models\QrToken;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\TarifSpp;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SimanlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Settings
        Setting::set('biaya_pendaftaran', '100000');

        // 2. Cabang
        $cabang1 = Cabang::updateOrCreate(['kode' => 'CAB01'], [
            'nama' => 'Cabang Yogyakarta Diponegoro',
            'alamat' => 'Jl. Pangeran Diponegoro No. 45, Gowongan, Jetis, Kota Yogyakarta',
            'status' => 'aktif',
        ]);

        $cabang2 = Cabang::updateOrCreate(['kode' => 'CAB02'], [
            'nama' => 'Cabang Sleman Kaliurang KM 9',
            'alamat' => 'Jl. Kaliurang KM 9.3, Ngaglik, Kab. Sleman',
            'status' => 'aktif',
        ]);

        $cabang3 = Cabang::updateOrCreate(['kode' => 'CAB03'], [
            'nama' => 'Cabang Bantul Sudirman',
            'alamat' => 'Jl. Jenderal Sudirman No. 12, Nyeparan, Kab. Bantul',
            'status' => 'aktif',
        ]);

        // 3. Tarif SPP Master
        $tarifData = [
            ['jenjang' => 'TK', 'kelas_min' => 0, 'kelas_max' => 0, 'nominal' => 150000.0, 'berlaku_sejak' => '2026-01-01'],
            ['jenjang' => 'SD', 'kelas_min' => 1, 'kelas_max' => 3, 'nominal' => 150000.0, 'berlaku_sejak' => '2026-01-01'],
            ['jenjang' => 'SD', 'kelas_min' => 4, 'kelas_max' => 6, 'nominal' => 175000.0, 'berlaku_sejak' => '2026-01-01'],
            ['jenjang' => 'SMP', 'kelas_min' => 7, 'kelas_max' => 9, 'nominal' => 200000.0, 'berlaku_sejak' => '2026-01-01'],
            ['jenjang' => 'SMA_SMK', 'kelas_min' => 10, 'kelas_max' => 12, 'nominal' => 225000.0, 'berlaku_sejak' => '2026-01-01'],
        ];

        foreach ($tarifData as $t) {
            TarifSpp::updateOrCreate(
                ['jenjang' => $t['jenjang'], 'kelas_min' => $t['kelas_min'], 'kelas_max' => $t['kelas_max']],
                ['nominal' => $t['nominal'], 'berlaku_sejak' => $t['berlaku_sejak']]
            );
        }

        // 4. Guru & Akun User
        $gurusData = [
            [
                'nama' => 'Budi Santoso, S.Pd.',
                'email' => 'budi.santoso@sirojulillah.sch.id',
                'no_telp' => '081234567891',
                'no_pegawai' => 'PEG-2023-001',
                'jenjang_utama' => 'SD',
                'kualifikasi' => ['TK', 'SD'],
                'cabang' => [$cabang1->id, $cabang2->id],
            ],
            [
                'nama' => 'Sarah Az-Zahra, M.Pd.',
                'email' => 'sarah.azzahra@sirojulillah.sch.id',
                'no_telp' => '081234567892',
                'no_pegawai' => 'PEG-2022-014',
                'jenjang_utama' => 'SMP',
                'kualifikasi' => ['SMP', 'SMA_SMK'],
                'cabang' => [$cabang1->id],
            ],
            [
                'nama' => 'Dian Maharani, S.Pd.',
                'email' => 'dian.maharani@sirojulillah.sch.id',
                'no_telp' => '081234567893',
                'no_pegawai' => 'PEG-2024-008',
                'jenjang_utama' => 'SD',
                'kualifikasi' => ['SD', 'SMP'],
                'cabang' => [$cabang1->id, $cabang3->id],
            ],
            [
                'nama' => 'Fadhil Ramadhan, S.Si.',
                'email' => 'fadhil.r@sirojulillah.sch.id',
                'no_telp' => '081234567894',
                'no_pegawai' => 'PEG-2023-022',
                'jenjang_utama' => 'SMA_SMK',
                'kualifikasi' => ['SMA_SMK'],
                'cabang' => [$cabang1->id],
            ],
            [
                'nama' => 'Lestari Handayani, S.Pd.',
                'email' => 'lestari.h@sirojulillah.sch.id',
                'no_telp' => '081234567895',
                'no_pegawai' => 'PEG-2024-019',
                'jenjang_utama' => 'TK',
                'kualifikasi' => ['TK', 'SD'],
                'cabang' => [$cabang1->id],
            ],
            [
                'nama' => 'Ahmad Zakaria, M.Sc.',
                'email' => 'ahmad.zakaria@sirojulillah.sch.id',
                'no_telp' => '081234567896',
                'no_pegawai' => 'PEG-2021-005',
                'jenjang_utama' => 'SMA_SMK',
                'kualifikasi' => ['SMP', 'SMA_SMK'],
                'cabang' => [$cabang1->id],
            ],
        ];

        $createdGurus = [];

        foreach ($gurusData as $gData) {
            $user = User::firstOrCreate(
                ['email' => $gData['email']],
                [
                    'name' => $gData['nama'],
                    'password' => Hash::make('password123'),
                    'role' => 'guru',
                    'no_pegawai' => $gData['no_pegawai'],
                    'tanggal_lahir' => '1995-05-15',
                    'is_active' => true,
                ]
            );

            $guru = Guru::updateOrCreate(
                ['email' => $gData['email']],
                [
                    'user_id' => $user->id,
                    'nama' => $gData['nama'],
                    'no_telp' => $gData['no_telp'],
                    'no_pegawai' => $gData['no_pegawai'],
                    'tanggal_lahir' => '1995-05-15',
                    'alamat' => 'Yogyakarta',
                    'jenjang' => $gData['jenjang_utama'],
                    'is_active' => true,
                ]
            );

            // Relasi guru_cabang
            foreach ($gData['cabang'] as $cid) {
                DB::table('guru_cabang')->updateOrInsert([
                    'guru_id' => $guru->id,
                    'cabang_id' => $cid,
                ]);
            }

            // Relasi guru_jenjang
            foreach ($gData['kualifikasi'] as $jenjang) {
                DB::table('guru_jenjang')->updateOrInsert([
                    'guru_id' => $guru->id,
                    'jenjang' => $jenjang,
                ]);
            }

            $createdGurus[$guru->nama] = $guru;
        }

        // 5. Siswa Sudah Dibimbing Guru (Untuk mengisi kuota/slot)
        $budi = $createdGurus['Budi Santoso, S.Pd.'] ?? null;
        $sarah = $createdGurus['Sarah Az-Zahra, M.Pd.'] ?? null;
        $dian = $createdGurus['Dian Maharani, S.Pd.'] ?? null;
        $fadhil = $createdGurus['Fadhil Ramadhan, S.Si.'] ?? null;
        $lestari = $createdGurus['Lestari Handayani, S.Pd.'] ?? null;
        $zakaria = $createdGurus['Ahmad Zakaria, M.Sc.'] ?? null;

        $assignedStudentsData = [
            // Budi Santoso (5 siswa)
            ['nama' => 'Rizky Alamsyah', 'jenjang' => 'SD', 'kelas' => 4, 'guru_id' => $budi?->id],
            ['nama' => 'Aulia Zahra', 'jenjang' => 'SD', 'kelas' => 5, 'guru_id' => $budi?->id],
            ['nama' => 'Muhammad Dafi', 'jenjang' => 'SD', 'kelas' => 6, 'guru_id' => $budi?->id],
            ['nama' => 'Citra Kirana', 'jenjang' => 'TK', 'kelas' => 0, 'guru_id' => $budi?->id],
            ['nama' => 'Bintang Pratama', 'jenjang' => 'SD', 'kelas' => 3, 'guru_id' => $budi?->id],

            // Sarah Az-Zahra (4 siswa)
            ['nama' => 'Kayla Anandita', 'jenjang' => 'SMP', 'kelas' => 8, 'guru_id' => $sarah?->id],
            ['nama' => 'Raditya Putra', 'jenjang' => 'SMP', 'kelas' => 9, 'guru_id' => $sarah?->id],
            ['nama' => 'Syifa Fauziyah', 'jenjang' => 'SMA_SMK', 'kelas' => 10, 'guru_id' => $sarah?->id],
            ['nama' => 'Naufal Izzudin', 'jenjang' => 'SMP', 'kelas' => 7, 'guru_id' => $sarah?->id],

            // Dian Maharani (6 siswa - Penuh!)
            ['nama' => 'Alvino Putra', 'jenjang' => 'SD', 'kelas' => 4, 'guru_id' => $dian?->id],
            ['nama' => 'Jessica Tan', 'jenjang' => 'SD', 'kelas' => 5, 'guru_id' => $dian?->id],
            ['nama' => 'Devon Michael', 'jenjang' => 'SD', 'kelas' => 6, 'guru_id' => $dian?->id],
            ['nama' => 'Karin Novilda', 'jenjang' => 'SMP', 'kelas' => 7, 'guru_id' => $dian?->id],
            ['nama' => 'Faris Abqari', 'jenjang' => 'SD', 'kelas' => 2, 'guru_id' => $dian?->id],
            ['nama' => 'Haikal Kamil', 'jenjang' => 'SMP', 'kelas' => 8, 'guru_id' => $dian?->id],

            // Fadhil Ramadhan (3 siswa)
            ['nama' => 'Farhan Maulana', 'jenjang' => 'SMA_SMK', 'kelas' => 11, 'guru_id' => $fadhil?->id],
            ['nama' => 'Dwi Lestari', 'jenjang' => 'SMA_SMK', 'kelas' => 12, 'guru_id' => $fadhil?->id],
            ['nama' => 'Aldi Taher', 'jenjang' => 'SMA_SMK', 'kelas' => 10, 'guru_id' => $fadhil?->id],

            // Lestari Handayani (2 siswa)
            ['nama' => 'Kinara Putri', 'jenjang' => 'TK', 'kelas' => 0, 'guru_id' => $lestari?->id],
            ['nama' => 'Rafa Al-Ghifari', 'jenjang' => 'TK', 'kelas' => 0, 'guru_id' => $lestari?->id],

            // Ahmad Zakaria (1 siswa)
            ['nama' => 'Jonathan Christian', 'jenjang' => 'SMA_SMK', 'kelas' => 12, 'guru_id' => $zakaria?->id],
        ];

        foreach ($assignedStudentsData as $sd) {
            Siswa::firstOrCreate(
                ['nama' => $sd['nama'], 'cabang_id' => $cabang1->id],
                [
                    'tempat_lahir' => 'Yogyakarta',
                    'tanggal_lahir' => '2014-06-12',
                    'alamat' => 'Jl. Kaliurang KM 5',
                    'nama_ibu' => 'Ibu '.explode(' ', $sd['nama'])[0],
                    'no_telp_ortu' => '081234567800',
                    'jenjang' => $sd['jenjang'],
                    'kelas' => $sd['kelas'],
                    'guru_id' => $sd['guru_id'],
                    'status' => 'aktif',
                ]
            );
        }

        // 6. Siswa Belum Berguru (Antrean Auto-Assign)
        $unassignedStudents = [
            ['nama' => 'Arkan Naufal Pratama', 'jenjang' => 'SD', 'kelas' => 5, 'ibu' => 'Ibu Dewi Lestari', 'wa' => '081234567801'],
            ['nama' => 'Clara Michelle Sudiro', 'jenjang' => 'SMP', 'kelas' => 8, 'ibu' => 'Ibu Natalia', 'wa' => '082198765432'],
            ['nama' => 'Dimas Bagus Hermawan', 'jenjang' => 'SD', 'kelas' => 3, 'ibu' => 'Ibu Wulandari', 'wa' => '085711223344'],
            ['nama' => 'Fathia Rahma Azzahra', 'jenjang' => 'TK', 'kelas' => 0, 'ibu' => 'Ibu Siti Khodijah', 'wa' => '081399887766'],
            ['nama' => 'Gilang Ramadhan', 'jenjang' => 'SMA_SMK', 'kelas' => 11, 'ibu' => 'Ibu Nurhayati', 'wa' => '081900112233'],
        ];

        foreach ($unassignedStudents as $us) {
            Siswa::firstOrCreate(
                ['nama' => $us['nama'], 'cabang_id' => $cabang1->id],
                [
                    'tempat_lahir' => 'Yogyakarta',
                    'tanggal_lahir' => '2015-02-10',
                    'alamat' => 'Yogyakarta Kota',
                    'nama_ibu' => $us['ibu'],
                    'no_telp_ortu' => $us['wa'],
                    'jenjang' => $us['jenjang'],
                    'kelas' => $us['kelas'],
                    'guru_id' => null,
                    'status' => 'aktif',
                ]
            );
        }

        // 7. Jadwal Mengajar Hari Ini
        $jadwalData = [
            [
                'guru_id' => $budi?->id,
                'hari' => 'Jumat',
                'jam_mulai' => '13:30:00',
                'jam_selesai' => '15:00:00',
                'cabang_id' => $cabang1->id,
            ],
            [
                'guru_id' => $sarah?->id,
                'hari' => 'Jumat',
                'jam_mulai' => '14:00:00',
                'jam_selesai' => '15:30:00',
                'cabang_id' => $cabang1->id,
            ],
            [
                'guru_id' => $dian?->id,
                'hari' => 'Jumat',
                'jam_mulai' => '15:30:00',
                'jam_selesai' => '17:00:00',
                'cabang_id' => $cabang1->id,
            ],
            [
                'guru_id' => $zakaria?->id,
                'hari' => 'Jumat',
                'jam_mulai' => '16:00:00',
                'jam_selesai' => '17:30:00',
                'cabang_id' => $cabang1->id,
            ],
            [
                'guru_id' => $lestari?->id,
                'hari' => 'Jumat',
                'jam_mulai' => '16:30:00',
                'jam_selesai' => '18:00:00',
                'cabang_id' => $cabang1->id,
            ],
            [
                'guru_id' => $fadhil?->id,
                'hari' => 'Jumat',
                'jam_mulai' => '18:30:00',
                'jam_selesai' => '20:00:00',
                'cabang_id' => $cabang1->id,
            ],
        ];

        foreach ($jadwalData as $jd) {
            if ($jd['guru_id']) {
                Jadwal::firstOrCreate([
                    'guru_id' => $jd['guru_id'],
                    'hari' => $jd['hari'],
                    'jam_mulai' => $jd['jam_mulai'],
                    'cabang_id' => $jd['cabang_id'],
                ], [
                    'jam_selesai' => $jd['jam_selesai'],
                ]);
            }
        }

        // 8. Tagihan SPP & Kwitansi Resmi
        $sppSiswa = Siswa::where('cabang_id', $cabang1->id)->take(5)->get();
        if ($sppSiswa->count() >= 4) {
            // Lunas 1
            PembayaranSpp::firstOrCreate([
                'siswa_id' => $sppSiswa[0]->id,
                'bulan' => 10,
                'tahun' => 2026,
            ], [
                'cabang_id' => $cabang1->id,
                'nominal' => 150000.0,
                'status' => 'lunas',
                'nama_pembayar' => 'Bpk. Hendra Santoso',
                'tanggal_bayar' => '2026-10-30',
                'no_kwitansi' => 'KW/CAB01/2026/10/0042',
            ]);

            // Lunas 2
            PembayaranSpp::firstOrCreate([
                'siswa_id' => $sppSiswa[1]->id,
                'bulan' => 10,
                'tahun' => 2026,
            ], [
                'cabang_id' => $cabang1->id,
                'nominal' => 180000.0,
                'status' => 'lunas',
                'nama_pembayar' => 'Ibu Ratna Kumala',
                'tanggal_bayar' => '2026-10-30',
                'no_kwitansi' => 'KW/CAB01/2026/10/0041',
            ]);

            // Lunas 3
            PembayaranSpp::firstOrCreate([
                'siswa_id' => $sppSiswa[2]->id,
                'bulan' => 10,
                'tahun' => 2026,
            ], [
                'cabang_id' => $cabang1->id,
                'nominal' => 220000.0,
                'status' => 'lunas',
                'nama_pembayar' => 'Bpk. Nugroho',
                'tanggal_bayar' => '2026-10-29',
                'no_kwitansi' => 'KW/CAB01/2026/10/0040',
            ]);

            // Belum bayar
            PembayaranSpp::firstOrCreate([
                'siswa_id' => $sppSiswa[3]->id,
                'bulan' => 10,
                'tahun' => 2026,
            ], [
                'cabang_id' => $cabang1->id,
                'nominal' => 180000.0,
                'status' => 'belum_bayar',
            ]);
        }

        // 9. Mutasi Kas Cabang
        $kasData = [
            ['tipe' => 'masuk', 'kategori' => 'SPP', 'nominal' => 150000.0, 'keterangan' => 'Pelunasan SPP Nabila Putri Santoso (KW/CAB01/2026/10/0042)', 'tanggal' => '2026-10-30'],
            ['tipe' => 'masuk', 'kategori' => 'Pendaftaran', 'nominal' => 80000.0, 'keterangan' => 'Pendaftaran Siswa Baru: Arkan Naufal (Diskon Rp 20.000)', 'tanggal' => '2026-10-30'],
            ['tipe' => 'keluar', 'kategori' => 'Listrik & WiFi', 'nominal' => 350000.0, 'keterangan' => 'Tagihan Listrik PLN & Indihome Cabang Diponegoro', 'tanggal' => '2026-10-29'],
            ['tipe' => 'keluar', 'kategori' => 'Modul Belajar', 'nominal' => 450000.0, 'keterangan' => 'Cetak Modul Pembelajaran Matematika SD/SMP (30 Eksemplar)', 'tanggal' => '2026-10-29'],
            ['tipe' => 'masuk', 'kategori' => 'SPP', 'nominal' => 180000.0, 'keterangan' => 'Pelunasan SPP Rizky Pratama (KW/CAB01/2026/10/0041)', 'tanggal' => '2026-10-29'],
        ];

        foreach ($kasData as $k) {
            Transaksi::firstOrCreate([
                'cabang_id' => $cabang1->id,
                'keterangan' => $k['keterangan'],
            ], [
                'tipe' => $k['tipe'],
                'kategori' => $k['kategori'],
                'nominal' => $k['nominal'],
                'tanggal' => $k['tanggal'],
            ]);
        }

        // 10. QR Token Aktif
        QrToken::updateOrCreate(['cabang_id' => $cabang1->id, 'tipe' => 'datang'], [
            'token' => 'SIROJU-8492',
            'expires_at' => Carbon::now()->addSeconds(20),
        ]);
    }
}
