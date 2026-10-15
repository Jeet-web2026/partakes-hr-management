<?php

namespace App\Observers;

use App\Events\ShareLinkCreated;
use App\Events\ShareLinkDeleted;
use App\Events\ShareLinkUpdated;
use App\Models\ShareLink;

class ShareLinkObserver
{
    public function created(ShareLink $shareLink): void
    {
        ShareLinkCreated::dispatch($shareLink);
    }

    public function updated(ShareLink $shareLink): void
    {
        // Skip logging when only timestamps changed (e.g. a touch() with no real update)
        if (! $shareLink->wasChanged(array_diff(array_keys($shareLink->getChanges()), ['MoOn']))) {
            return;
        }

        ShareLinkUpdated::dispatch($shareLink);
    }

    public function deleted(ShareLink $shareLink): void
    {
        ShareLinkDeleted::dispatch($shareLink);
    }
}
