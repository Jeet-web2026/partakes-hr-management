<?php

namespace App\Jobs;

use App\Services\ApplicationManagement\MastIntvStepService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateDefaultInterviewStepsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;
    public int $backoff = 10;
    public int $uniqueFor = 60;

    public function __construct(
        public string $orgaUin,
        public string $userId,
    ) {}

    public function uniqueId(): string
    {
        return $this->orgaUin;
    }

    public function handle(MastIntvStepService $service): void
    {
        $service->createDefaultSteps($this->orgaUin, $this->userId);
    }

    public function failed(Throwable $e): void
    {
        Log::error('Default interview steps creation failed', [
            'Orga_UIN' => $this->orgaUin,
            'error' => $e->getMessage(),
        ]);
    }
}
