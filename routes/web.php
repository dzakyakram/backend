<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ModerasiController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Pengelola\PengelolaDashboardController;
use App\Http\Controllers\Pengelola\PengelolaLokasiController;
use App\Http\Controllers\Pengelola\PengelolaModerasiController;

// ─── Root redirect ───────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ─── Auth ────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─── Admin routes ────────────────────────────────────────────
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen User
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle-status');

    // Manajemen Lokasi
    Route::resource('locations', LocationController::class);
    Route::patch('locations/{location}/approve', [LocationController::class, 'approve'])
        ->name('locations.approve');
    Route::patch('locations/{location}/reject',  [LocationController::class, 'reject'])
        ->name('locations.reject');

    // Moderasi
    Route::get('/moderasi',           [ModerasiController::class, 'index'])->name('moderasi.index');
    Route::patch('/moderasi/{id}/approve', [ModerasiController::class, 'approve'])->name('moderasi.approve');
    Route::patch('/moderasi/{id}/reject',  [ModerasiController::class, 'reject'])->name('moderasi.reject');

    // Laporan & Export
    Route::get('/laporan',        [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-csv',     [LaporanController::class, 'exportCsv'])->name('laporan.csv');
    Route::get('/laporan/export-geojson', [LaporanController::class, 'exportGeoJson'])->name('laporan.geojson');

    // Peta Admin
    Route::get('/peta', [DashboardController::class, 'peta'])->name('peta');
});

// ─── Pengelola routes ─────────────────────────────────────────
Route::prefix('pengelola')
    ->middleware(['auth', 'role:pengelola,admin'])
    ->name('pengelola.')
    ->group(function () {

    Route::get('/dashboard', [PengelolaDashboardController::class, 'index'])->name('dashboard');

    // Kelola lokasi (approve/reject)
    Route::get('/lokasi',             [PengelolaLokasiController::class, 'index'])->name('lokasi.index');
    Route::get('/lokasi/{id}',        [PengelolaLokasiController::class, 'show'])->name('lokasi.show');
    Route::patch('/lokasi/{id}/approve', [PengelolaLokasiController::class, 'approve'])->name('lokasi.approve');
    Route::patch('/lokasi/{id}/reject',  [PengelolaLokasiController::class, 'reject'])->name('lokasi.reject');

    // Moderasi antrian
    Route::get('/moderasi',           [PengelolaModerasiController::class, 'index'])->name('moderasi.index');
    Route::post('/moderasi/{id}',     [PengelolaModerasiController::class, 'process'])->name('moderasi.process');
});
