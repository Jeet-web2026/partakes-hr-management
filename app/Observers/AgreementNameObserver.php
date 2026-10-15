<?php

namespace App\Observers;

use App\Events\AgreementNameCreated;
use App\Events\AgreementNameDeleted;
use App\Events\AgreementNameUpdated;
use App\Models\AgreementName;

class AgreementNameObserver
{
    public function created(AgreementName $agreementName): void
    {
        event(new AgreementNameCreated($agreementName));
    }

    public function updated(AgreementName $agreementName): void
    {
        event(new AgreementNameUpdated($agreementName));
    }

    public function deleted(AgreementName $agreementName): void
    {
        event(new AgreementNameDeleted($agreementName));
    }
}