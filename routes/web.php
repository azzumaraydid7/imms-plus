<?php

use App\Modules\Identity\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;

$webHost = parse_url((string) config('app.url'), PHP_URL_HOST) ?: (string) config('app.url');

Route::domain($webHost)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/', [LoginController::class, 'show'])->name('login');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('throttle:10,1');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
        Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    });
});
