<?php

namespace App\Repositories\Contracts;

use App\Models\MastHrFlag;
use Illuminate\Database\Eloquent\Collection;

interface MastHrFlagRepositoryInterface
{
    /** @return Collection<int, MastHrFlag> */
    public function listForOrganisation(string $orgaUin, ?string $search = null, ?string $stage = null): Collection;

    public function findInOrganisation(string $orgaUin, string $uin): ?MastHrFlag;

    public function existsForOrganisation(string $orgaUin): bool;

    /** True when a (non-deleted) flag with this name already exists in the stage. */
    public function nameExists(string $orgaUin, string $stage, string $name, ?string $ignoreUin = null): bool;

    public function create(array $data): MastHrFlag;

    public function update(MastHrFlag $flag, array $data): MastHrFlag;

    public function softDelete(MastHrFlag $flag, string $deletedBy): bool;
}
