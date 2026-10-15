<?php

namespace App\Observers;

use App\Events\AgreementCreated;
use App\Events\AgreementDeleted;
use App\Events\AgreementUpdated;
use App\Models\Agreement;

class AgreementObserver
{
    /**
     * Handle the Agreement "created" event.
     */
    public function created(Agreement $agreement): void
    {
         event(new AgreementCreated($agreement));
    }

    /**
     * Handle the Agreement "updated" event.
     */
    public function updated(Agreement $agreement): void
    {
        event(new AgreementUpdated($agreement, $agreement->getOriginal()));
    }

    /**
     * Handle the Agreement "deleted" event.
     */
    public function deleted(Agreement $agreement): void
    {
        event(new AgreementDeleted($agreement));
    }

    /**
     * Handle the Agreement "restored" event.
     */
    public function restored(Agreement $agreement): void
    {
        //
    }

    /**
     * Handle the Agreement "force deleted" event.
     */
    public function forceDeleted(Agreement $agreement): void
    {
        //
    }
}
