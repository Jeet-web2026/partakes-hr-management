<?php

namespace App\Providers;

use App\Repositories\Contracts\MastHrFlagRepositoryInterface;
use App\Repositories\Contracts\MastIntvQstnRepositoryInterface;
use App\Repositories\Contracts\MastIntvStepRepositoryInterface;
use App\Repositories\Eloquent\MastHrFlagRepository;
use App\Repositories\Eloquent\MastIntvQstnRepository;
use App\Repositories\Eloquent\MastIntvStepRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MastIntvStepRepositoryInterface::class, MastIntvStepRepository::class);
        $this->app->bind(MastIntvQstnRepositoryInterface::class, MastIntvQstnRepository::class);
        $this->app->bind(MastHrFlagRepositoryInterface::class, MastHrFlagRepository::class);
    }
}