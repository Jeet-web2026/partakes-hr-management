<?php

namespace App\Events;

use App\Models\ShareLink;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShareLinkCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public ShareLink $shareLink) {}
}
