<?php

namespace App\Jobs;

use App\Services\ApplicationManagement\InterviewStepService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SeedDefaultInterviewStepsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $uniqueFor = 300;

    public function __construct(
        public string $orgaUin,
        public string $userUin,
    ) {}

    // Required for a parameterized unique job: one lock per organisation.
    public function uniqueId(): string
    {
        return $this->orgaUin;
    }

    public function handle(InterviewStepService $service): void
    {
        $service->seedDefaults($this->orgaUin, $this->userUin);
    }
}
