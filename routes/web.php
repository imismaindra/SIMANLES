<?php

use App\Http\Controllers\AdminDashboardController;
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

// 1. Dashboard Utama
Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.home');

// 2. Data & Pendaftaran Siswa
Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
Route::post('/siswa/daftar', [SiswaController::class, 'store'])->name('web.siswa.store');
Route::post('/siswa/kenaikan-kelas', [SiswaController::class, 'kenaikanKelas'])->name('web.siswa.kenaikan-kelas');
Route::patch('/siswa/{id}/status', [SiswaController::class, 'updateStatus'])->name('web.siswa.status');
Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('web.siswa.destroy');

// 3. Distribusi & Manajemen Guru
Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
Route::post('/guru/assign', [GuruController::class, 'assign'])->name('web.guru.assign');
Route::post('/guru/unassign/{siswaId}', [GuruController::class, 'unassign'])->name('web.guru.unassign');
Route::post('/guru/batch-assign', [GuruController::class, 'batchAssign'])->name('web.guru.batch-assign');
Route::post('/guru/auto-assign', [GuruController::class, 'autoAssign'])->name('web.guru.auto-assign');

// 4. Jadwal Mengajar
Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
Route::post('/jadwal', [JadwalController::class, 'store'])->name('web.jadwal.store');
Route::put('/jadwal/{id}', [JadwalController::class, 'update'])->name('web.jadwal.update');
Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('web.jadwal.destroy');

// 5. Presensi Guru & Layar QR
Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
Route::get('/presensi/live-data', [PresensiController::class, 'liveData'])->name('web.presensi.live-data');
Route::get('/presensi/scan', [PresensiController::class, 'scannerPage'])->name('presensi.scanner');
Route::post('/presensi/qr', [PresensiController::class, 'generateQr'])->name('web.presensi.qr');
Route::post('/presensi/scan', [PresensiController::class, 'scanWeb'])->name('web.presensi.scan');
Route::post('/presensi/manual', [PresensiController::class, 'storeManual'])->name('web.presensi.manual');

// 6. Keuangan & Tagihan SPP Siswa
Route::get('/spp', [SppController::class, 'index'])->name('spp.index');
Route::post('/spp/billing', [SppController::class, 'generateBilling'])->name('web.spp.billing');
Route::post('/spp/bayar/{id}', [SppController::class, 'bayar'])->name('web.spp.bayar');
Route::post('/spp/sent/{id}', [SppController::class, 'markSent'])->name('web.spp.sent');
Route::get('/spp/{id}/kwitansi', [SppController::class, 'kwitansi'])->name('web.spp.kwitansi');

// 7. Buku Kas Cabang
Route::get('/kas', [KasController::class, 'index'])->name('kas.index');
Route::post('/kas/transaksi', [KasController::class, 'store'])->name('web.kas.store');

// 8. Master Data & Pengaturan
Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
Route::post('/pengaturan/tarif', [PengaturanController::class, 'updateTarif'])->name('web.pengaturan.tarif');
Route::post('/pengaturan/cabang', [PengaturanController::class, 'storeCabang'])->name('web.pengaturan.cabang');
