<?php

namespace App\Filament\Settings\Pages;

use App\Filament\Dashboard\Pages\Concerns\HasCompanyHeader;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Session;

class Settings extends Page
{
    use HasCompanyHeader;

    protected static ?string $navigationLabel = 'Agreement Name';

    protected static ?int $navigationSort =13;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.settings.pages.settings';

    public string $userUin;
    public string $orgaUin;

    public function mount(): void
    {
        $this->userUin = Session::get('partaker_user_uin');
        $this->orgaUin = Session::get('selected_orga_uin');

        if (! $this->userUin || ! $this->orgaUin || ! $this->userData) {
            $this->redirect(route('home'));
        }
    }
}
