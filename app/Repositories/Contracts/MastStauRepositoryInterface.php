<?php

namespace App\Repositories\Contracts;

use App\Models\MastStau;
use Illuminate\Database\Eloquent\Collection;

interface MastStauRepositoryInterface
{
    public function getByOrganisation(int $orgUin): Collection;

    public function findById(int $stauUin): ?MastStau;

    public function create(array $data): MastStau;

    public function update(int $stauUin, array $data): MastStau;

    public function delete(int $stauUin): bool;
}
