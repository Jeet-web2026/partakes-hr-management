<?php

namespace App\Filament\Dashboard\Pages;

use App\Filament\Dashboard\Pages\Concerns\HasCompanyHeader;
use App\Services\Dashboard\DashboardManagementService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Session;

class Dashboard extends Page
{
    use HasCompanyHeader;

    // Icon shown next to the nav label
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    // Label shown in the left menu
    protected static ?string $navigationLabel = 'Dashboard';

    // Negative sort pins it above every other nav item (default items are usually 0+)
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.dashboard.pages.dashboard';

    // protected static ?string $slug = '';

    public string $userUin;
    public string $orgaUin;

    protected DashboardManagementService $dashboardService;

    /**
     * Livewire calls boot() on every request (including AJAX component updates),
     * so this is the right place to resolve the service via DI rather than mount().
     */
    public function boot(DashboardManagementService $dashboardService): void
    {
        $this->dashboardService = $dashboardService;
    }

    public function mount(): void
    {
        $this->userUin = Session::get('partaker_user_uin');
        $this->orgaUin = Session::get('selected_orga_uin');

        if (! $this->userUin || ! $this->orgaUin || ! $this->userData) {
            $this->redirect(route('home'));
        }
    }

    /**
     * Live tile counts for the logged-in organization, pulled from the service.
     */
    public function getTiles(): array
    {
        return $this->dashboardService->getTiles($this->orgaUin);
    }

    /**
     * Short review copy shown when a tile is clicked. Static text for now —
     * move into the service too if this ever needs to vary by org or month.
     */
    public function getTileDetails(): array
    {
        return $this->dashboardService->getTileDetails();
    }
}
