<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Facades\Menu;
use Native\Desktop\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        if (! app()->environment('testing') && Schema::hasTable('users') && User::count() === 0) {
            Artisan::call('db:seed', ['--force' => true]);
        }

        Menu::create(
            Menu::app(),
            Menu::file(),
            Menu::edit(),
            Menu::view(),
            Menu::window(),
            Menu::make(
                Menu::route('dashboard', 'Dashboard'),
                Menu::separator(),
                Menu::route('clients.index', 'Clients'),
                Menu::route('receipts.index', 'Receipts'),
                Menu::route('material-types.index', 'Material Types'),
                Menu::route('reports.index', 'Reports'),
                Menu::separator(),
                Menu::route('native.database.export', 'Export Database'),
            )->label('Navigation')
        );

        Window::open()
            ->width(1200)
            ->height(800)
            ->minWidth(800)
            ->minHeight(600)
            ->title(config('app.name'))
            ->rememberState();
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
