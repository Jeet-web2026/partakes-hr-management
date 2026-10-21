<?php

namespace App\Filament\Candidatemanagement\Pages\Common;

use App\Models\UserOrgaRela;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Locked;
use Symfony\Component\HttpFoundation\Response;

trait HasCompanyHeader
{
    #[Locked]
    public array $userData = [];

    #[Locked]
    public ?object $companyInformation = null;

    public function mountHasCompanyHeader(): void
    {
        $userUin  = Session::get('partaker_user_uin');
        $orgaUin  = Session::get('selected_orga_uin');
        $userData = Session::get('partaker_user_data');

        if (! $userUin || ! $orgaUin || ! $userData) {
            $this->redirectRoute('home');

            return;
        }

        $companyInformation = UserOrgaRela::query()
            ->where('User_UIN', $userUin)
            ->with(['organisation:Orga_UIN,Orga_Name'])
            ->get();

        if ($companyInformation->isEmpty()) {
            abort(Response::HTTP_FORBIDDEN, 'Organisation data not found.');
        }

        $this->userData = $userData;
        $this->companyInformation = $companyInformation;

        Session::put('company_information', $this->companyInformation);
        Session::put('user_data', $userData);
    }
}
