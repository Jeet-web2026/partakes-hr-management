<?php

namespace App\Repositories\Eloquent;

use App\Models\MastDsig;
use App\Repositories\Contracts\MastDsigRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MastDsigRepository implements MastDsigRepositoryInterface
{
    public function __construct(
        protected MastDsig $model,
    ) {}

    public function getByOrganisation(int $orgUin, ?string $search = null): Collection
    {
        return $this->baseQuery($orgUin, $search)->get();
    }

    public function paginateByOrganisation(int $orgUin, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->baseQuery($orgUin, $search)->paginate($perPage);
    }

    public function findById(int $dsigUin): ?MastDsig
    {
        return $this->model->newQuery()->with('status')->find($dsigUin);
    }

    public function nameExists(int $orgUin, string $name, ?int $ignoreDsigUin = null): bool
    {
        return $this->model
            ->newQuery()
            ->where('Org_UIN', $orgUin)
            ->where('Dsig_Name', $name)
            ->when($ignoreDsigUin, fn(Builder $q) => $q->where('Dsig_UIN', '!=', $ignoreDsigUin))
            ->exists();
    }

    public function create(array $data): MastDsig
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(int $dsigUin, array $data): MastDsig
    {
        $designation = $this->model->newQuery()->findOrFail($dsigUin);
        $designation->update($data);

        return $designation->refresh();
    }

    public function delete(int $dsigUin): bool
    {
        return (bool) $this->model->newQuery()->findOrFail($dsigUin)->delete();
    }

    protected function baseQuery(int $orgUin, ?string $search): Builder
    {
        return $this->model
            ->newQuery()
            ->with('status')
            ->where('Org_UIN', $orgUin)
            ->when($search, fn(Builder $q) => $q->where('Dsig_Name', 'like', "%{$search}%"))
            ->orderBy('Dsig_Name');
    }
}
