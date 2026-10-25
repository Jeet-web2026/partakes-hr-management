<?php

namespace App\Providers;

use App\Repositories\Contracts\MastIntvStepRepositoryInterface;
use App\Repositories\Eloquent\MastIntvStepRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MastIntvStepRepositoryInterface::class, MastIntvStepRepository::class);
    }
}