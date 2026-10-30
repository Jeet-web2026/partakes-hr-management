<?php

namespace App\Repositories\Contracts;

use App\Models\MastDsig;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MastDsigRepositoryInterface
{
    public function getByOrganisation(int $orgUin, ?string $search = null): Collection;

    public function paginateByOrganisation(int $orgUin, ?string $search = null, int $perPage = 15): LengthAwarePaginator;

    public function findById(int $dsigUin): ?MastDsig;

    public function nameExists(int $orgUin, string $name, ?int $ignoreDsigUin = null): bool;

    public function create(array $data): MastDsig;

    public function update(int $dsigUin, array $data): MastDsig;

    public function delete(int $dsigUin): bool;
}
