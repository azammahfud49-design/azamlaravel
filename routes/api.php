<?php

// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExportController;

/*
|--------------------------------------------------------------------------
| API Routes - Manajemen Mahasiswa
|--------------------------------------------------------------------------
*/

// ============================================================
// 1. PUBLIC ROUTES (Tanpa Autentikasi)
// ============================================================

// Kelompok Auth (Register, Login, dll)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password',  [AuthController::class, 'resetPassword']);
});

// Fitur Export yang dipindah ke luar (Biar bisa langsung ditembak dari browser tanpa login)
Route::get('/mahasiswa/export/csv', [ExportController::class, 'exportCsv']);
Route::get('/mahasiswa/export/txt', [ExportController::class, 'exportTxt']);


// ============================================================
// 2. PROTECTED ROUTES (Wajib Login + Verified)
// ============================================================
Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // Auth - logout & user info
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });

    // Dashboard Statistik
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // CRUD Mahasiswa + Search, Sort, Filter, Import
    Route::prefix('mahasiswa')->group(function () {

        // CRUD utama
        Route::get('/',           [MahasiswaController::class, 'index']);       
        Route::post('/',          [MahasiswaController::class, 'store']);       
        Route::get('/{id}',       [MahasiswaController::class, 'show']);        
        Route::put('/{id}',       [MahasiswaController::class, 'update']);      
        Route::delete('/{id}',    [MahasiswaController::class, 'destroy']);     

        // Algoritma Search
        Route::post('/search/linear',     [MahasiswaController::class, 'linearSearch']);
        Route::post('/search/binary',     [MahasiswaController::class, 'binarySearch']);
        Route::post('/search/sequential', [MahasiswaController::class, 'sequentialSearch']);

        // Algoritma Sort
        Route::post('/sort/bubble', [MahasiswaController::class, 'bubbleSort']);
        Route::post('/sort/merge',  [MahasiswaController::class, 'mergeSort']);

        // Fitur Import (Tetap butuh login untuk keamanan database)
        Route::post('/import/csv',  [ExportController::class, 'importCsv']);
    });

    // Profile user yang login
    Route::prefix('profile')->group(function () {
        Route::get('/',         [ProfileController::class, 'show']);
        Route::put('/',         [ProfileController::class, 'update']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
    });

});