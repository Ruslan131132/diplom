<?php

use App\Http\Controllers\QRController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::group([
    'prefix' => 'auth',
], function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('token', 'launchApp');
    });
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('qr')->group(function () {
        Route::post('generate', [QRController::class, 'generate']);
    });
});
Route::prefix('qr')->group(function () {
    Route::post('check/{id}', [QRController::class, 'check']);
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
