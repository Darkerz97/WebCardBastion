<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::prefix('sync')->group(function (): void {
    Route::post('/heartbeat', [SyncController::class, 'heartbeat']);
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('products', ProductController::class)->names('api.products');
    Route::patch('products/{product}/stock', [ProductController::class, 'updateStock']);

    Route::apiResource('customers', CustomerController::class)->names('api.customers');
    Route::apiResource('devices', DeviceController::class)->names('api.devices');

    Route::apiResource('sales', SaleController::class)->only(['index', 'store', 'show'])->names('api.sales');
    Route::post('sales/{sale}/payments', [SaleController::class, 'addPayment']);

    Route::prefix('sync')->group(function (): void {
        Route::get('/products', [SyncController::class, 'products']);
        Route::get('/customers', [SyncController::class, 'customers']);
        Route::post('/upload-sales', [SyncController::class, 'uploadSales']);
    });
});
