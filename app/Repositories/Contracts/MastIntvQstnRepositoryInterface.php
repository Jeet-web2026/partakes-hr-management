<?php

namespace App\Repositories\Contracts;

use App\Models\MastIntvQstn;
use Illuminate\Database\Eloquent\Collection;

interface MastIntvQstnRepositoryInterface
{
    public function listForOrganisation(string $orgaUin, ?string $search = null): Collection;

    public function findInOrganisation(string $orgaUin, string $uin): ?MastIntvQstn;

    public function create(array $data): MastIntvQstn;

    public function update(MastIntvQstn $question, array $data): MastIntvQstn;

    public function softDelete(MastIntvQstn $question, string $deletedBy): bool;
}