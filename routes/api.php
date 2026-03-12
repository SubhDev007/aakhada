<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BetController;
use App\Http\Controllers\Admin\GameController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    // User Game Routes
    Route::post('/bet', [BetController::class, 'place']);
    Route::get('/history', [BetController::class, 'history']);

    // Admin Routes
    Route::prefix('admin')->middleware(['role:superadmin,bookman'])->group(function () {
        Route::get('/current-round', [GameController::class, 'currentRound']);
        Route::post('/set-result', [GameController::class, 'setResult'])->middleware('role:superadmin'); // Only superadmin can set result? logic says admin
        Route::match(['get', 'post'], '/settings', [GameController::class, 'settings'])->middleware('role:superadmin');
    });
});
