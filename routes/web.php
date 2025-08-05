<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedisTestController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Redis test routes
Route::prefix('redis')->group(function () {
    Route::get('/test', [RedisTestController::class, 'index']);
    Route::post('/set', [RedisTestController::class, 'setData']);
    Route::get('/get/{key}', [RedisTestController::class, 'getData']);
    Route::delete('/delete/{key}', [RedisTestController::class, 'deleteData']);
});

// API Routes for testing
Route::prefix('api')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now(),
            'version' => app()->version()
        ]);
    });
    
    Route::get('/redis/status', [RedisTestController::class, 'index']);
    Route::post('/redis/cache-test', [RedisTestController::class, 'testCache']);
});
