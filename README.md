# SIMANLES — Sistem Informasi Manajemen Les

**Sistem Informasi Manajemen Lembaga Kursus & Pelatihan (LKP) Sang Siroju Lillah**

Aplikasi web berbasis Laravel untuk mengelola operasional lembaga bimbingan belajar (bimbel) multi-cabang — dari pendaftaran siswa, distribusi guru, presensi QR, penagihan SPP, hingga keuangan cabang, dalam satu platform terpadu.

---

## Daftar Isi

- [Gambaran Umum](#gambaran-umum)
- [Fitur Lengkap](#fitur-lengkap)
- [Peran & Hak Akses Menu](#peran--hak-akses-menu)
- [Tech Stack](#tech-stack)
- [Skema Database](#skema-database)
- [Prasyarat](#prasyarat)
- [Instalasi & Setup](#instalasi--setup)
- [Akun Demo](#akun-demo)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Menjalankan Tes](#menjalankan-tes)
- [Struktur Proyek](#struktur-proyek)

---

## Gambaran Umum

SIMANLES dirancang untuk mengatasi kerumitan pengelolaan lembaga les yang memiliki **banyak cabang**, banyak guru dengan jadwal beragam, dan ratusan siswa dengan jenjang pendidikan yang berbeda. Sistem ini mengintegrasikan seluruh proses operasional harian ke dalam satu antarmuka admin yang mudah digunakan.

```
Admin Pusat  →  Melihat & mengawasi semua cabang
Admin Cabang →  Mengelola operasional cabang masing-masing
Guru         →  Melakukan presensi mandiri via QR Code
```

---

## Fitur Lengkap

### 🏠 Dashboard Utama
- **KPI Ringkasan Cabang** — jumlah siswa aktif, utilisasi slot guru, saldo kas bersih, dan pendapatan bulan ini
- **Tampilan Dual Role** — beralih antara view Admin Pusat (semua cabang) dan Admin Cabang (per cabang)
- **Widget Siswa Tanpa Guru** — daftar cepat siswa aktif yang belum ditugaskan ke guru
- **Slot Guru Real-time** — kartu status kuota per guru (kapasitas 6 siswa/guru)
- **Jadwal Sesi Hari Ini** — jadwal mengajar yang sedang berjalan
- **Riwayat Pembayaran SPP** — 6 transaksi terbaru dengan quick-link WhatsApp
- **Mutasi Kas Terbaru** — 6 transaksi kas cabang terkini

### 👤 Manajemen Siswa (`/siswa`)
- **Pendaftaran Siswa Baru** — form lengkap dengan data diri, jenjang (TK/SD/SMP/SMA_SMK), kelas, data orang tua, dan penugasan guru awal
- **Pencarian & Filter** — cari berdasarkan nama, nama ibu, atau nomor HP; filter per jenjang, status, dan status penugasan guru
- **Update Status Siswa** — ubah status antara `aktif`, `cuti`, dan `keluar`
- **Kenaikan Kelas Massal** — proses kenaikan kelas akhir tahun ajaran (1-klik untuk seluruh cabang)
- **Soft Delete** — hapus data siswa tanpa menghilangkan riwayat historis
- **Rekapitulasi Statistik** — total siswa, breakdown per jenjang (TK/SD/SMP/SMA_SMK), dan siswa tanpa guru

### 👩‍🏫 Distribusi & Manajemen Guru (`/guru`)
- **Daftar Guru & Slot** — kartu guru beserta kualifikasi jenjang, jumlah siswa bimbingan, dan sisa kapasitas slot
- **Assign Manual** — tugaskan satu siswa ke satu guru (dengan validasi kapasitas & kualifikasi jenjang)
- **Batch Assign** — tugaskan beberapa siswa sekaligus ke satu guru
- **Auto-Assign 1-Klik** — algoritma distribusi otomatis yang menyebarkan siswa tanpa guru ke guru yang tersedia sesuai jenjang dan kapasitas
- **Unassign Siswa** — lepas bimbingan siswa dari guru
- **Indikator Kapasitas** — persentase utilisasi slot keseluruhan cabang, jumlah guru penuh, dan slot tersisa

### 📅 Jadwal Mengajar (`/jadwal`)
- **Tambah Sesi Jadwal** — buat jadwal mengajar dengan guru, siswa, jam mulai, dan jam selesai
- **Edit & Hapus Jadwal** — perbarui atau batalkan sesi yang sudah terjadwal
- **View Jadwal Harian** — tampilan sesi mengajar hari ini per cabang

### 🎯 Presensi Guru & QR Code (`/presensi`)
- **QR Code Dinamis** — generate QR token berumur pendek (5–60 detik) untuk datang dan pulang
- **Refresh QR Otomatis** — token kedaluwarsa otomatis untuk mencegah penyalahgunaan
- **Halaman Scanner Mandiri** (`/presensi/scan`) — guru scan QR via browser smartphone tanpa app tambahan
- **Presensi Datang & Pulang** — sistem mencatat jam datang dan jam pulang secara terpisah
- **Live Data Polling** — endpoint `/presensi/live-data` memperbarui tampilan layar kiosk secara real-time
- **Riwayat Absensi** — histori kehadiran 7 hari terakhir per cabang
- **Statistik Kehadiran Hari Ini** — jumlah guru bertugas, sudah datang, sedang mengajar, belum hadir, dan persentase kehadiran

### 💰 SPP & Penagihan (`/spp`)
- **Generate Tagihan Bulanan** — buat tagihan SPP otomatis untuk semua siswa aktif bulan tertentu (idempoten: aman dijalankan ulang)
- **Tarif Dinamis per Jenjang** — tarif berbeda untuk TK, SD kelas 1–3, SD kelas 4–6, SMP, dan SMA/SMK
- **Pelunasan SPP** — catat pembayaran dengan nama pembayar dan tanggal bayar
- **Nomor Kwitansi Resmi** — diterbitkan otomatis saat pelunasan
- **Cetak Kwitansi** (`/spp/{id}/kwitansi`) — tampilan slip kwitansi siap cetak
- **Kirim via WhatsApp** — link deep-link WA langsung ke nomor HP orang tua siswa; catat status pengiriman
- **Filter & Cari** — filter per bulan, tahun, status (lunas/belum bayar), dan nama siswa/ibu
- **Laporan Periode** — total tagihan, nominal lunas, nominal tunggakan, dan persentase pembayaran
- **Generate Otomatis Terjadwal** — Artisan command `spp:generate-monthly` dijalankan via scheduler

### 🗂️ Buku Kas Cabang (`/kas`)
- **Catat Transaksi Kas Masuk & Keluar** — dengan kategori dan keterangan bebas
- **Filter Mutasi** — filter per bulan, tahun, tipe (masuk/keluar), dan kategori
- **Laporan Laba Rugi** — rekapitulasi total pemasukan, pengeluaran, dan saldo bersih per periode
- **Rekap Kas via API** — endpoint JSON `/api/kas/rekap` untuk integrasi eksternal

### ⚙️ Pengaturan & Master Data (`/pengaturan`)
- **Master Tarif SPP** — atur nominal SPP per jenjang dan kelas secara global
- **Biaya Pendaftaran** — atur biaya pendaftaran siswa baru
- **Manajemen Cabang** — tambah cabang baru dengan nama, kode unik, dan alamat
- **Ringkasan Cabang** — daftar semua cabang beserta jumlah siswa aktif dan guru

---

## Peran & Hak Akses Menu

Sistem memiliki **3 peran pengguna** yang dikontrol via field `role` pada tabel `users`:

| Menu / Fitur | `pusat` (Admin Pusat) | `cabang` (Admin Cabang) | `guru` (Guru) |
|---|:---:|:---:|:---:|
| **Dashboard** — semua cabang | ✅ | ❌ | ❌ |
| **Dashboard** — cabang sendiri | ✅ | ✅ | ❌ |
| **Siswa** — lihat daftar | ✅ Semua cabang | ✅ Cabang sendiri | ❌ |
| **Siswa** — daftar baru | ✅ | ✅ | ❌ |
| **Siswa** — update status | ✅ | ✅ | ❌ |
| **Siswa** — kenaikan kelas massal | ✅ | ✅ | ❌ |
| **Siswa** — hapus | ✅ | ✅ | ❌ |
| **Guru** — lihat distribusi | ✅ Semua cabang | ✅ Cabang sendiri | ❌ |
| **Guru** — assign/unassign manual | ✅ | ✅ | ❌ |
| **Guru** — batch assign | ✅ | ✅ | ❌ |
| **Guru** — auto-assign | ✅ | ✅ | ❌ |
| **Jadwal** — lihat jadwal | ✅ | ✅ | ❌ |
| **Jadwal** — tambah/edit/hapus | ✅ | ✅ | ❌ |
| **Presensi** — layar QR & monitor | ✅ | ✅ | ❌ |
| **Presensi** — scanner mandiri (scan QR) | ❌ | ❌ | ✅ |
| **SPP** — lihat tagihan | ✅ Semua cabang | ✅ Cabang sendiri | ❌ |
| **SPP** — generate tagihan bulanan | ✅ | ✅ | ❌ |
| **SPP** — catat pelunasan | ✅ | ✅ | ❌ |
| **SPP** — cetak kwitansi | ✅ | ✅ | ❌ |
| **SPP** — kirim WhatsApp | ✅ | ✅ | ❌ |
| **Kas** — lihat mutasi | ✅ | ✅ Cabang sendiri | ❌ |
| **Kas** — catat transaksi | ✅ | ✅ | ❌ |
| **Pengaturan** — master tarif SPP | ✅ | ❌ | ❌ |
| **Pengaturan** — biaya pendaftaran | ✅ | ❌ | ❌ |
| **Pengaturan** — tambah cabang baru | ✅ | ❌ | ❌ |
| **Pengaturan** — lihat data cabang | ✅ | ✅ | ❌ |

> **Catatan:** Pengecekan role saat ini berbasis parameter `?role=pusat` pada URL. Implementasi autentikasi session Laravel Breeze/Fortify dapat ditambahkan sebagai enhancement berikutnya.

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| **Framework** | Laravel 13 (PHP 8.3+) |
| **Frontend** | Blade Templates + Tailwind CSS |
| **Reaktivitas** | Livewire 4 |
| **Database** | SQLite (dev) / MySQL (prod) |
| **QR Code** | `simplesoftwareio/simple-qrcode` |
| **PDF / Kwitansi** | `barryvdh/laravel-dompdf` |
| **Charts** | `arielmejiadev/larapex-charts` |
| **Icons** | `mallardduck/blade-lucide-icons` |
| **Testing** | PHPUnit 12 |
| **Build Tool** | Vite + npm |

---

## Skema Database

```
users           → akun pengguna (role: pusat | cabang | guru)
cabang          → data master cabang
guru            → data profil guru
guru_cabang     → pivot: guru ↔ cabang (many-to-many)
guru_jenjang    → pivot: guru ↔ kualifikasi jenjang
siswa           → data siswa (relasi ke guru & cabang)
tarif_spp       → master tarif SPP per jenjang & rentang kelas
pembayaran_spp  → tagihan & status pembayaran bulanan per siswa
transaksi       → buku kas (masuk/keluar) per cabang
jadwal          → sesi mengajar (guru ↔ siswa)
absensi         → rekam kehadiran guru (jam datang & pulang)
qr_token        → token QR presensi berumur pendek
settings        → konfigurasi global (key-value)
```

---

## Prasyarat

- **PHP** >= 8.3
- **Composer** >= 2.x
- **Node.js** >= 18.x & npm
- **SQLite** (default untuk development) atau MySQL/PostgreSQL

---

## Instalasi & Setup

### 1. Clone Repositori

```bash
git clone <url-repositori> simanles
cd simanles
```

### 2. Setup Otomatis (Recommended)

Jalankan satu perintah yang menginstal semua dependensi, generate key, migrate, dan build aset:

```bash
composer run setup
```

### 3. Setup Manual (Opsional)

```bash
# Install dependensi PHP
composer install

# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Jalankan migrasi database
php artisan migrate

# Install dependensi JS & build aset
npm install
npm run build
```

### 4. Isi Data Awal (Seeding)

Jalankan seeder untuk mengisi data demo lengkap: 2 cabang, 3 admin, 12 guru, 80+ siswa, tarif SPP, dan sampel transaksi kas:

```bash
php artisan db:seed --class=SimanlesSeeder
```

---

## Akun Demo

Setelah seeder dijalankan, akun berikut tersedia untuk login:

| Role | Email | Password |
|---|---|---|
| **Admin Pusat** | `admin@lespintar.id` | `admin123` |
| **Admin Cabang Ponokawan** | `cabang.ponokawan@lespintar.id` | `cabang123` |
| **Admin Cabang Jakarta Selatan** | `cabang.jaksel@lespintar.id` | `cabang123` |
| **Guru** (contoh) | `nip3101@lespintar.id` | `1995-05-14` (tanggal lahir) |

> Semua akun guru menggunakan **tanggal lahir** sebagai password default dalam format `YYYY-MM-DD`.

---

## Menjalankan Aplikasi

### Mode Development

```bash
composer run dev
```

Perintah ini menjalankan server Laravel, queue worker, dan Vite dev server secara bersamaan.

### Manual

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server (hot-reload)
npm run dev
```

Akses aplikasi di: **http://localhost:8000**

### Scheduler (SPP Otomatis)

Untuk mengaktifkan generate tagihan SPP bulanan secara otomatis, tambahkan cron berikut di server:

```bash
* * * * * cd /path/ke/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Menjalankan Tes

```bash
# Jalankan semua tes
php artisan test

# Jalankan tes spesifik
php artisan test --filter=SiswaLogicTest
php artisan test --filter=PresensiQrLogicTest
php artisan test --filter=SppAndKasLogicTest
```

### Daftar Test Suite

| File Test | Cakupan |
|---|---|
| `SiswaLogicTest` | Pendaftaran, update status, kenaikan kelas, soft delete |
| `GuruAssignmentLogicTest` | Assign, unassign, batch assign, auto-assign, validasi kapasitas |
| `JadwalLogicTest` | Buat, edit, hapus jadwal mengajar |
| `PresensiQrLogicTest` | Generate QR token, scan datang, scan pulang, token kedaluwarsa |
| `SppAndKasLogicTest` | Generate tagihan, pelunasan, kwitansi, catat kas masuk/keluar |
| `MenuPagesTest` | Smoke test semua halaman menu dapat diakses (HTTP 200) |

---

## Struktur Proyek

```
app/
├── Console/Commands/
│   └── GenerateMonthlySppBilling.php   # Artisan command SPP bulanan
├── Http/Controllers/
│   ├── AdminDashboardController.php    # Dashboard utama
│   ├── GuruController.php              # Manajemen & distribusi guru
│   ├── JadwalController.php            # Jadwal mengajar
│   ├── KasController.php               # Buku kas cabang
│   ├── PengaturanController.php        # Master data & pengaturan
│   ├── PresensiController.php          # Presensi QR & scanner
│   ├── SiswaController.php             # Manajemen siswa
│   └── SppController.php              # Tagihan & pembayaran SPP
├── Models/
│   ├── Absensi.php                     # Rekam kehadiran guru
│   ├── Cabang.php                      # Data cabang
│   ├── Guru.php                        # Data profil guru
│   ├── Jadwal.php                      # Sesi mengajar
│   ├── PembayaranSpp.php               # Tagihan SPP siswa
│   ├── QrToken.php                     # Token QR presensi
│   ├── Setting.php                     # Konfigurasi global
│   ├── Siswa.php                       # Data siswa
│   ├── TarifSpp.php                    # Master tarif SPP
│   ├── Transaksi.php                   # Mutasi kas
│   └── User.php                        # Akun pengguna
└── Services/
    ├── CabangContextService.php        # Konteks cabang aktif per request
    ├── GuruService.php                 # Logika assign/auto-assign guru
    ├── JadwalService.php               # Logika jadwal mengajar
    ├── KasService.php                  # Logika catat & rekap kas
    ├── PresensiService.php             # Logika QR & scan presensi
    ├── SiswaService.php                # Logika daftar & kenaikan kelas
    └── SppService.php                  # Logika generate tagihan & pelunasan

database/
├── migrations/                         # 12 file migrasi database
└── seeders/
    └── SimanlesSeeder.php              # Data demo lengkap (2 cabang, 80+ siswa)

routes/
└── web.php                             # Semua route web & API
```

---

## Lisensi

Proyek ini dikembangkan sebagai bagian dari **Tugas Proyek Semester 7** — Universitas.  
Hak cipta © 2026 LKP Sang Siroju Lillah. Seluruh hak dilindungi.
