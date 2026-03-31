<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


Route::get('/clear-all', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return "All cache cleared successfully!";
})->name('clear-all');
Route::get('/', [App\Http\Controllers\GameViewController::class, 'index'])->name('game.index');



Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring', [App\Http\Controllers\Admin\AdminDashboardController::class, 'monitoring'])->name('monitoring');
    Route::get('/bets', [App\Http\Controllers\Admin\AdminDashboardController::class, 'bets'])->name('bets.index');
    Route::get('/settings', [App\Http\Controllers\Admin\AdminDashboardController::class, 'settings'])->name('settings.edit');
    Route::post('/settings', [App\Http\Controllers\Admin\AdminDashboardController::class, 'updateSettings'])->name('settings.update');
    Route::post('/set-result', [App\Http\Controllers\Admin\GameController::class, 'setResult'])->name('set-result');
    Route::post('/process-round', [App\Http\Controllers\Admin\GameController::class, 'processManually'])->name('process');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
        Route::post('/{user}/add-funds', [App\Http\Controllers\Admin\UserController::class, 'addFunds'])->name('add-funds');
    });

    Route::prefix('bookmen')->name('bookmen.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\BookmanController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\BookmanController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\BookmanController::class, 'store'])->name('store');
        Route::post('/{bookman}/add-funds', [App\Http\Controllers\Admin\BookmanController::class, 'addFunds'])->name('add-funds');
    });

    Route::prefix('round-schedules')->name('round-schedules.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\RoundScheduleController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\RoundScheduleController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\RoundScheduleController::class, 'store'])->name('store');
        Route::get('/{roundSchedule}/edit', [App\Http\Controllers\Admin\RoundScheduleController::class, 'edit'])->name('edit');
        Route::put('/{roundSchedule}', [App\Http\Controllers\Admin\RoundScheduleController::class, 'update'])->name('update');
        Route::post('/{roundSchedule}/toggle', [App\Http\Controllers\Admin\RoundScheduleController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{roundSchedule}', [App\Http\Controllers\Admin\RoundScheduleController::class, 'destroy'])->name('destroy');
    });

    Route::get('/settlements', [App\Http\Controllers\Admin\SettlementController::class, 'index'])->name('settlements.index');
});

Route::middleware(['auth', 'role:bookman'])->prefix('bookman')->name('bookman.')->group(function () {
    Route::get('/', [App\Http\Controllers\Bookman\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Bookman\UserController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Bookman\UserController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Bookman\UserController::class, 'store'])->name('store');
        Route::post('/{user}/add-funds', [App\Http\Controllers\Bookman\UserController::class, 'addFunds'])->name('add-funds');
    });

    Route::get('/report', [App\Http\Controllers\Bookman\ReportController::class, 'index'])->name('report');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/game/bet', [App\Http\Controllers\Api\BetController::class, 'place'])->name('game.bet');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
