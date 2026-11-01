<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});

// admin = operasional (kasir, produk), owner = pantau laporan (read-only), master = semua + cek sistem
Route::middleware(['auth', 'role:admin,owner,master'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/penjualan', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/penjualan/{sale}', [SaleController::class, 'show'])->name('sales.show');
});

Route::middleware(['auth', 'role:admin,master'])->group(function () {
    Route::get('/kasir', [PosController::class, 'index'])->name('pos');
    Route::post('/kasir', [PosController::class, 'store'])->name('pos.store');

    Route::get('/surat-jalan', [SaleController::class, 'suratJalanIndex'])->name('delivery.index');
    Route::get('/surat-jalan/{sale}', [SaleController::class, 'suratJalan'])->name('delivery.show');

    Route::resource('produk', ProductController::class)
        ->except('show')->parameters(['produk' => 'product'])->names('products');

    Route::post('/penjualan/{sale}/lunas', [SaleController::class, 'settle'])->name('sales.settle');
});

// Cek role asli (bukan "view as") ada di MasterController.
Route::middleware('auth')->prefix('master')->controller(MasterController::class)->group(function () {
    Route::get('/', 'index')->name('master');
    Route::post('/lihat-sebagai/{role}', 'viewAs')->whereIn('role', ['admin', 'owner', 'master'])->name('master.view');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
