<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RolePermissionController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Add this line temporarily for debugging
Log::info('API routes file is being loaded');

// Public routes
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::get('test', [TestController::class, 'test']);

    require __DIR__ . '/documents.php';
    require __DIR__ . '/deliveries.php';
    require __DIR__ . '/settings.php';
    require __DIR__ . '/master.php';
});
