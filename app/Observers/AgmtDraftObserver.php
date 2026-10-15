<?php

namespace App\Observers;

use App\Events\AgmtDraftCreated;
use App\Events\AgmtDraftDeleted;
use App\Events\AgmtDraftUpdated;
use App\Models\AgmtDraft;

class AgmtDraftObserver
{
    /**
     * Handle the AgmtDraft "created" event.
     */
    public function created(AgmtDraft $agmtDraft): void
    {
        event(new AgmtDraftCreated($agmtDraft));
    }

    /**
     * Handle the AgmtDraft "updated" event.
     */
    public function updated(AgmtDraft $agmtDraft): void
    {
        event(new AgmtDraftUpdated($agmtDraft, $agmtDraft->getOriginal()));
    }

    /**
     * Handle the AgmtDraft "deleted" event.
     */
    public function deleted(AgmtDraft $agmtDraft): void
    {
        event(new AgmtDraftDeleted($agmtDraft));
    }

    /**
     * Handle the AgmtDraft "restored" event.
     */
    public function restored(AgmtDraft $agmtDraft): void
    {
        //
    }

    /**
     * Handle the AgmtDraft "force deleted" event.
     */
    public function forceDeleted(AgmtDraft $agmtDraft): void
    {
        //
    }
}