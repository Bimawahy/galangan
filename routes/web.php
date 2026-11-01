<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/kasir', [PosController::class, 'index'])->name('pos');
    Route::post('/kasir', [PosController::class, 'store'])->name('pos.store');

    Route::resource('produk', ProductController::class)
        ->except('show')->parameters(['produk' => 'product'])->names('products');

    Route::get('/penjualan', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/penjualan/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::post('/penjualan/{sale}/lunas', [SaleController::class, 'settle'])->name('sales.settle');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
