<?php

// routes/api.php
// Tambahkan atau sesuaikan dengan routes yang sudah ada

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\LocationapiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — NusantaraMap v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Auth (tanpa token) ────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('login',    [AuthApiController::class, 'login']);
        Route::post('register', [AuthApiController::class, 'register']);
    });

    // ── Protected routes (butuh Bearer token) ────────────────
    Route::middleware('auth:api')->group(function () {

        // Auth
        Route::post('auth/logout', [AuthApiController::class, 'logout']);
        Route::get('auth/me',      [AuthApiController::class, 'me']);

        // Profile
        Route::put('profile', [AuthApiController::class, 'updateProfile']);

        // Locations
        Route::get('locations',         [LocationApiController::class, 'index']);
        Route::get('locations/geojson', [LocationApiController::class, 'geojson']);
        Route::get('locations/radius',  [LocationApiController::class, 'radius']);

        // ★ MY UPLOADS — untuk kolom "UPLOAD" di profil
        Route::get('locations/my',      [LocationApiController::class, 'myLocations']);

        Route::post('locations',        [LocationApiController::class, 'store']);
        Route::get('locations/{location}',    [LocationApiController::class, 'show']);
        Route::delete('locations/{location}', [LocationApiController::class, 'destroy']);

        // ★ BOOKMARKS — untuk kolom "TERSIMPAN" di profil
        Route::get('bookmarks',                              [BookmarkController::class, 'index']);
        Route::post('locations/{location}/bookmark',         [BookmarkController::class, 'store']);
        Route::delete('locations/{location}/bookmark',       [BookmarkController::class, 'destroy']);
    });
});
