<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Native\WindowController;
use App\Modules\Identity\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;

$webHost = parse_url((string) config('app.url'), PHP_URL_HOST) ?: (string) config('app.url');

Route::domain($webHost)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/', [LoginController::class, 'show'])->name('login');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('throttle:10,1');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

        Route::prefix('window')->name('window.')->group(function () {
            Route::post('minimize', [WindowController::class, 'minimize'])->name('minimize');
            Route::post('maximize', [WindowController::class, 'maximize'])->name('maximize');
            Route::post('close', [WindowController::class, 'close'])->name('close');
        });
    });
});
