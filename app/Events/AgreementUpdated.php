<?php

namespace App\Events;

use App\Models\Agreement;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AgreementUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Agreement $agreement,
        public array $originalAttributes = []
    ) {}
}
