<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the education management admin dashboard.
     */
    public function index(Request $request): View
    {
        $currentBranch = 'Cabang Utama Diponegoro';
        $academicYear = 'Tahun Ajaran 2026/2027';
        $currentSemester = 'Semester Ganjil';
        $todayFormatted = 'Jumat, 30 Oktober 2026';

        // KPI Metrik Terukur
        $kpi = [
            'sesi_hari_ini' => [
                'total' => 24,
                'selesai' => 14,
                'berlangsung' => 6,
                'siap_mulai' => 4,
                'baseline_note' => 'vs rata-rata 21 sesi di hari Jumat',
            ],
            'siswa_aktif' => [
                'total' => 342,
                'privat' => 94,
                'reguler' => 186,
                'intensif' => 62,
                'delta' => '+18 siswa baru bulan ini',
            ],
            'presensi_siswa' => [
                'persentase' => 97.2,
                'hadir' => 174,
                'terjadwal' => 179,
                'izin' => 3,
                'dispensasi' => 2,
                'target' => 95.0,
            ],
            'keuangan' => [
                'terkumpul' => 84750000,
                'target' => 90000000,
                'persentase' => 94.1,
                'selisih' => 5250000,
                'jatuh_tempo' => 7,
            ],
        ];

        // Item Prioritas Mendesak (Perlu Tindakan Admin)
        $urgentActions = [
            [
                'id' => 'ACT-01',
                'tipe' => 'reschedule',
                'judul' => 'Pengajuan Reschedule Sesi Les',
                'deskripsi' => 'Dimas Arya (Privat Fisika SMA XII) minta geser dari 15.30 ke Sabtu 10.00 (ulangan harian sekolah).',
                'waktu' => '10 menit lalu',
                'urgensi' => 'Perlu Konfirmasi',
                'aksi' => 'Tinjau Jadwal',
            ],
            [
                'id' => 'ACT-02',
                'tipe' => 'verifikasi_bayar',
                'judul' => 'Verifikasi Bukti Transfer SPP',
                'deskripsi' => 'Jonathan Christian · Transfer Rp 4.500.000 (Paket Intensif SNBT 2027). Bukti transfer telah diunggah.',
                'waktu' => '35 menit lalu',
                'urgensi' => '3 Pembayaran',
                'aksi' => 'Verifikasi Kuitansi',
            ],
            [
                'id' => 'ACT-03',
                'tipe' => 'tentor_substitusi',
                'judul' => 'Pergantian Tentor Sesi 16.30',
                'deskripsi' => 'Kak Farhan digantikan Kak Dimas Wahyu untuk sesi Matematika Saintek R. Galileo.',
                'waktu' => '1 jam lalu',
                'urgensi' => 'Telah Terverifikasi',
                'aksi' => 'Detail Sesi',
            ],
        ];

        // Sesi Pembelajaran Hari Ini
        $sessions = [
            [
                'id' => 'SES-101',
                'waktu_mulai' => '08:30',
                'waktu_selesai' => '10:00',
                'mata_pelajaran' => 'Matematika Saintek',
                'tingkat' => 'SMA Kelas XII',
                'program' => 'Privat 1-on-1',
                'ruangan' => 'Bilik Privat 01',
                'tentor' => 'Kak Fadhil Ramadhan, S.Si.',
                'siswa' => 'Dimas Arya Pratama',
                'status' => 'selesai',
                'catatan_belajar' => 'Pembahasan TKA Limit Trigonometri & L\'Hopital. Penguasaan rumus dasar sangat baik.',
            ],
            [
                'id' => 'SES-102',
                'waktu_mulai' => '10:15',
                'waktu_selesai' => '11:45',
                'mata_pelajaran' => 'Fisika Dasar & UTBK',
                'tingkat' => 'SMA Kelas XII',
                'program' => 'Reguler Kelompok',
                'ruangan' => 'Ruang Galileo',
                'tentor' => 'Kak Sarah Az-Zahra, M.Pd.',
                'siswa' => 'Nabila Putri & 4 siswa lainnya',
                'status' => 'selesai',
                'catatan_belajar' => 'Dinamika Rotasi & Hukum Kekekalan Momentum Sudut. Latihan 15 soal tuntas.',
            ],
            [
                'id' => 'SES-103',
                'waktu_mulai' => '13:00',
                'waktu_selesai' => '14:30',
                'mata_pelajaran' => 'Bahasa Inggris TOEFL Prep',
                'tingkat' => 'Alumni & SMA',
                'program' => 'Privat 1-on-1',
                'ruangan' => 'Bilik Privat 02',
                'tentor' => 'Mr. Edward Salim, B.Ed.',
                'siswa' => 'Kevin Sanjaya',
                'status' => 'selesai',
                'catatan_belajar' => 'Structure & Written Expression diagnostic test. Skor simulasi 560.',
            ],
            [
                'id' => 'SES-104',
                'waktu_mulai' => '14:45',
                'waktu_selesai' => '16:15',
                'mata_pelajaran' => 'Kimia Stoikiometri',
                'tingkat' => 'SMA Kelas XI',
                'program' => 'Reguler Kelompok',
                'ruangan' => 'Ruang Newton',
                'tentor' => 'Kak Dian Maharani, S.Pd.',
                'siswa' => 'Kelompok XI-IPA 2 (6 siswa)',
                'status' => 'berlangsung',
                'catatan_belajar' => 'Materi pereaksi pembatas & kadar zat dalam senyawa. Sedang sesi latihan mandiri.',
            ],
            [
                'id' => 'SES-105',
                'waktu_mulai' => '15:30',
                'waktu_selesai' => '17:00',
                'mata_pelajaran' => 'Matematika SMP',
                'tingkat' => 'SMP Kelas IX',
                'program' => 'Privat 1-on-1',
                'ruangan' => 'Bilik Privat 03',
                'tentor' => 'Kak Budi Pratama, S.Pd.',
                'siswa' => 'Rizky Maulana',
                'status' => 'berlangsung',
                'catatan_belajar' => 'Persamaan Kuadrat & Fungsi Kuadrat. Target persiapan Asesmen Sumatif Akhir Jenjang.',
            ],
            [
                'id' => 'SES-106',
                'waktu_mulai' => '16:30',
                'waktu_selesai' => '18:00',
                'mata_pelajaran' => 'Biologi Sel & Genetika',
                'tingkat' => 'Intensif SNBT',
                'program' => 'Intensif SNBT',
                'ruangan' => 'Ruang Einstein',
                'tentor' => 'Kak Nurul Hidayah, S.Ked.',
                'siswa' => 'Batch Alpha Kedokteran (8 siswa)',
                'status' => 'siap_mulai',
                'catatan_belajar' => 'Persiapan modul pewarisan sifat Mendel & penyimpangan semu. Materi tayang siap di proyektor.',
            ],
            [
                'id' => 'SES-107',
                'waktu_mulai' => '18:30',
                'waktu_selesai' => '20:00',
                'mata_pelajaran' => 'Penalaran Matematika SNBT',
                'tingkat' => 'SMA Kelas XII',
                'program' => 'Reguler Kelompok',
                'ruangan' => 'Ruang Newton',
                'tentor' => 'Kak Fadhil Ramadhan, S.Si.',
                'siswa' => 'Kelas Reguler XII-B (8 siswa)',
                'status' => 'terjadwal',
                'catatan_belajar' => 'Drilling soal penalaran kuantitatif 2026. Modul kuis 20 butir soal siap.',
            ],
            [
                'id' => 'SES-108',
                'waktu_mulai' => '18:45',
                'waktu_selesai' => '20:15',
                'mata_pelajaran' => 'Bahasa Indonesia Literasi',
                'tingkat' => 'SMP Kelas IX',
                'program' => 'Privat 1-on-1',
                'ruangan' => 'Bilik Privat 01',
                'tentor' => 'Kak Anisa Rahma, M.Hum.',
                'siswa' => 'Amanda Aurelia',
                'status' => 'terjadwal',
                'catatan_belajar' => 'Teknik membaca cepat & analisis ide pokok teks argumentatif.',
            ],
        ];

        // Tentor Bertugas Hari Ini
        $tentorsOnDuty = [
            [
                'nama' => 'Kak Fadhil Ramadhan, S.Si.',
                'bidang' => 'Matematika Saintek',
                'total_sesi_hari_ini' => 3,
                'jam_selesai' => '20:00',
                'ruangan_saat_ini' => 'Bilik Privat 01',
                'status_tentor' => 'Sesi Berikutnya: 18.30',
                'inisial' => 'FR',
                'warna_avatar' => 'bg-slate-800 text-white',
            ],
            [
                'nama' => 'Kak Sarah Az-Zahra, M.Pd.',
                'bidang' => 'Fisika & UTBK',
                'total_sesi_hari_ini' => 2,
                'jam_selesai' => '11:45',
                'ruangan_saat_ini' => 'R. Guru / Konsultasi',
                'status_tentor' => 'Sesi Hari Ini Tuntas',
                'inisial' => 'SA',
                'warna_avatar' => 'bg-blue-800 text-white',
            ],
            [
                'nama' => 'Mr. Edward Salim, B.Ed.',
                'bidang' => 'English & TOEFL',
                'total_sesi_hari_ini' => 2,
                'jam_selesai' => '17:30',
                'ruangan_saat_ini' => 'Bilik Privat 02',
                'status_tentor' => 'Sesi 2 Siap 16.00',
                'inisial' => 'ES',
                'warna_avatar' => 'bg-amber-800 text-white',
            ],
            [
                'nama' => 'Kak Dian Maharani, S.Pd.',
                'bidang' => 'Kimia SMA',
                'total_sesi_hari_ini' => 2,
                'jam_selesai' => '18:15',
                'ruangan_saat_ini' => 'Ruang Newton',
                'status_tentor' => 'Sedang Mengajar',
                'inisial' => 'DM',
                'warna_avatar' => 'bg-emerald-800 text-white',
            ],
            [
                'nama' => 'Kak Nurul Hidayah, S.Ked.',
                'bidang' => 'Biologi Kedinasan/SNBT',
                'total_sesi_hari_ini' => 1,
                'jam_selesai' => '18:00',
                'ruangan_saat_ini' => 'Ruang Einstein',
                'status_tentor' => 'Mulai 16.30',
                'inisial' => 'NH',
                'warna_avatar' => 'bg-teal-800 text-white',
            ],
        ];

        // Riwayat Transaksi & Pembayaran Terbaru
        $recentTransactions = [
            [
                'kode_inv' => 'INV-202610-092',
                'nama_siswa' => 'Ananda Rayhan',
                'wali' => 'Ibu Ratna',
                'paket' => 'Paket 16 Sesi Privat SMA',
                'program' => 'Privat',
                'nominal' => 2400000,
                'metode' => 'BCA Virtual Account',
                'waktu' => '30 Okt 2026, 13:42',
                'status' => 'lunas',
            ],
            [
                'kode_inv' => 'INV-202610-091',
                'nama_siswa' => 'Siti Zahra',
                'wali' => 'Bpk. Haryono',
                'paket' => 'SPP Reguler Kelas IX SMP',
                'program' => 'Reguler',
                'nominal' => 650000,
                'metode' => 'QRIS Mandiri',
                'waktu' => '30 Okt 2026, 11:20',
                'status' => 'lunas',
            ],
            [
                'kode_inv' => 'INV-202610-090',
                'nama_siswa' => 'Jonathan Christian',
                'wali' => 'Ibu Linda',
                'paket' => 'Paket Intensif SNBT 2027',
                'program' => 'Intensif',
                'nominal' => 4500000,
                'metode' => 'Transfer Bank Jatim',
                'waktu' => '30 Okt 2026, 09:15',
                'status' => 'menunggu_verifikasi',
            ],
            [
                'kode_inv' => 'INV-202610-089',
                'nama_siswa' => 'Alifia Maharani',
                'wali' => 'Bpk. Nugroho',
                'paket' => 'Paket 8 Sesi Privat SD',
                'program' => 'Privat',
                'nominal' => 1100000,
                'metode' => 'Tunai (Kasir Cabang)',
                'waktu' => '29 Okt 2026, 16:30',
                'status' => 'lunas',
            ],
            [
                'kode_inv' => 'INV-202610-088',
                'nama_siswa' => 'Farhan Maulana',
                'wali' => 'Ibu Sri Wahyuni',
                'paket' => 'SPP Reguler Kelas XI SMA',
                'program' => 'Reguler',
                'nominal' => 750000,
                'metode' => 'Tagihan Otomatis',
                'waktu' => '28 Okt 2026, 08:00',
                'status' => 'jatuh_tempo',
            ],
        ];

        // Data Beban Belajar Mingguan (Sesi Terjadwal vs Kapasitas Ruang)
        $weeklyLoad = [
            ['hari' => 'Senin', 'label' => 'Sen', 'sesi' => 20, 'kapasitas' => 28],
            ['hari' => 'Selasa', 'label' => 'Sel', 'sesi' => 22, 'kapasitas' => 28],
            ['hari' => 'Rabu', 'label' => 'Rab', 'sesi' => 25, 'kapasitas' => 28],
            ['hari' => 'Kamis', 'label' => 'Kam', 'sesi' => 21, 'kapasitas' => 28],
            ['hari' => 'Jumat', 'label' => 'Jum', 'sesi' => 24, 'kapasitas' => 28, 'is_today' => true],
            ['hari' => 'Sabtu', 'label' => 'Sab', 'sesi' => 27, 'kapasitas' => 30],
        ];

        // Okupansi Ruangan Hari Ini
        $roomOccupancy = [
            ['nama' => 'Ruang Galileo (Reguler)', 'kapasitas_kursi' => 12, 'sesi_terisi' => 5, 'total_sesi' => 6, 'persen' => 83],
            ['nama' => 'Ruang Newton (Reguler)', 'kapasitas_kursi' => 12, 'sesi_terisi' => 6, 'total_sesi' => 6, 'persen' => 100],
            ['nama' => 'Ruang Einstein (Intensif)', 'kapasitas_kursi' => 10, 'sesi_terisi' => 4, 'total_sesi' => 5, 'persen' => 80],
            ['nama' => 'Bilik Privat 01-04 (1-on-1)', 'kapasitas_kursi' => 8, 'sesi_terisi' => 9, 'total_sesi' => 10, 'persen' => 90],
        ];

        return view('admin.dashboard', compact(
            'currentBranch',
            'academicYear',
            'currentSemester',
            'todayFormatted',
            'kpi',
            'urgentActions',
            'sessions',
            'tentorsOnDuty',
            'recentTransactions',
            'weeklyLoad',
            'roomOccupancy'
        ));
    }
}
