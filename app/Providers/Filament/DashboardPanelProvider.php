<?php

namespace App\Providers\Filament;

use App\Filament\Dashboard\Pages\Dashboard;
use App\Http\Middleware\EnsurePartakerSessionExists;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Str;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->sidebarCollapsibleOnDesktop()
            ->brandName('')
            ->viteTheme('resources/css/filament/dashboard/theme.css')
            ->colors([
                'primary' => Color::hex('#ffffff'),
            ])
            ->discoverResources(in: app_path('Filament/Dashboard/Resources'), for: 'App\Filament\Dashboard\Resources')
            ->discoverPages(in: app_path('Filament/Dashboard/Pages'), for: 'App\Filament\Dashboard\Pages')
            ->homeUrl(fn() => Dashboard::getUrl())
            ->discoverWidgets(in: app_path('Filament/Dashboard/Widgets'), for: 'App\Filament\Dashboard\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
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
                EnsurePartakerSessionExists::class
            ]);
    }

    public function boot(): void
    {
        View::composer('filament-panels::livewire.topbar', function ($view) {
            $view->with([
                'userData' => Session::get('partaker_user_data', []),
                'companyInformation' => Session::get('company_information'),
                'backurlforSettings' => 'https://partakers.in/dashboard',
                'title' => Str::headline(collect(explode('/', request()->path()))->last()),
            ]);
        });
    }
}
