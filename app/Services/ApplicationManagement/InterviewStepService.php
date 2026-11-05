<?php

namespace App\Services\ApplicationManagement;

use App\Models\InterviewStep;
use App\Repositories\Contracts\InterviewStepRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class InterviewStepService
{
    public const DEFAULT_STEPS = [
        ['name' => 'New Candidate', 'sort' => 1, 'status' => 100201],
        ['name' => 'Candidate Short Listed', 'sort' => 2, 'status' => 100202],
        ['name' => 'Schedule Interview', 'sort' => 3, 'status' => 100202],
        ['name' => 'Candidate Selected', 'sort' => 4, 'status' => 100202],
        ['name' => 'Offer Letter Issued', 'sort' => 5, 'status' => 100202],
        ['name' => 'Offer Accepted', 'sort' => 6, 'status' => 100202]
    ];

    public function __construct(
        private readonly InterviewStepRepositoryInterface $steps,
    ) {}

    public function list(bool $onlyActive = false, string $orgUin): Collection
    {
        return $onlyActive ? $this->steps->getActive($orgUin) : $this->steps->all();
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
        foreach (self::DEFAULT_STEPS as $step) {
            $this->steps->upsertByName($step['name'], $step['sort'], $step['status'], $orgUin, $userUin);
        }

        return count(self::DEFAULT_STEPS);
    }

    public function listForOrganisation(string $orgaUin): Collection
    {
        return InterviewStep::query()
            ->with('status')
            ->forOrganisation($orgaUin)
            ->orderBy('Stage_Sort')
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
