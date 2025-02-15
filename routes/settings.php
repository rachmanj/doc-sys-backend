<?php

use App\Http\Controllers\Auth\RolePermissionController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('roles')->group(function () {
    Route::get('/', [RolePermissionController::class, 'getRoles']);
    Route::post('/', [RolePermissionController::class, 'createRole']);
    Route::put('/{id}', [RolePermissionController::class, 'updateRole']);
    Route::delete('/{id}', [RolePermissionController::class, 'deleteRole']);
});

Route::prefix('permissions')->group(function () {
    Route::get('/', [RolePermissionController::class, 'getPermissions']);
    Route::get('/all', [RolePermissionController::class, 'getAllPermissions']);
    Route::post('/', [RolePermissionController::class, 'createPermission']);
    Route::put('/{id}', [RolePermissionController::class, 'updatePermission']);
    Route::delete('/{id}', [RolePermissionController::class, 'deletePermission']);
});

// User roles and permissions
Route::get('users/{user}/roles', [RolePermissionController::class, 'getUserRolesAndPermissions']);
Route::post('users/{user}/roles', [RolePermissionController::class, 'assignRoleToUser']);

// User Management Routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/all', [UserController::class, 'getAllUsers']);
    Route::get('/{user}', [UserController::class, 'show']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{user}', [UserController::class, 'update']);
    Route::delete('/{user}', [UserController::class, 'destroy']);
    
    // Role management routes (moving existing routes here for better organization)
    Route::get('/{user}/roles', [RolePermissionController::class, 'getUserRolesAndPermissions']);
    Route::post('/{user}/roles', [RolePermissionController::class, 'assignRoleToUser']);
});

// Department Management Routes
Route::prefix('departments')->group(function () {
    Route::get('/', [DepartmentController::class, 'index']);
    Route::get('/all', [DepartmentController::class, 'getAllDepartments']);
    Route::post('/', [DepartmentController::class, 'store']);
    Route::get('/{department}', [DepartmentController::class, 'show']);
    Route::put('/{department}', [DepartmentController::class, 'update']);
    Route::delete('/{department}', [DepartmentController::class, 'destroy']);
});
