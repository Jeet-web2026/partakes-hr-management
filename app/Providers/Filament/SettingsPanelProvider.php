<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\EnsurePartakerSessionExists;

class SettingsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('settings')
            ->path('settings')
            ->sidebarCollapsibleOnDesktop()
            ->brandName('')
            ->viteTheme('resources/css/filament/dashboard/theme.css')
            ->colors([
                'primary' => Color::hex('#ffffff'),
            ])
            ->discoverResources(in: app_path('Filament/Settings/Resources'), for: 'App\Filament\Settings\Resources')
            ->discoverPages(in: app_path('Filament/Settings/Pages'), for: 'App\Filament\Settings\Pages')
            ->discoverWidgets(in: app_path('Filament/Settings/Widgets'), for: 'App\Filament\Settings\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                EnsurePartakerSessionExists::class,
            ]);
    }
}
