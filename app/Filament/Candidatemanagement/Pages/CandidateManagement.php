<?php

namespace App\Filament\Candidatemanagement\Pages;

use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Session;

class CandidateManagement extends Page
{
    use HasCompanyHeader;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Candidate Management';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.candidatemanagement.pages.candidate-management';
    protected static ?string $slug = 'candidate-management';


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
