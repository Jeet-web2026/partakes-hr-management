<?php

namespace App\Repositories\Eloquent;

use App\Models\MastHrFlag;
use App\Repositories\Contracts\MastHrFlagRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MastHrFlagRepository implements MastHrFlagRepositoryInterface
{
    public function listForOrganisation(string $orgaUin, ?string $search = null, ?string $stage = null): Collection
    {
        $term = trim((string) $search);

        return MastHrFlag::query()
            ->forOrganisation($orgaUin)
            ->when($stage, fn(Builder $q) => $q->where('hr_flag_stge_name', $stage))
            ->when($term !== '', fn(Builder $q) => $q->whereRaw(
                'LOWER(hr_flag_name) LIKE ?',
                ['%' . mb_strtolower($term) . '%']
            ))
            ->orderBy('Cr_On')
            ->get();
    }

    public function findInOrganisation(string $orgaUin, string $uin): ?MastHrFlag
    {
        return MastHrFlag::query()
            ->forOrganisation($orgaUin)
            ->where('hr_flag_UIN', $uin)
            ->first();
    }

    public function existsForOrganisation(string $orgaUin): bool
    {
        return MastHrFlag::query()->forOrganisation($orgaUin)->exists();
    }

    public function nameExists(string $orgaUin, string $stage, string $name, ?string $ignoreUin = null): bool
    {
        return MastHrFlag::query()
            ->forOrganisation($orgaUin)
            ->where('hr_flag_stge_name', $stage)
            ->whereRaw('LOWER(hr_flag_name) = ?', [mb_strtolower(trim($name))])
            ->when($ignoreUin, fn(Builder $q) => $q->where('hr_flag_UIN', '!=', $ignoreUin))
            ->exists();
    }

    public function create(array $data): MastHrFlag
    {
        return MastHrFlag::create($data);
    }

    public function update(MastHrFlag $flag, array $data): MastHrFlag
    {
        $flag->update($data);

        return $flag->refresh();
    }

    public function softDelete(MastHrFlag $flag, string $deletedBy): bool
    {
        return $flag->update(['Del_By' => $deletedBy, 'Mo_By' => $deletedBy]);
    }
}
