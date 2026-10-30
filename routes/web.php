<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SppController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Tanpa Auth & RBAC sesuai permintaan spesifikasi)
|--------------------------------------------------------------------------
*/

// Dashboard Utama
Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.home');

// Form Actions (Direct Web Actions)
Route::post('/siswa/daftar', [SiswaController::class, 'store'])->name('web.siswa.store');
Route::post('/guru/auto-assign', [GuruController::class, 'autoAssign'])->name('web.guru.auto-assign');
Route::post('/guru/assign', [GuruController::class, 'assign'])->name('web.guru.assign');
Route::post('/spp/bayar/{id}', [SppController::class, 'bayar'])->name('web.spp.bayar');
Route::post('/kas/transaksi', [KasController::class, 'store'])->name('web.kas.store');
