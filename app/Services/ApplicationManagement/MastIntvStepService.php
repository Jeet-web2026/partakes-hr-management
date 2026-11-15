<?php

namespace App\Services\ApplicationManagement;

use App\Jobs\CreateDefaultInterviewStepsJob;
use App\Models\MastIntvStep;
use App\Repositories\Contracts\InterviewStepRepositoryInterface;
use App\Repositories\Contracts\MastIntvStepRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MastIntvStepService
{
    public const STATUS_ACTIVE   = 100201;
    public const STATUS_INACTIVE = 100202;

    public const DEFAULT_STEPS = [
        'Introduction',
        'Written / Skill Test 1',
        'Intimate',
        'Verbal Interview Round 1',
        'Verbal Interview Round 2',
        'Verbal Interview Final Round',
        'Salary Expectation/Negotiation',
    ];

    /** Offer Accepted shows all 7 steps; the LAST three are these fixed ones (fixed names, always active, not editable). */
    public const OFFER_ACCEPTED_STAGE = 'Offer Accepted';

    public const OFFER_ACCEPTED_STEPS = [
        'Offer letter issued/ acceptance',
        'Employee document update',
        'Employee joining & posting',
    ];

    public function __construct(
        protected MastIntvStepRepositoryInterface $steps,
        protected InterviewStepRepositoryInterface $stageInterface
    ) {}

    /* ---------------------------------------------------------------------
     | Fixed-stage helpers
     * ------------------------------------------------------------------- */

    public static function isFixedStageName(?string $name): bool
    {
        return mb_strtolower(trim((string) $name)) === mb_strtolower(self::OFFER_ACCEPTED_STAGE);
    }

    /**
     * Fixed rows for a stage: row index (0-based) => fixed step name.
     * Offer Accepted: the last three of the 7 steps. Other stages: none.
     */
    public static function fixedStepMap(?string $stageName): array
    {
        if (! self::isFixedStageName($stageName)) {
            return [];
        }

        $start = count(self::DEFAULT_STEPS) - count(self::OFFER_ACCEPTED_STEPS);

        return array_combine(
            range($start, count(self::DEFAULT_STEPS) - 1),
            self::OFFER_ACCEPTED_STEPS
        );
    }

    /** Step labels (placeholders) for a stage: always 7; fixed rows carry their fixed name. */
    public static function stepLabelsForStage(?string $stageName): array
    {
        return array_replace(self::DEFAULT_STEPS, self::fixedStepMap($stageName));
    }

    /* ---------------------------------------------------------------------
     | Queries
     * ------------------------------------------------------------------- */

    public function queryForOrganisation(string $orgaUin): Builder
    {
        return $this->steps->queryForOrganisation($orgaUin);
    }

    public function listForOrganisation(string $orgaUin): Collection
    {
        return $this->steps->allForOrganisation($orgaUin);
    }

    /* ---------------------------------------------------------------------
     | Creation
     * ------------------------------------------------------------------- */

    public function ensureDefaultStepsQueued(string $orgaUin, string $userId): void
    {
        if ($this->steps->existsForOrganisation($orgaUin)) {
            return;
        }

        CreateDefaultInterviewStepsJob::dispatch($orgaUin, $userId);
    }

    public function createDefaultSteps(string $orgaUin, string $userId): void
    {
        $allStages = $this->stageInterface->getActive($orgaUin);

        DB::transaction(function () use ($orgaUin, $userId, $allStages) {
            foreach ($allStages as $index => $stage) {
                $number = $index + 1;

                if ($this->steps->findByOrganisationAndNumber($orgaUin, $number)) {
                    continue;
                }

                // Defaults are created inactive (as before); Offer Accepted is always active.
                $this->createStepsForStage($orgaUin, $userId, $stage, [], [], self::STATUS_INACTIVE);
            }
        });

        DB::table('tbl_mast_intv_stage')
            ->where('Orga_UIN', $orgaUin)
            ->update(['Stage_Status' => self::STATUS_ACTIVE]);
    }

    /**
     * Create all step rows for one stage.
     *
     * Always 7 steps. Names/statuses come from the given arrays, except fixed rows
     * (Offer Accepted: last three) whose names and status are forced (client input ignored).
     */
    public function createStepsForStage(
        string $orgaUin,
        string $userId,
        $stage,
        array $names = [],
        array $statuses = [],
        int|string $defaultStatus = self::STATUS_ACTIVE
    ): void {
        $fixedMap = self::fixedStepMap($stage->Stage_Name);
        $labels   = self::stepLabelsForStage($stage->Stage_Name);

        DB::transaction(function () use ($orgaUin, $userId, $stage, $fixedMap, $labels, $names, $statuses, $defaultStatus) {
            foreach ($labels as $i => $label) {
                $isFixed = array_key_exists($i, $fixedMap);
                $name    = $isFixed ? $label : trim((string) ($names[$i] ?? ''));

                $this->steps->create([
                    'Intv_Step_Numb'             => $i + 1,
                    'Intv_Step_Name'             => $name !== '' ? $name : null,
                    'Intv_Step_Placeholder_Type' => $label,
                    'Intv_Step_Stau'             => $isFixed
                        ? self::STATUS_ACTIVE
                        : ($statuses[$i] ?? $defaultStatus),
                    'Orga_UIN'                   => $orgaUin,
                    'Cr_By'                      => $userId,
                    'Mo_By'                      => $userId,
                    'Intv_Stage'                 => $stage->getKey(),
                ]);
            }
        });
    }

    /**
     * "Add" flow from the Interview Steps page: validates the stage, then
     * creates all of its step rows.
     *
     * @throws ValidationException
     */
    public function addForStage(
        string $orgaUin,
        string $userId,
        string $stageUin,
        array $names,
        array $statuses
    ): void {
        $stage = $this->stageInterface->getActive($orgaUin)
            ->first(fn($s) => (string) $s->getKey() === $stageUin);

        if (! $stage) {
            throw ValidationException::withMessages([
                'editStage' => 'Invalid or inactive stage.',
            ]);
        }

        if (MastIntvStep::query()->where('Intv_Stage', $stage->getKey())->exists()) {
            throw ValidationException::withMessages([
                'editStage' => 'This stage already has interview steps.',
            ]);
        }

        $this->createStepsForStage($orgaUin, $userId, $stage, $names, $statuses);
    }

    /* ---------------------------------------------------------------------
     | Update / delete
     * ------------------------------------------------------------------- */

    /**
     * @throws ValidationException when the step is one of a stage's fixed steps
     */
    public function updateStep(MastIntvStep $step, array $data, string $userId): MastIntvStep
    {
        $fixedMap = self::fixedStepMap($step->stage?->Stage_Name);

        if (array_key_exists(((int) $step->Intv_Step_Numb) - 1, $fixedMap)) {
            throw ValidationException::withMessages([
                'editNames' => 'This step is fixed and cannot be edited.',
            ]);
        }

        $changes = array_intersect_key(
            $data,
            array_flip(['Intv_Step_Name', 'Intv_Step_Stau', 'Intv_Stage'])
        );

        $changes['Mo_By'] = $userId;

        return $this->steps->update($step, $changes);
    }

    public function deleteStep(MastIntvStep $step, string $userId): bool
    {
        return $this->steps->softDelete($step, $userId);
    }
}