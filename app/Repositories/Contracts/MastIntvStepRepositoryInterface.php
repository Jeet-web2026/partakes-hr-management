<?php

namespace App\Repositories\Contracts;

use App\Models\MastIntvStep;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface MastIntvStepRepositoryInterface
{
    public function queryForOrganisation(string $orgaUin): Builder;

    /** @return Collection<int, MastIntvStep> */
    public function allForOrganisation(string $orgaUin): Collection;

    public function findById(string $uin): ?MastIntvStep;

    public function findByOrganisationAndNumber(string $orgaUin, int $stepNumber): ?MastIntvStep;

    public function existsForOrganisation(string $orgaUin): bool;

    public function nameExists(string $orgaUin, string $name, ?string $ignoreUin = null): bool;

    public function create(array $data): MastIntvStep;

    public function update(MastIntvStep $step, array $data): MastIntvStep;

    public function softDelete(MastIntvStep $step, string $deletedBy): bool;
}
