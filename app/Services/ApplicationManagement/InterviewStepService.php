<?php

namespace App\Services\ApplicationManagement;

use App\Models\InterviewStep;
use App\Repositories\Contracts\InterviewStepRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class InterviewStepService
{
    public const DEFAULT_STEPS = [
        'New Candidate',
        'Candidate Short Listed',
        'Schedule Interview',
        'Candidate Selected',
        'Offer Letter Issued',
        'Offer Accepted'
    ];

    public function __construct(
        private readonly InterviewStepRepositoryInterface $steps,
    ) {}

    public function list(bool $onlyActive = false): Collection
    {
        return $onlyActive ? $this->steps->getActive() : $this->steps->all();
    }

    public function create(array $data): InterviewStep
    {
        return $this->steps->create($data);
    }

    public function update(InterviewStep $step, array $data): InterviewStep
    {
        return $this->steps->update($step, $data);
    }

    public function delete(InterviewStep $step): bool
    {
        return $this->steps->delete($step);
    }

    public function seedDefaults(string $orgUin, string $userUin): int
    {
        foreach (self::DEFAULT_STEPS as $name) {
            $this->steps->upsertByName($name,  true, $orgUin, $userUin);
        }

        return count(self::DEFAULT_STEPS);
    }

    public function listForOrganisation(string $orgaUin): Collection
    {
        return InterviewStep::query()
            ->forOrganisation($orgaUin)
            ->orderBy('id')
            ->get();
    }

    public function updateStep(InterviewStep $stage, array $data, string $userUin): InterviewStep
    {
        $duplicate = InterviewStep::query()
            ->forOrganisation($stage->Orga_UIN)
            ->where('Stage_Name', $data['Stage_Name'])
            ->whereKeyNot($stage->getKey())
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'editName' => 'A stage with this name already exists.',
            ]);
        }

        $stage->update($data + ['Mo_By' => $userUin]);

        return $stage;
    }
}
