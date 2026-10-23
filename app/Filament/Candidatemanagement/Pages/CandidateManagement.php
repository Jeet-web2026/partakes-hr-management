<?php

namespace App\Filament\Candidatemanagement\Pages;

use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;

class CandidateManagement extends Page
{
    use HasCompanyHeader;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;
    protected static ?string $navigationLabel = 'Candidate Management';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.candidatemanagement.pages.candidate-management';
    protected static ?string $slug = 'candidate-management';


    public string $userUin;
    public string $orgaUin;

    public string $statusFilter = '';
    public ?string $statusDate = null;

    public function mount(): void
    {
        $this->userUin = Session::get('partaker_user_uin');
        $this->orgaUin = Session::get('selected_orga_uin');

        if (! $this->userUin && ! $this->orgaUin && ! $this->userData) {
            $this->redirect(route('home'));
        }
    }
    #[Computed]
    public function getStatuses(): array
    {
        return [
            'new_candidate'       => 'New Candidate',
            'short_listed'        => 'Candidate Short Listed',
            'interview_scheduled' => 'Schedule Interview',
            'selected'            => 'Candidate Selected',
            'offer_issued'        => 'Offer Letter Issued',
            'offer_accepted'      => 'Offer Accepted',
        ];
    }

    public function updatedStatusFilter(): void
    {
        $this->statusDate = null;
    }
}
