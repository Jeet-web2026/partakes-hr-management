<?php

namespace App\Events;

use App\Models\AgmtDraft;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AgmtDraftCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public AgmtDraft $agmtDraft)
    {
    }
}