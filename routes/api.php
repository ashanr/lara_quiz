<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedisTestController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Redis API routes
Route::prefix('redis')->group(function () {
    Route::get('/test', [RedisTestController::class, 'index']);
    Route::post('/set', [RedisTestController::class, 'setData']);
    Route::get('/get/{key}', [RedisTestController::class, 'getData']);
    Route::delete('/delete/{key}', [RedisTestController::class, 'deleteData']);
});
