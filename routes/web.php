<?php

use App\Http\Controllers\BuyReceiptPdfController;
use App\Http\Controllers\BuyReportController;
use App\Http\Controllers\DispatchReportController;
use App\Http\Controllers\ReceiptPdfController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');
    Route::livewire('buy-dashboard', 'pages::buy-dashboard')->name('buy-dashboard');

    Route::prefix('users')->name('users.')->group(function () {
        Route::livewire('/', 'pages::users.index')->name('index');
        Route::livewire('/create', 'pages::users.create')->name('create');
        Route::livewire('/{user}/edit', 'pages::users.edit')->name('edit');
    });

    Route::prefix('clients')->name('clients.')->group(function () {
        Route::livewire('/', 'pages::clients.index')->name('index');
        Route::livewire('/create', 'pages::clients.create')->name('create');
        Route::livewire('/{client}', 'pages::clients.show')->name('show');
        Route::livewire('/{client}/edit', 'pages::clients.edit')->name('edit');
    });

    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::livewire('/', 'pages::suppliers.index')->name('index');
        Route::livewire('/create', 'pages::suppliers.create')->name('create');
        Route::livewire('/{supplier}', 'pages::suppliers.show')->name('show');
        Route::livewire('/{supplier}/edit', 'pages::suppliers.edit')->name('edit');
    });

    Route::prefix('receipts')->name('receipts.')->group(function () {
        Route::livewire('/', 'pages::receipts.index')->name('index');
        Route::livewire('/create', 'pages::receipts.create')->name('create');
        Route::livewire('/{receipt}', 'pages::receipts.show')->name('show');
        Route::livewire('/{receipt}/edit', 'pages::receipts.edit')->name('edit');
        Route::get('/{receipt}/pdf', [ReceiptPdfController::class, 'download'])->name('pdf');
    });

    Route::prefix('buy-receipts')->name('buy-receipts.')->group(function () {
        Route::livewire('/', 'pages::buy_receipts.index')->name('index');
        Route::livewire('/create', 'pages::buy_receipts.create')->name('create');
        Route::livewire('/{buy_receipt}', 'pages::buy_receipts.show')->name('show');
        Route::livewire('/{buy_receipt}/edit', 'pages::buy_receipts.edit')->name('edit');
        Route::get('/{buy_receipt}/pdf', [BuyReceiptPdfController::class, 'download'])->name('pdf');
    });

    Route::prefix('material-types')->name('material-types.')->group(function () {
        Route::livewire('/', 'pages::material-types.index')->name('index');
        Route::livewire('/create', 'pages::material-types.create')->name('create');
        Route::livewire('/{materialType}/edit', 'pages::material-types.edit')->name('edit');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::livewire('/', 'pages::reports.index')->name('index');
        Route::get('/daily-pdf', [DispatchReportController::class, 'daily'])->name('daily-pdf');
        Route::get('/monthly-pdf', [DispatchReportController::class, 'monthly'])->name('monthly-pdf');
        Route::get('/client-material-summary-pdf', [DispatchReportController::class, 'clientMaterialSummary'])->name('client-material-summary-pdf');
    });

    Route::prefix('buy-reports')->name('buy-reports.')->group(function () {
        Route::livewire('/', 'pages::buy_reports.index')->name('index');
        Route::get('/daily-pdf', [BuyReportController::class, 'daily'])->name('daily-pdf');
        Route::get('/monthly-pdf', [BuyReportController::class, 'monthly'])->name('monthly-pdf');
        Route::get('/supplier-material-summary-pdf', [BuyReportController::class, 'supplierMaterialSummary'])->name('supplier-material-summary-pdf');
        Route::get('/vehicle-summary-pdf', [BuyReportController::class, 'monthlyVehicleSummary'])->name('vehicle-summary-pdf');
        Route::get('/monthly-vehicle-report-pdf', [BuyReportController::class, 'monthlyVehicleReport'])->name('monthly-vehicle-report-pdf');
        Route::get('/daily-vehicle-summary-pdf', [BuyReportController::class, 'dailyVehicleSummary'])->name('daily-vehicle-summary-pdf');
    });
});

require __DIR__.'/settings.php';
