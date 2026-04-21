<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Configure Filament colors
        Filament::registerTheme(
            [
                'primary' => Color::hex('#D4AF37'),
                'secondary' => Color::hex('#1E3A8A'),
                'success' => Color::hex('#10B981'),
                'warning' => Color::hex('#F59E0B'),
                'danger' => Color::hex('#EF4444'),
                'info' => Color::hex('#3B82F6'),
                'gray' => Color::hex('#64748B'),
            ]
        );

        // Configure brand
        Filament::brandLogo(asset('images/logo-dark.svg'));
        Filament::brandLogoHeight('2rem');
        Filament::brandName('HF Finance');
        Filament::favicon(asset('favicon.ico'));

        // Configure theme
        Filament::darkMode(true);
        Filament::sidebarCollapsibleOnDesktop();
    }
}
