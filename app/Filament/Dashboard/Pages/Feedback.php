<?php

namespace App\Filament\Dashboard\Pages;

use App\Filament\Dashboard\Pages\Concerns\HasCompanyHeader;
use App\Models\CountryMaster;
use App\Models\MasterVerticalPageName;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class Feedback extends Page
{
    use HasCompanyHeader;

    protected static ?string $navigationLabel = 'Feedback';

    protected static ?int $navigationSort = 3;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected string $view = 'filament.dashboard.pages.feedback';

    public string $userUin;
    public string $orgaUin;

    public $pages = null;
    public $countryDetails = null;
    public $defaultCountryUin = null;

    public function mount(): void
    {
        $this->userUin = Session::get('partaker_user_uin');
        $this->orgaUin = Session::get('selected_orga_uin');

        if (! $this->userUin || ! $this->orgaUin || ! $this->userData) {
            $this->redirect(route('home'));
        }

        $this->pages = MasterVerticalPageName::query()
            ->select(['Page_UIN', 'Page_name'])
            ->get();

        $this->countryDetails = Cache::remember('country_details_list', now()->addHours(20), function () {
            return CountryMaster::query()
                ->whereNotNull('Flag_Emoj')
                ->select(['Admn_Cutr_Mast_UIN', 'Name', 'Code', 'Flag_Emoj'])
                ->orderBy('Name')
                ->get();
        });

        $this->defaultCountryUin = $this->countryDetails
            ->firstWhere('Code', 'IN')
            ?->Admn_Cutr_Mast_UIN;
    }
}
