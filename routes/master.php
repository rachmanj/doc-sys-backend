<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\Master\AdditionalDocumentTypeController;

Route::prefix('master')->name('master.')->group(function () {
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/cek-target', [SupplierController::class, 'cek_target'])->name('cek');
        Route::get('/import', [SupplierController::class, 'import'])->name('import');
        Route::get('/search', [SupplierController::class, 'search'])->name('search');
        Route::get('/all', [SupplierController::class, 'all'])->name('all');
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('document-types')->name('document-types.')->group(function () {
        Route::get('/all', [AdditionalDocumentTypeController::class, 'all'])->name('all');
        Route::post('/', [AdditionalDocumentTypeController::class, 'store'])->name('store');
        Route::get('/search', [AdditionalDocumentTypeController::class, 'search'])->name('search');
        Route::get('/{additionalDocumentType}', [AdditionalDocumentTypeController::class, 'show'])->name('show');
        Route::put('/{additionalDocumentType}', [AdditionalDocumentTypeController::class, 'update'])->name('update');
        Route::delete('/{additionalDocumentType}', [AdditionalDocumentTypeController::class, 'destroy'])->name('destroy');
    });
});
