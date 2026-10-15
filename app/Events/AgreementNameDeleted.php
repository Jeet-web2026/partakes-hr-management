<?php

namespace App\Events;

use App\Models\AgreementName;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AgreementNameDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public AgreementName $agreementName,
    ) {}
}