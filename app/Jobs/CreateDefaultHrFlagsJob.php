<?php

namespace App\Jobs;

use App\Services\ApplicationManagement\MastHrFlagService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateDefaultHrFlagsJob implements ShouldQueue, ShouldBeUnique
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

    public function handle(MastHrFlagService $service): void
    {
        $service->createDefaultFlags($this->orgaUin, $this->userId);
    }

    public function failed(Throwable $e): void
    {
        Log::error('Default HR flags creation failed', [
            'Orga_UIN' => $this->orgaUin,
            'error' => $e->getMessage(),
        ]);
    }
}
