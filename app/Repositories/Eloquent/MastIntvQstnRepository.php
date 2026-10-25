<?php

namespace App\Repositories\Eloquent;

use App\Models\MastIntvQstn;
use App\Repositories\Contracts\MastIntvQstnRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MastIntvQstnRepository implements MastIntvQstnRepositoryInterface
{
    public function listForOrganisation(string $orgaUin, ?string $search = null): Collection
    {
        $term = trim((string) $search);

        return MastIntvQstn::query()
            ->forOrganisation($orgaUin)
            ->when($term !== '', fn($q) => $q->whereRaw(
                'LOWER(Question_Text) LIKE ?',
                ['%' . mb_strtolower($term) . '%']
            ))
            ->orderBy('Cr_On')
            ->get();
    }

    public function findInOrganisation(string $orgaUin, string $uin): ?MastIntvQstn
    {
        return MastIntvQstn::query()
            ->forOrganisation($orgaUin)
            ->where('intv_qstn_UIN', $uin)
            ->first();
    }

    public function create(array $data): MastIntvQstn
    {
        return MastIntvQstn::create($data);
    }

    public function update(MastIntvQstn $question, array $data): MastIntvQstn
    {
        $question->update($data);

        return $question->refresh();
    }

    public function softDelete(MastIntvQstn $question, string $deletedBy): bool
    {
        return $question->update(['Del_By' => $deletedBy, 'Mo_By' => $deletedBy]);
    }
}
