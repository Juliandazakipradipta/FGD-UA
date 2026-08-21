<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\MinuteController;
use App\Http\Controllers\PublicMinuteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik (tanpa login) - siapapun bisa mengisi notulensi
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicMinuteController::class, 'create'])->name('home');

Route::post('/notulensi', [PublicMinuteController::class, 'store'])
    ->middleware('throttle:120,1') // Diperluas hingga 120 submit/menit agar aman untuk 50+ peserta dalam 1 jaringan Wi-Fi
    ->name('notulensi.store');

Route::get('/notulensi/berhasil', [PublicMinuteController::class, 'success'])
    ->name('notulensi.success');

/*
|--------------------------------------------------------------------------
| Rute Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));

    // Guest admin (belum login)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:30,1')
            ->name('login.attempt');
    });

    // Admin yang sudah login (fail-safe authentication check)
    Route::middleware(\App\Http\Middleware\EnsureAdminAuth::class)->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/notulensi', [MinuteController::class, 'index'])->name('minutes.index');
        Route::get('/notulensi/export', [MinuteController::class, 'export'])->name('minutes.export');
        Route::get('/notulensi/{id}', [MinuteController::class, 'show'])->name('minutes.show');
        Route::post('/notulensi/{id}/delete', [MinuteController::class, 'destroy'])->name('minutes.destroy');

        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
        Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
        Route::post('/groups/{id}/delete', [GroupController::class, 'destroy'])->name('groups.destroy');
    });
});
