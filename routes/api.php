<?php

use App\Modules\Identity\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

$apiHost = parse_url((string) config('app.api_url'), PHP_URL_HOST) ?: (string) config('app.api_url');

Route::domain($apiHost)->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
