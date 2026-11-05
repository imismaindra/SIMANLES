<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SppController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Sistem Informasi Manajemen Les - LKP Sang Siroju Lillah)
|--------------------------------------------------------------------------
*/

// Otentikasi Pengguna (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Otentikasi Keluar (Auth)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Area Internal Terproteksi (Auth Required)
Route::middleware('auth')->group(function () {

    // 1. Dashboard Utama (Pusat & Cabang)
    Route::middleware('role:pusat,cabang')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.home');
    });

    // 2. Data & Pendaftaran Siswa (Pusat & Cabang)
    Route::middleware('role:pusat,cabang')->prefix('siswa')->group(function () {
        Route::get('/', [SiswaController::class, 'index'])->name('siswa.index');
        Route::post('/daftar', [SiswaController::class, 'store'])->name('web.siswa.store');
        Route::put('/{id}', [SiswaController::class, 'update'])->name('web.siswa.update');
        Route::post('/import', [SiswaController::class, 'importExcel'])->name('web.siswa.import');
        Route::post('/kenaikan-kelas', [SiswaController::class, 'kenaikanKelas'])->name('web.siswa.kenaikan-kelas');
        Route::patch('/{id}/status', [SiswaController::class, 'updateStatus'])->name('web.siswa.status');
        Route::delete('/{id}', [SiswaController::class, 'destroy'])->name('web.siswa.destroy');
    });

    // 3. Distribusi & Manajemen Guru (Pusat & Cabang)
    Route::middleware('role:pusat,cabang')->prefix('guru')->group(function () {
        Route::get('/', [GuruController::class, 'index'])->name('guru.index');
        Route::post('/', [GuruController::class, 'store'])->name('web.guru.store');
        Route::put('/{id}', [GuruController::class, 'update'])->name('web.guru.update');
        Route::delete('/{id}', [GuruController::class, 'destroy'])->name('web.guru.destroy');
        Route::post('/import', [GuruController::class, 'importExcel'])->name('web.guru.import');
        Route::post('/assign', [GuruController::class, 'assign'])->name('web.guru.assign');
        Route::post('/unassign/{siswaId}', [GuruController::class, 'unassign'])->name('web.guru.unassign');
        Route::post('/batch-assign', [GuruController::class, 'batchAssign'])->name('web.guru.batch-assign');
        Route::post('/auto-assign', [GuruController::class, 'autoAssign'])->name('web.guru.auto-assign');
    });

    // 4. Jadwal Mengajar (Akses: Guru, Cabang, Pusat)
    Route::prefix('jadwal')->group(function () {
        Route::get('/', [JadwalController::class, 'index'])->name('jadwal.index');
        Route::post('/', [JadwalController::class, 'store'])->name('web.jadwal.store')->middleware('role:pusat,cabang');
        Route::put('/{id}', [JadwalController::class, 'update'])->name('web.jadwal.update')->middleware('role:pusat,cabang');
        Route::delete('/{id}', [JadwalController::class, 'destroy'])->name('web.jadwal.destroy')->middleware('role:pusat,cabang');
    });

    // 5. Presensi Guru & Layar QR (Akses: Guru, Cabang, Pusat)
    Route::prefix('presensi')->group(function () {
        Route::get('/', [PresensiController::class, 'index'])->name('presensi.index');
        Route::get('/live-data', [PresensiController::class, 'liveData'])->name('web.presensi.live-data');
        Route::get('/scan', [PresensiController::class, 'scannerPage'])->name('presensi.scanner');
        Route::post('/qr', [PresensiController::class, 'generateQr'])->name('web.presensi.qr')->middleware('role:pusat,cabang');
        Route::post('/scan', [PresensiController::class, 'scanWeb'])->name('web.presensi.scan');
    });

    // 6. Keuangan & Tagihan SPP Siswa (Pusat & Cabang)
    Route::middleware('role:pusat,cabang')->prefix('spp')->group(function () {
        Route::get('/', [SppController::class, 'index'])->name('spp.index');
        Route::post('/billing', [SppController::class, 'generateBilling'])->name('web.spp.billing');
        Route::post('/bayar/{id}', [SppController::class, 'bayar'])->name('web.spp.bayar');
        Route::post('/sent/{id}', [SppController::class, 'markSent'])->name('web.spp.sent');
        Route::get('/{id}/kwitansi', [SppController::class, 'kwitansi'])->name('web.spp.kwitansi');
    });

    // 7. Buku Kas Cabang (Pusat & Cabang)
    Route::middleware('role:pusat,cabang')->prefix('kas')->group(function () {
        Route::get('/', [KasController::class, 'index'])->name('kas.index');
        Route::post('/transaksi', [KasController::class, 'store'])->name('web.kas.store');
    });

    // 8. Master Data, Tarif & Kelola Cabang (Khusus Pusat)
    Route::middleware('role:pusat')->prefix('pengaturan')->group(function () {
        Route::get('/', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('/tarif', [PengaturanController::class, 'updateTarif'])->name('web.pengaturan.tarif');
        Route::post('/cabang', [PengaturanController::class, 'storeCabang'])->name('web.pengaturan.cabang');
        Route::put('/cabang/{id}', [PengaturanController::class, 'updateCabang'])->name('web.pengaturan.cabang.update');
        Route::patch('/cabang/{id}/status', [PengaturanController::class, 'toggleCabangStatus'])->name('web.pengaturan.cabang.toggle');
    });

    // 9. Audit Log & Keamanan Sistem (Khusus Pusat)
    Route::middleware('role:pusat')->get('/audit', [AuditController::class, 'index'])->name('audit.index');
});
