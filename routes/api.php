<?php

use App\Http\Controllers\GuruController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SppController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Tanpa Auth & RBAC sesuai permintaan spesifikasi)
|--------------------------------------------------------------------------
*/

// Siswa & Pendaftaran
Route::prefix('siswa')->group(function () {
    Route::post('/', [SiswaController::class, 'store'])->name('api.siswa.store');
    Route::patch('/{id}/status', [SiswaController::class, 'updateStatus'])->name('api.siswa.status');
    Route::post('/kenaikan-kelas', [SiswaController::class, 'kenaikanKelas'])->name('api.siswa.kenaikan-kelas');
    Route::delete('/{id}', [SiswaController::class, 'destroy'])->name('api.siswa.destroy');
});

// Guru & Alokasi Kuota
Route::prefix('guru')->group(function () {
    Route::post('/assign', [GuruController::class, 'assign'])->name('api.guru.assign');
    Route::post('/unassign/{siswaId}', [GuruController::class, 'unassign'])->name('api.guru.unassign');
    Route::post('/batch-assign', [GuruController::class, 'batchAssign'])->name('api.guru.batch-assign');
    Route::post('/auto-assign', [GuruController::class, 'autoAssign'])->name('api.guru.auto-assign');
});

// Jadwal & Deteksi Bentrok Lintas Cabang
Route::prefix('jadwal')->group(function () {
    Route::post('/', [JadwalController::class, 'store'])->name('api.jadwal.store');
    Route::put('/{id}', [JadwalController::class, 'update'])->name('api.jadwal.update');
    Route::delete('/{id}', [JadwalController::class, 'destroy'])->name('api.jadwal.destroy');
});

// SPP, Kwitansi Resmi & Cron Bulanan
Route::prefix('spp')->group(function () {
    Route::post('/billing', [SppController::class, 'generateBilling'])->name('api.spp.billing');
    Route::post('/{id}/bayar', [SppController::class, 'bayar'])->name('api.spp.bayar');
    Route::post('/{id}/sent', [SppController::class, 'markSent'])->name('api.spp.sent');
});

// Buku Kas & Laba Rugi
Route::prefix('kas')->group(function () {
    Route::post('/', [KasController::class, 'store'])->name('api.kas.store');
    Route::get('/rekap', [KasController::class, 'rekap'])->name('api.kas.rekap');
});

// Presensi QR Dinamis
Route::prefix('absensi')->group(function () {
    Route::post('/qr', [PresensiController::class, 'generateQr'])->name('api.absensi.qr');
    Route::post('/scan', [PresensiController::class, 'scan'])->name('api.absensi.scan');
});

