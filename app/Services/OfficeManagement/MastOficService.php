<?php

namespace App\Services\OfficeManagement;

use App\Models\HierKey;
use App\Models\MastOfic;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MastOficService
{
    public function paginate(string $orga, string $search, string $hiky, int $perPage): LengthAwarePaginator
    {
        return MastOfic::query()
            ->with(['hierKey', 'parent', 'creator'])
            ->where('Orga_UIN', $orga)
            ->when($search !== '', fn($q) => $q->where(fn($q) => $q
                ->where('Ofic_Name', 'like', "%{$search}%")
                ->orWhere('Ofic_Code', 'like', "%{$search}%")))
            ->when($hiky !== '', fn($q) => $q->where('Hiky_UIN', $hiky))
            ->orderBy('Ofic_Name')
            ->paginate($perPage);
    }

    public function find(string $orga, string $uin): ?MastOfic
    {
        return MastOfic::where('Orga_UIN', $orga)->find($uin);
    }

    // Office types created for this organisation (Office Type page), active only
    public function officeTypes(string $orga): Collection
    {
        return HierKey::query()
            ->where('Orga_UIN', $orga)
            ->where('Stau_UIN', 100201)
            ->orderBy('Hiky_Levl')
            ->get();
    }

    // Parent must belong to a higher office type (smaller level number)
    public function parentOptions(string $orga, int $level, ?string $exceptUin): Collection
    {
        return MastOfic::query()
            ->where('Orga_UIN', $orga)
            ->when($exceptUin, fn($q) => $q->whereKeyNot($exceptUin))
            ->whereHas('hierKey', fn($q) => $q->where('Hiky_Levl', '<', $level))
            ->orderBy('Ofic_Name')
            ->get(['Ofic_UIN', 'Ofic_Name', 'Ofic_Code']);
    }

    // Adjust table / column names to your geo masters.
    public function countries(): Collection
    {
        return DB::table('tbl_mast_cutr')->orderBy('Cutr_Comp_Name')->pluck('Cutr_Comp_Name', 'Cutr_UIN');
    }

    public function states(?string $cutr): Collection
    {
        return $cutr
            ? DB::table('tbl_mast_stat')->where('Cutr_UIN', $cutr)->orderBy('Stat_Comp_Name')->pluck('Stat_Comp_Name', 'Stat_UIN')
            : collect();
    }

    public function districts(?string $stat): Collection
    {
        return $stat
            ? DB::table('tbl_mast_dist')->where('Stat_UIN', $stat)->orderBy('Dist_Comp_Name')->pluck('Dist_Comp_Name', 'Dist_UIN')
            : collect();
    }

    public function create(string $orga, array $data, string $userUin): MastOfic
    {
        return MastOfic::create($data + ['Orga_UIN' => $orga, 'CrBy' => $userUin]);
    }

    public function update(MastOfic $office, array $data, string $userUin): MastOfic
    {
        $office->update($data + ['MoBy' => $userUin]);

        return $office;
    }

    public function delete(MastOfic $office, string $userUin): void
    {
        if ($office->children()->exists()) {
            throw ValidationException::withMessages([
                'office' => 'This office has child offices. Delete or move them first.',
            ]);
        }

        $office->forceFill(['DeBy' => $userUin])->save();
        $office->delete();
    }
}
