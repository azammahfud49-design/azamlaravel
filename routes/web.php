<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;

use  App\Http\Controllers\Api\ExportController;

Route::get('/export/csv', [ExportController::class, 'exportCsv']);
Route::get('/export/txt', [ExportController::class, 'exportTxt']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/verify-email/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return redirect('/verify-result?verification=error&message=' . urlencode('Link verifikasi tidak valid'));
    }

    if ($user->hasVerifiedEmail()) {
        return redirect('/verify-result?verification=already&message=' . urlencode('Email sudah diverifikasi'));
    }

    $user->markEmailAsVerified();
    event(new \Illuminate\Auth\Events\Verified($user));

    return redirect('/verify-result?verification=success&message=' . urlencode('Email berhasil diverifikasi! Silakan login.'));
})->name('verification.verify');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
