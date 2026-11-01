<?php

namespace App\Services\ApplicationManagement;

use App\Enums\InterviewStepStatus;
use App\Jobs\CreateDefaultInterviewStepsJob;
use App\Models\MastIntvStep;
use App\Repositories\Contracts\InterviewStepRepositoryInterface;
use App\Repositories\Contracts\MastIntvStepRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MastIntvStepService
{
    public const DEFAULT_STEPS = [
        'Introduction',
        'Written / Skill Test 1',
        'Intimate',
        'Verbal Interview Round 1',
        'Verbal Interview Round 2',
        'Verbal Interview Final Round',
        'Salary Expectation/Negotiation',
    ];

    public function __construct(protected MastIntvStepRepositoryInterface $steps, protected InterviewStepRepositoryInterface $stageInterface) {}

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
        $allStages =  $this->stageInterface->getActive();

        DB::transaction(function () use ($orgaUin, $userId, $allStages) {
            foreach ($allStages as $index => $stage) {
                $number = $index + 1;

                if ($this->steps->findByOrganisationAndNumber($orgaUin, $number)) {
                    continue;
                }

                foreach (self::DEFAULT_STEPS as $stepIndex => $step) {
                    $stepNumber = $stepIndex + 1;

                    $this->steps->create([
                        'Intv_Step_Numb'             => $stepNumber,
                        'Intv_Step_Name'             => null,
                        'Intv_Step_Placeholder_Type' => $step,
                        'Intv_Step_Stau'             => InterviewStepStatus::Active,
                        'Orga_UIN'                   => $orgaUin,
                        'Cr_By'                      => $userId,
                        'Mo_By'                      => $userId,
                        'Intv_Stage'                 => $stage->getKey(),
                    ]);
                }
            }
        });

        DB::table('tbl_mast_intv_stage')->update([
            'Stage_Status' => true
        ]);
    }
    public function updateStep(MastIntvStep $step, array $data, string $userId): MastIntvStep
    {
        $changes = array_intersect_key(
            $data,
            array_flip(['Intv_Step_Name', 'Intv_Step_Stau', 'Intv_Stage'])
        );

        if (array_key_exists('Intv_Step_Name', $changes)) {
            $names = $changes['Intv_Step_Name'];

            if (is_array($names)) {
                $names = array_map(fn($n) => trim((string) $n), array_values($names));

                if (collect($names)->every(fn($n) => $n === '')) {
                    $names = null;
                }
            } else {
                $names = null;
            }

            $changes['Intv_Step_Name'] = $names;
        }

        $changes['Mo_By'] = $userId;

        return $this->steps->update($step, $changes);
    }

    public function deleteStep(MastIntvStep $step, string $userId): bool
    {
        return $this->steps->softDelete($step, $userId);
    }
}
