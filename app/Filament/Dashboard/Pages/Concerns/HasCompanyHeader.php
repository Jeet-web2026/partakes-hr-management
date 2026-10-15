<?php

namespace App\Filament\Dashboard\Pages\Concerns;

use Illuminate\Support\Facades\Session;

trait HasCompanyHeader
{
    public array $userData = [];

    public ?object $companyInformation = null;

    public function bootHasCompanyHeader(): void
    {
        $this->userData = Session::get('partaker_user_data', []);
        $this->companyInformation = Session::get('company_information');
    }
}
