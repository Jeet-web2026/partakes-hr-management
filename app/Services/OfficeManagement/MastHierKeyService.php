<?php

namespace App\Services\OfficeManagement;

use App\Enums\OfficeLevel;
use App\Models\HierKey;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MastHierKeyService
{
    public function listForOrganisation(string $orgaUin, string $search = '', ?OfficeLevel $level = null): Collection
    {
        return HierKey::query()
            ->with(['creator', 'modifier', 'status'])
            ->where('Orga_UIN', $orgaUin)
            ->when($search !== '', fn($q) => $q->where('Hiky_Levl_Name', 'like', "%{$search}%"))
            ->when($level, fn($q) => $q->where('Hiky_Levl', $level->value))
            ->orderBy('Hiky_Levl')
            ->get();
    }

    public function find(string $orgaUin, string $uin): ?HierKey
    {
        return HierKey::where('Orga_UIN', $orgaUin)->whereKey($uin)->first();
    }

    public function create(string $orgaUin, int $level, string $name, int $status, string $userUin): HierKey
    {
        $this->assertLevelIsFree($orgaUin, $level);

        return HierKey::create([
            'Orga_UIN' => $orgaUin,
            'Hiky_Levl' => $level,
            'Hiky_Levl_Name' => $name,
            'Stau_UIN' => $status,
            'CrBy' => $userUin,
        ]);
    }

    public function update(HierKey $hierKey, array $data, string $userUin): HierKey
    {
        $this->assertLevelIsFree($hierKey->Orga_UIN, (int) $data['Hiky_Levl'], $hierKey->getKey());

        $hierKey->fill($data + [
            'MoBy' => $userUin,
            'MoOn' => now(),
        ])->save();

        return $hierKey;
    }

    public function delete(HierKey $hierKey, string $userUin): void
    {
        // Block deletion while offices still use this level
        $inUse = DB::table('tbl_mast_office')
            ->where('Hiky_UIN', $hierKey->getKey())
            ->whereNull('DeOn')
            ->exists();

        if ($inUse) {
            throw ValidationException::withMessages([
                'delete' => 'This level is used by existing offices and cannot be deleted.',
            ]);
        }

        // Direct update so the soft delete doesn't touch the Modified columns
        HierKey::whereKey($hierKey->getKey())->update([
            'DeBy' => $userUin,
            'DeOn' => now(),
        ]);
    }

    protected function assertLevelIsFree(string $orgaUin, int $level, mixed $ignoreUin = null): void
    {
        $exists = HierKey::query()
            ->where('Orga_UIN', $orgaUin)
            ->where('Hiky_Levl', $level)
            ->when($ignoreUin, fn($q) => $q->whereKeyNot($ignoreUin))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'formLevel' => 'This level already exists for the organisation.',
            ]);
        }
    }

    public function usedLevels(string $orgaUin, mixed $ignoreUin = null): array
    {
        return HierKey::query()
            ->where('Orga_UIN', $orgaUin)
            ->when($ignoreUin, fn($q) => $q->whereKeyNot($ignoreUin))
            ->pluck('Hiky_Levl')
            ->map(fn($l) => (int) $l)
            ->all();
    }
}
