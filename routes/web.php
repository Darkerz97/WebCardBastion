<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\SaleController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('products/template', [ProductController::class, 'template'])->name('products.template');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::resource('products', ProductController::class);

    Route::get('customers/template', [CustomerController::class, 'template'])->name('customers.template');
    Route::post('customers/import', [CustomerController::class, 'import'])->name('customers.import');
    Route::resource('customers', CustomerController::class);

    Route::get('sales/template', [SaleController::class, 'template'])->name('sales.template');
    Route::post('sales/import', [SaleController::class, 'import'])->name('sales.import');
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
});
