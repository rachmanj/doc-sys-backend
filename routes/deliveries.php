<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Deliveries\LpdController;

Route::prefix('deliveries')->group(function () {
    Route::prefix('lpds')->group(function () {
        Route::get('all', [LpdController::class, 'all']);
        Route::get('check-duplication', [LpdController::class, 'checkDuplication']);
        Route::get('/', [LpdController::class, 'search']);
        Route::post('/', [LpdController::class, 'store']);
        Route::get('/{lpd}', [LpdController::class, 'getById']);
        Route::put('/{lpd}', [LpdController::class, 'update']);
        Route::delete('/{lpd}', [LpdController::class, 'destroy']);
    });
});
