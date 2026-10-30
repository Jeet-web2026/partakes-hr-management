<?php

namespace App\Repositories\Eloquent;

use App\Models\MastStau;
use App\Repositories\Contracts\MastStauRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MastStauRepository implements MastStauRepositoryInterface
{
    public function __construct(
        protected MastStau $model,
    ) {}

    public function getByOrganisation(int $orgUin): Collection
    {
        return $this->model
            ->newQuery()
            ->where('Orga_UIN', $orgUin)
            ->orderBy('Stau_UIN')
            ->get();
    }

    public function findById(int $stauUin): ?MastStau
    {
        return $this->model->newQuery()->find($stauUin);
    }

    public function create(array $data): MastStau
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(int $stauUin, array $data): MastStau
    {
        $status = $this->model->newQuery()->findOrFail($stauUin);
        $status->update($data);

        return $status->refresh();
    }

    public function delete(int $stauUin): bool
    {
        return (bool) $this->model->newQuery()->findOrFail($stauUin)->delete();
    }
}
