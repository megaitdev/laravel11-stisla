<?php

use App\Http\Controllers\BeaCukaiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\VerifikasiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Auth::routes();

Route::get('get-data/{table}/{month}/{year}', [BeaCukaiController::class, 'getData']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard']);

    // Profile Features
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/profile/tab/{tab}', [ProfileController::class, 'setTabActive']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);


    Route::get('/profile/verifikasi-nomor/{user:id}', [VerifikasiController::class, 'verifikasiNomor']);
    Route::get('/profile/ajax/verifikasi-nomor/send-code/{user:id}', [VerifikasiController::class, 'sendCodeNomor']);
    Route::get('/profile/ajax/verifikasi-nomor/resend-code/{user:id}', [VerifikasiController::class, 'resendCodeNomor']);
    Route::get('/profile/ajax/verifikasi-nomor/verified/{user:id}', [VerifikasiController::class, 'verifiedNomor']);
    Route::get('/profile/ajax/verifikasi-nomor/is-verified/{user:id}', [VerifikasiController::class, 'isVerifiedNomor']);

    Route::get('/profile/verifikasi-email/{user:id}', [VerifikasiController::class, 'verifikasiEmail']);
    Route::get('/profile/ajax/verifikasi-email/send-code/{user:id}', [VerifikasiController::class, 'sendCodeEmail']);
    Route::get('/profile/ajax/verifikasi-email/resend-code/{user:id}', [VerifikasiController::class, 'resendCodeEmail']);
    Route::get('/profile/ajax/verifikasi-email/verified/{user:id}', [VerifikasiController::class, 'verifiedEmail']);
    Route::get('/profile/ajax/verifikasi-email/is-verified/{user:id}', [VerifikasiController::class, 'isVerifiedEmail']);

    Route::get('/mail', [EmailController::class, 'sendEmail']);
});
