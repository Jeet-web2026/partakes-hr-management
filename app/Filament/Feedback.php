<?php

namespace App\Filament;

use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Models\CountryMaster;
use App\Models\MasterVerticalPageName;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Locked;

class Feedback extends Page
{
    use HasCompanyHeader;

    protected static ?string $navigationLabel = 'Feedback';
    protected static ?int $navigationSort = 3;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.dashboard.pages.feedback';

    public ?string $userUin = null;
    public ?string $orgaUin = null;

    public $pages = null;
    public $countryDetails = null;
    public $defaultCountryUin = null;

    // Page_UIN from ?pageId=... when it matches a page, otherwise null.
    // #[Locked] stops the browser from changing it through Livewire requests.
    #[Locked]
    public ?string $lockedPageUin = null;

    #[Locked]
    public bool $embed = false;
    #[Locked]
    public string $returnUrl = '';
    #[Locked]
    public bool $submitted = false;

    public function mount(): void
    {
        $this->userUin = Session::get('partaker_user_uin');
        $this->orgaUin = Session::get('selected_orga_uin');

        if (! $this->userUin && ! $this->orgaUin && ! $this->userData) {
            $this->redirect(route('home'));
            return;
        }

        $this->embed = request()->boolean('embed');
        $this->submitted = $this->embed && request()->boolean('submitted');
        $this->returnUrl = $this->safeReturnUrl(request()->query('return'));


        $this->pages = MasterVerticalPageName::query()
            ->select(['Page_UIN', 'Page_name'])
            ->get();

        $requested = request()->query('pageId');

        $match = $requested === null
            ? null
            : $this->pages->first(fn($p) => (string) $p->Page_UIN === (string) $requested);

        $this->lockedPageUin = $match ? (string) $match->Page_UIN : null;

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

    protected function safeReturnUrl(?string $url): string
    {
        $fallback = route('home');

        $url = $url ?: url()->previous($fallback);

        if (str_starts_with($url, '//')) {
            return $fallback;
        }

        $parts = parse_url($url);

        if ($parts === false) {
            return $fallback;
        }

        $host = $parts['host'] ?? null;

        // Absolute URL on another site
        if ($host !== null && $host !== request()->getHost()) {
            return $fallback;
        }

        // Not an absolute URL and not a site-relative path
        if ($host === null && ! str_starts_with($url, '/')) {
            return $fallback;
        }

        // Never return to the feedback page itself
        if (str_starts_with(ltrim($parts['path'] ?? '', '/'), 'feedback')) {
            return $fallback;
        }

        return $url;
    }
}
