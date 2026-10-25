<?php

namespace App\Services\ApplicationManagement;

use App\Enums\InterviewStepStatus;
use App\Models\MastIntvQstn;
use App\Repositories\Contracts\MastIntvQstnRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MastIntvQstnService
{
    public function __construct(protected MastIntvQstnRepositoryInterface $questions) {}

    public function listForOrganisation(string $orgaUin, ?string $search = null): Collection
    {
        return $this->questions->listForOrganisation($orgaUin, $search);
    }

    public function find(string $orgaUin, string $uin): ?MastIntvQstn
    {
        return $this->questions->findInOrganisation($orgaUin, $uin);
    }

    public function create(string $orgaUin, string $text, InterviewStepStatus|string $status, string $userId): MastIntvQstn
    {
        return $this->questions->create([
            'Question_Text' => trim($text),
            'Intv_qstn_Stau' => $status,
            'Orga_UIN' => $orgaUin,
            'Cr_By' => $userId,
            'Mo_By' => $userId,
        ]);
    }

    public function update(MastIntvQstn $question, array $data, string $userId): MastIntvQstn
    {
        $changes = array_intersect_key($data, array_flip(['Question_Text', 'Intv_qstn_Stau']));

        if (isset($changes['Question_Text'])) {
            $changes['Question_Text'] = trim($changes['Question_Text']);
        }

        $changes['Mo_By'] = $userId;

        return $this->questions->update($question, $changes);
    }

    public function delete(MastIntvQstn $question, string $userId): bool
    {
        return $this->questions->softDelete($question, $userId);
    }
}
