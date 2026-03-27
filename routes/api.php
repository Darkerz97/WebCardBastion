<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'api.role:admin,manager,cashier'])->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::get('/me', [AuthController::class, 'me']);

    Route::get('products', [ProductController::class, 'index'])->name('api.products.index')->middleware('api.ability:products:read');
    Route::post('products', [ProductController::class, 'store'])->name('api.products.store')->middleware('api.ability:products:write');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('api.products.show')->middleware('api.ability:products:read');
    Route::match(['put', 'patch'], 'products/{product}', [ProductController::class, 'update'])->name('api.products.update')->middleware('api.ability:products:write');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('api.products.destroy')->middleware('api.ability:products:write');
    Route::patch('products/{product}/stock', [ProductController::class, 'updateStock'])->middleware('api.ability:stock:write');

    Route::get('categories', [CategoryController::class, 'index'])->name('api.categories.index')->middleware('api.ability:categories:read');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('api.categories.show')->middleware('api.ability:categories:read');

    Route::get('customers', [CustomerController::class, 'index'])->name('api.customers.index')->middleware('api.ability:customers:read');
    Route::post('customers', [CustomerController::class, 'store'])->name('api.customers.store')->middleware('api.ability:customers:write');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('api.customers.show')->middleware('api.ability:customers:read');
    Route::match(['put', 'patch'], 'customers/{customer}', [CustomerController::class, 'update'])->name('api.customers.update')->middleware('api.ability:customers:write');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('api.customers.destroy')->middleware('api.ability:customers:write');

    Route::get('devices', [DeviceController::class, 'index'])->name('api.devices.index')->middleware('api.ability:devices:read');
    Route::post('devices', [DeviceController::class, 'store'])->name('api.devices.store')->middleware('api.ability:devices:write');
    Route::get('devices/{device}', [DeviceController::class, 'show'])->name('api.devices.show')->middleware('api.ability:devices:read');
    Route::match(['put', 'patch'], 'devices/{device}', [DeviceController::class, 'update'])->name('api.devices.update')->middleware('api.ability:devices:write');
    Route::delete('devices/{device}', [DeviceController::class, 'destroy'])->name('api.devices.destroy')->middleware('api.ability:devices:write');

    Route::get('sales', [SaleController::class, 'index'])->name('api.sales.index')->middleware('api.ability:sales:read');
    Route::post('sales', [SaleController::class, 'store'])->name('api.sales.store')->middleware('api.ability:sales:write');
    Route::get('sales/{sale}', [SaleController::class, 'show'])->name('api.sales.show')->middleware('api.ability:sales:read');
    Route::post('sales/{sale}/payments', [SaleController::class, 'addPayment'])->middleware('api.ability:payments:write');

    Route::prefix('sync')->group(function (): void {
        Route::post('/heartbeat', [SyncController::class, 'heartbeat'])->middleware('api.ability:sync:heartbeat');
        Route::get('/catalog', [SyncController::class, 'catalog'])->middleware('api.ability:sync:read');
        Route::get('/products', [SyncController::class, 'products'])->middleware('api.ability:sync:read');
        Route::get('/customers', [SyncController::class, 'customers'])->middleware('api.ability:sync:read');
        Route::post('/upload-sales', [SyncController::class, 'uploadSales'])->middleware('api.ability:sync:upload-sales');
    });
});
