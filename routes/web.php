<?php

use App\Http\Controllers\DispatchReportController;
use App\Http\Controllers\NativeDatabaseController;
use App\Http\Controllers\ReceiptPdfController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');

    Route::prefix('clients')->name('clients.')->group(function () {
        Route::livewire('/', 'pages::clients.index')->name('index');
        Route::livewire('/create', 'pages::clients.create')->name('create');
        Route::livewire('/{client}', 'pages::clients.show')->name('show');
        Route::livewire('/{client}/edit', 'pages::clients.edit')->name('edit');
    });

    Route::prefix('receipts')->name('receipts.')->group(function () {
        Route::livewire('/', 'pages::receipts.index')->name('index');
        Route::livewire('/create', 'pages::receipts.create')->name('create');
        Route::livewire('/{receipt}', 'pages::receipts.show')->name('show');
        Route::livewire('/{receipt}/edit', 'pages::receipts.edit')->name('edit');
        Route::get('/{receipt}/pdf', [ReceiptPdfController::class, 'download'])->name('pdf');
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
    });

    Route::get('/native/database/export', [NativeDatabaseController::class, 'export'])->name('native.database.export');
});

require __DIR__.'/settings.php';
