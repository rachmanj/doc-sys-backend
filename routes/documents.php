<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Documents\InvoiceController;
use App\Http\Controllers\Documents\AdditionalDocumentController;

Route::prefix('documents')->name('documents.')->group(function () {
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('search', [InvoiceController::class, 'search'])->name('search');
        Route::get('check-duplication', [InvoiceController::class, 'checkDuplication'])->name('checkDuplication');
        Route::get('/get-by-id', [InvoiceController::class, 'getById'])->name('getById');
        Route::post('store', [InvoiceController::class, 'store'])->name('store');
        Route::put('update/{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('additional-documents')->name('additional-documents.')->group(function () {
        Route::get('get-by-po', [AdditionalDocumentController::class, 'getByPo'])->name('getByPo');
        Route::get('/search', [AdditionalDocumentController::class, 'search'])->name('search');
        Route::get('{additionalDocument}', [AdditionalDocumentController::class, 'getById'])->name('getById');
        Route::post('store', [AdditionalDocumentController::class, 'store'])->name('store');
        Route::put('{additionalDocument}', [AdditionalDocumentController::class, 'update'])->name('update');
        Route::delete('{additionalDocument}', [AdditionalDocumentController::class, 'destroy'])->name('destroy');

        Route::get('get-by-invoice', [AdditionalDocumentController::class, 'getByInvoice'])->name('getByInvoice');
    });
});
