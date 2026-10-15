<?php

namespace App\Filament\Dashboard\Pages;

use App\Filament\Dashboard\Pages\Concerns\HasCompanyHeader;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Session;

class Agreements extends Page
{
    use HasCompanyHeader;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Agreements';
    protected static ?int $navigationSort = 2;
     protected string $view = 'filament.dashboard.pages.agreements';
    protected static ?string $slug = 'agreements';
    

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