<?php

namespace App\Services\ApplicationManagement;

use App\Enums\InterviewStepStatus;
use App\Jobs\CreateDefaultInterviewStepsJob;
use App\Models\MastIntvStep;
use App\Repositories\Contracts\MastIntvStepRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MastIntvStepService
{
    public const DEFAULT_STEPS = [
        1 => 'Introduction',
        2 => 'Written / Skill Test 1',
        3 => 'Intimate',
        4 => 'Verbal Interview Round 1',
        5 => 'Verbal Interview Round 2',
        6 => 'Verbal Interview Final Round',
        7 => 'Salary Expectation/Negotiation',
    ];

    public function __construct(protected MastIntvStepRepositoryInterface $steps) {}

    public function queryForOrganisation(string $orgaUin): Builder
    {
        return $this->steps->queryForOrganisation($orgaUin);
    }

    public function listForOrganisation(string $orgaUin): Collection
    {
        return $this->steps->allForOrganisation($orgaUin);
    }

    public function ensureDefaultStepsQueued(string $orgaUin, string $userId): void
    {
        if ($this->steps->existsForOrganisation($orgaUin)) {
            return;
        }

        CreateDefaultInterviewStepsJob::dispatch($orgaUin, $userId);
    }

    public function createDefaultSteps(string $orgaUin, string $userId): void
    {
        DB::transaction(function () use ($orgaUin, $userId) {
            foreach (self::DEFAULT_STEPS as $number => $name) {
                if ($this->steps->findByOrganisationAndNumber($orgaUin, $number)) {
                    continue;
                }

                $this->steps->create([
                    'Intv_Step_Numb' => $number,
                    'Intv_Step_Name' => $name,
                    'Intv_Step_Stau' => InterviewStepStatus::Active,
                    'Orga_UIN' => $orgaUin,
                    'Cr_By' => $userId,
                    'Mo_By' => $userId,
                    'Intv_Stage' => 1
                ]);
            }
        });
    }
    public function updateStep(MastIntvStep $step, array $data, string $userId): MastIntvStep
    {
        $changes = array_intersect_key(
            $data,
            array_flip(['Intv_Step_Name', 'Intv_Step_Stau', 'Intv_Stage'])
        );

        if (isset($changes['Intv_Step_Name'])) {
            $changes['Intv_Step_Name'] = trim($changes['Intv_Step_Name']);

            if ($this->steps->nameExists($step->Orga_UIN, $changes['Intv_Step_Name'], $step->getKey())) {
                throw ValidationException::withMessages([
                    'Intv_Step_Name' => 'Another step in this organisation already uses this name.',
                ]);
            }
        }

        $changes['Mo_By'] = $userId;
        

        return $this->steps->update($step, $changes);
    }

    public function deleteStep(MastIntvStep $step, string $userId): bool
    {
        return $this->steps->softDelete($step, $userId);
    }
}
