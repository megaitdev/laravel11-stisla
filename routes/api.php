<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\BeaCukaiController;

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
