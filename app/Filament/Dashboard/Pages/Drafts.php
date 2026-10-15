<?php

namespace App\Filament\Dashboard\Pages;

use App\Filament\Dashboard\Pages\Concerns\HasCompanyHeader;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Session;

class Drafts extends Page
{
    use HasCompanyHeader;

    protected static ?string $navigationLabel = 'Draft Agreement';

    protected static ?int $navigationSort = 3;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document';

    protected string $view = 'filament.dashboard.pages.drafts';

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
