<?php

namespace App\Repositories\Eloquent;

use App\Models\InterviewStep;
use App\Repositories\Contracts\InterviewStepRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        $name = $name;

        return DB::transaction(function () use ($name, $status, $orgUin, $userUin) {
            $existing = $this->findByOrganisationAndName($orgUin, $name, $userUin);

            if ($existing) {
                return $existing;
            }

            return $this->create([
                'Stage_Name'   => $name,
                'Stage_Status' => $status,
                'Orga_UIN'     => $orgUin,
                'Cr_By'        => $userUin,
                'Mo_By'        => $userUin,
            ]);
        });
    }

    public function findByOrganisationAndName(string $orgaUin, string $name, string $crBy): ?InterviewStep
    {
        return InterviewStep::query()
            ->forOrganisation($orgaUin)
             ->where('Cr_By', $crBy)
            ->where('Stage_Name', $name)
            ->first();
    }
}
