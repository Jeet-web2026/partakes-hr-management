<?php

namespace App\Providers;

use App\Models\AgmtDraft;
use App\Models\Agreement;
use App\Models\AgreementName;
use App\Models\ShareLink;
use App\Observers\AgmtDraftObserver;
use App\Observers\AgreementNameObserver;
use App\Observers\AgreementObserver;
use App\Observers\ShareLinkObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production') || app()->environment('development')) {
            URL::forceScheme('https');
        }

        Agreement::observe(AgreementObserver::class);
        AgmtDraft::observe(AgmtDraftObserver::class);
        AgreementName::observe(AgreementNameObserver::class);
        ShareLink::observe(ShareLinkObserver::class);

        Relation::morphMap([
            'agreement' => Agreement::class,
            'draft'     => AgmtDraft::class,
        ]);
    }
}
