<?php

namespace App\Providers\Filament;

use App\Filament\Feedback;
use Illuminate\Support\Str;
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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CandidatemanagementPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('candidatemanagement')
            ->path('')
            ->default()
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/dashboard/theme.css')
            ->brandName('')
            ->colors([
                'primary' => Color::hex('#ffffff'),
            ])
            ->discoverResources(in: app_path('Filament/Candidatemanagement/Resources'), for: 'App\Filament\Candidatemanagement\Resources')
            ->discoverPages(in: app_path('Filament/Candidatemanagement/Pages'), for: 'App\Filament\Candidatemanagement\Pages')
            ->discoverWidgets(in: app_path('Filament/Candidatemanagement/Widgets'), for: 'App\Filament\Candidatemanagement\Widgets')
            ->pages([
                Feedback::class,
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
