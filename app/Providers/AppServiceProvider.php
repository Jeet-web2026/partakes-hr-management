<?php

namespace App\Providers;

use App\Models\AgreementName;
use App\Models\HrMastJobAply;
use App\Models\OrganisationDetails;
use App\Models\ShareLink;
use App\Observers\AgreementNameObserver;
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

        AgreementName::observe(AgreementNameObserver::class);
        ShareLink::observe(ShareLinkObserver::class);

        Relation::morphMap([
            'candidate' => HrMastJobAply::class,
        ]);

        Relation::enforceMorphMap([
            'organisation' => OrganisationDetails::class,
        ]);
    }
}
