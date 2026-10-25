<?php

namespace App\Repositories\Eloquent;

use App\Models\MastIntvStep;
use App\Repositories\Contracts\MastIntvStepRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MastIntvStepRepository implements MastIntvStepRepositoryInterface
{
    public function queryForOrganisation(string $orgaUin): Builder
    {
        return MastIntvStep::query()->forOrganisation($orgaUin)->orderBy('Intv_Step_Numb');
    }

    public function allForOrganisation(string $orgaUin): Collection
    {
        return $this->queryForOrganisation($orgaUin)->get();
    }

    public function findById(string $uin): ?MastIntvStep
    {
        return MastIntvStep::query()->find($uin);
    }

    public function findByOrganisationAndNumber(string $orgaUin, int $stepNumber): ?MastIntvStep
    {
        return MastIntvStep::query()
            ->forOrganisation($orgaUin)
            ->where('Intv_Step_Numb', $stepNumber)
            ->first();
    }

    public function existsForOrganisation(string $orgaUin): bool
    {
        return MastIntvStep::query()->forOrganisation($orgaUin)->exists();
    }

    public function nameExists(string $orgaUin, string $name, ?string $ignoreUin = null): bool
    {
        return MastIntvStep::query()
            ->forOrganisation($orgaUin)
            ->where('Intv_Step_Name', $name)
            ->when($ignoreUin, fn(Builder $q) => $q->where('Intv_Step_UIN', '!=', $ignoreUin))
            ->exists();
    }

    public function create(array $data): MastIntvStep
    {
        return MastIntvStep::create($data);
    }

    public function update(MastIntvStep $step, array $data): MastIntvStep
    {
        $step->update($data);

        return $step->refresh();
    }

    public function softDelete(MastIntvStep $step, string $deletedBy): bool
    {
        return $step->update(['Del_By' => $deletedBy, 'Mo_By' => $deletedBy]);
    }
}
