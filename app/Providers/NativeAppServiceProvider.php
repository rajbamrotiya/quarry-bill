<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Facades\Menu;
use Native\Desktop\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Menu::create(
            Menu::app(),
            Menu::make(
                Menu::route('receipts.create', 'New Receipt', 'CmdOrCtrl+N'),
                Menu::route('clients.create', 'New Client', 'CmdOrCtrl+Shift+C'),
                Menu::separator(),
                Menu::quit()
            )->label('File'),
            Menu::make(
                Menu::route('dashboard', 'Dashboard', 'CmdOrCtrl+D'),
                Menu::route('receipts.index', 'Receipts', 'CmdOrCtrl+R'),
                Menu::route('clients.index', 'Clients', 'CmdOrCtrl+C'),
                Menu::route('reports.index', 'Reports', 'CmdOrCtrl+P'),
                Menu::separator(),
                Menu::fullscreen()
            )->label('View'),
            Menu::window(),
            Menu::make(
                Menu::link('https://laravel.com/docs', 'Documentation')
            )->label('Help')
        );

        Window::open()
            ->width(1280)
            ->height(800)
            ->minWidth(1024)
            ->minHeight(768)
            ->title('Quarry Billing System')
            ->showDevTools(false);
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '512M',
            'display_errors' => '1',
            'error_reporting' => E_ALL,
        ];
    }
}
