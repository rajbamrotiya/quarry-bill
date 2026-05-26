<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('clients')->name('clients.')->group(function () {
        Route::livewire('/', 'pages::clients.index')->name('index');
        Route::livewire('/create', 'pages::clients.create')->name('create');
        Route::livewire('/{client}', 'pages::clients.show')->name('show');
        Route::livewire('/{client}/edit', 'pages::clients.edit')->name('edit');
    });
});

require __DIR__.'/settings.php';
