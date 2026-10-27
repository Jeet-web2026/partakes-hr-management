<?php

namespace App\Repositories\Contracts;

use App\Models\InterviewStep;
use Illuminate\Support\Collection;

interface InterviewStepRepositoryInterface
{
    public function all(): Collection;

    public function getActive(): Collection;

    public function find(int $id): ?InterviewStep;

    public function create(array $data): InterviewStep;

    public function update(InterviewStep $step, array $data): InterviewStep;

    public function delete(InterviewStep $step): bool;

    public function upsertByName(string $name, bool $status, string $orgUin, string $userUin): InterviewStep;
}
