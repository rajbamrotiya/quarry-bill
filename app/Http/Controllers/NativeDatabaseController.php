<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Native\Desktop\Dialog;
use Native\Desktop\Facades\Notification;

class NativeDatabaseController extends Controller
{
    public function export()
    {
        // NativePHP uses 'nativephp' connection in production, fallback to 'sqlite' in development
        $currentDbPath = config('database.connections.nativephp.database')
                        ?? config('database.connections.sqlite.database');

        if (! File::exists($currentDbPath)) {
            Notification::title('Export Failed')
                ->message('Database file not found at: '.$currentDbPath)
                ->show();

            return redirect()->back();
        }

        $targetPath = Dialog::new()
            ->title('Export Database')
            ->defaultPath('database_backup.sqlite')
            ->filter('SQLite Database', ['sqlite'])
            ->save();

        if ($targetPath) {
            File::copy($currentDbPath, $targetPath);

            Notification::title('Database Exported')
                ->message('The database has been saved to: '.$targetPath)
                ->show();
        }

        return redirect()->back();
    }
}
