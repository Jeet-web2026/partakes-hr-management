<?php

namespace App\Repositories\Eloquent;

use App\Models\InterviewStep;
use App\Repositories\Contracts\InterviewStepRepositoryInterface;
use Illuminate\Support\Collection;

class InterviewStepRepository implements InterviewStepRepositoryInterface
{
    public function all(): Collection
    {
        return InterviewStep::query()->orderBy('id')->get();
    }

    public function getActive(): Collection
    {
        return InterviewStep::query()->active()->orderBy('id')->get();
    }

    public function find(int $id): ?InterviewStep
    {
        return InterviewStep::find($id);
    }

    public function create(array $data): InterviewStep
    {
        return InterviewStep::create($data);
    }

    public function update(InterviewStep $step, array $data): InterviewStep
    {
        $step->update($data);

        return $step->refresh();
    }

    public function delete(InterviewStep $step): bool
    {
        return (bool) $step->delete();
    }

    public function upsertByName(string $name, bool $status, string $orgUin, string $userUin): InterviewStep
    {
        return InterviewStep::firstOrCreate(
            [
                'Orga_UIN'   => $orgUin,
                'Cr_By'      => $userUin,
                'Stage_Name' => $name,
            ],
            [
                'Mo_By'        => $userUin,
                'Stage_Status' => $status,
            ],
        );
    }
}
