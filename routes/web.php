<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');
Route::get('/tes', function () {
    return view('auth.register-backup');
})->middleware('guest');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard']);
    Route::get('/blank-page', [DashboardController::class, 'blank']);
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile-edit');
    Route::get('/profile/change-password', [ProfileController::class, 'changepassword']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'password']);
});
