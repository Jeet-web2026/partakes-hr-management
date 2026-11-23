<?php

namespace App\Services\ApplicationManagement;

use App\Enums\HrFlagStage;
use App\Enums\InterviewStepStatus;
use App\Jobs\CreateDefaultHrFlagsJob;
use App\Models\InterviewStep;
use App\Models\MastHrFlag;
use App\Repositories\Contracts\MastHrFlagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MastHrFlagService
{
    public function __construct(protected MastHrFlagRepositoryInterface $flags) {}

    /** Sorted by stage order, then creation time. */
    public function listForOrganisation(string $orgaUin, ?string $search = null, ?HrFlagStage $stage = null): Collection
    {
        return $this->flags
            ->listForOrganisation($orgaUin, $search, $stage?->value)
            ->sort(fn(MastHrFlag $a, MastHrFlag $b) => [
                $a->hr_flag_stge_name,
                $a->Cr_On->getTimestamp(),
            ] <=> [
                $b->hr_flag_stge_name,
                $b->Cr_On->getTimestamp(),
            ])
            ->values();
    }

    public function find(string $orgaUin, string $uin): ?MastHrFlag
    {
        return $this->flags->findInOrganisation($orgaUin, $uin);
    }

    // Call this when the user selects an organisation.
    public function ensureDefaultFlagsQueued(string $orgaUin, string $userId): void
    {
        if ($this->flags->existsForOrganisation($orgaUin)) {
            return;
        }

        CreateDefaultHrFlagsJob::dispatch($orgaUin, $userId);
    }

    // Idempotent: a flag that already exists in its stage is skipped.
    public function createDefaultFlags(string $orgaUin, string $userId): void
    {
        DB::transaction(function () use ($orgaUin, $userId) {
            foreach (HrFlagStage::cases() as $stage) {
                foreach ($stage->defaultFlags() as $name) {
                    if ($this->flags->nameExists($orgaUin, $stage->value, $name)) {
                        continue;
                    }

                    $this->flags->create([
                        'hr_flag_stge_name' => $stage,
                        'hr_flag_placeholder' => $name,
                        'hr_flag_stau' => InterviewStepStatus::Inactive,
                        'Orga_UIN' => $orgaUin,
                        'Cr_By' => $userId,
                        'Mo_By' => $userId,
                        'is_available' => 0
                    ]);
                }
            }
        });
    }

    public function create(
        string $orgaUin,
        string $stageUin,
        string $name,
        InterviewStepStatus|string $status,
        string $userId
    ): MastHrFlag {
        $name = trim($name);

        $this->assertStageIsValid($orgaUin, $stageUin);
        $this->assertNameIsFree($orgaUin, $stageUin, $name);

        return $this->flags->create([
            'hr_flag_stge_name' => $stageUin,
            'hr_flag_name'      => $name,
            'hr_flag_stau'      => $status,
            'Orga_UIN'          => $orgaUin,
            'Cr_By'             => $userId,
            'Mo_By'             => $userId,
        ]);
    }

    protected function assertStageIsValid(string $orgaUin, string $stageUin): void
    {
        $exists = InterviewStep::query()
            ->active()
            ->forOrganisation($orgaUin)
            ->whereKey($stageUin)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'formStage' => 'The selected stage is not available.',
            ]);
        }
    }

    // Only stage, name and status can change; everything else in $data is ignored.
    public function update(MastHrFlag $flag, array $data, string $userId): MastHrFlag
    {
        $changes = array_intersect_key($data, array_flip(['hr_flag_stge_name', 'hr_flag_name', 'hr_flag_stau']));

        $stage = $flag->hr_flag_stge_name;

        $name = isset($changes['hr_flag_name']) ? trim($changes['hr_flag_name']) : $flag->hr_flag_name;

        $this->assertNameIsFree($flag->Orga_UIN, $stage, $name, $flag->getKey());

        $changes['hr_flag_stge_name'] = $stage;
        $changes['hr_flag_name'] = $name;
        $changes['Mo_By'] = $userId;

        return $this->flags->update($flag, $changes);
    }

    public function delete(MastHrFlag $flag, string $userId): bool
    {
        return $this->flags->softDelete($flag, $userId);
    }

    protected function assertNameIsFree(
        string $orgaUin,
        string $stageUin,
        string $name,
        ?string $ignoreUin = null
    ): void {
        if ($this->flags->nameExists($orgaUin, $stageUin, $name, $ignoreUin)) {
            throw ValidationException::withMessages([
                'formName' => 'This flag already exists in the selected stage.',
            ]);
        }
    }
}
