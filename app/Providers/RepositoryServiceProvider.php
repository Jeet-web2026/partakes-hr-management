<?php

namespace App\Providers;

use App\Repositories\Contracts\InterviewStepRepositoryInterface;
use App\Repositories\Contracts\MastDsigRepositoryInterface;
use App\Repositories\Contracts\MastHrFlagRepositoryInterface;
use App\Repositories\Contracts\MastIntvQstnRepositoryInterface;
use App\Repositories\Contracts\MastIntvStepRepositoryInterface;
use App\Repositories\Contracts\MastStauRepositoryInterface;
use App\Repositories\Eloquent\InterviewStepRepository;
use App\Repositories\Eloquent\MastDsigRepository;
use App\Repositories\Eloquent\MastHrFlagRepository;
use App\Repositories\Eloquent\MastIntvQstnRepository;
use App\Repositories\Eloquent\MastIntvStepRepository;
use App\Repositories\Eloquent\MastStauRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MastIntvStepRepositoryInterface::class, MastIntvStepRepository::class);
        $this->app->bind(MastIntvQstnRepositoryInterface::class, MastIntvQstnRepository::class);
        $this->app->bind(MastHrFlagRepositoryInterface::class, MastHrFlagRepository::class);
        $this->app->bind(InterviewStepRepositoryInterface::class, InterviewStepRepository::class);
        $this->app->bind(MastDsigRepositoryInterface::class, MastDsigRepository::class);
        $this->app->bind(MastStauRepositoryInterface::class, MastStauRepository::class);
    }
}