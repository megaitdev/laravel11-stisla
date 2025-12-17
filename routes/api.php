<?php

use App\Http\Controllers\BillingStatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\BeaCukaiController;
use App\Http\Controllers\BillingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Bea Cukai
Route::get('clear-data/{table}/{month}/{year}', [BeaCukaiController::class, 'clearData']);
Route::get('get-data/{table}/{month}/{year}', [BeaCukaiController::class, 'getData']);

Route::post('pemasukan', [BeaCukaiController::class, 'storePemasukan']);
Route::post('pengeluaran', [BeaCukaiController::class, 'storePengeluaran']);
Route::post('wip', [BeaCukaiController::class, 'storeWip']);
Route::post('mutasi-mesin', [BeaCukaiController::class, 'storeMutasiMesin']);
Route::post('mutasi-bahan-baku', [BeaCukaiController::class, 'storeMutasiBahanBaku']);
Route::post('mutasi-barang-jadi', [BeaCukaiController::class, 'storeMutasiBarangJadi']);
Route::post('sisa-scrap', [BeaCukaiController::class, 'storeSisaScrap']);


// API TOP Ekspor

Route::get('billing-status-a', [BillingStatusController::class, 'showWithStatusA']);
Route::get('payment-status', [BillingStatusController::class, 'getPaymentStatuses']);

// Billing Routes
Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
Route::get('/notif-summary/overdue', [BillingController::class, 'notifySummaryOverdue'])->name('billing.notify.summary');
Route::get('/notif-upcoming', [BillingController::class, 'notifyUpcomingBilling'])->name('billing.notify.upcoming');
Route::get('/notif-overdue', [BillingController::class, 'notifyOverdueBilling'])->name('billing.notify.overdue');


Route::get('/notif-upcoming/{dir}', [BillingController::class, 'notifyUpcomingBilling'])->name('billing.notify.upcoming');
Route::get('/notif-overdue/{dir}', [BillingController::class, 'notifyOverdueBilling'])->name('billing.notify.overdue');

// Billing Notification Routes
Route::get('/notif-upcoming-besok', [BillingController::class, 'notifyOCBesok']);
Route::get('/notif-overdue-besok', [BillingController::class, 'notifyOCOverdueBesok']);
