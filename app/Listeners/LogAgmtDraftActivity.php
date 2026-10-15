<?php

namespace App\Listeners;

use App\Events\AgmtDraftCreated;
use App\Events\AgmtDraftDeleted;
use App\Events\AgmtDraftUpdated;
use App\Models\AgmtDraft;
use App\Models\MastSbcrLogBook;
use App\Services\Common\UinGeneratorService;
use Illuminate\Support\Facades\Auth;

class LogAgmtDraftActivity
{
    // NOTE: pick real, unused category UINs from your MastSbcrCatg lookup —
    // these are placeholders following on from your 301/302/303 agreement set.
    private const CATG_AGMT_DRAFT_CREATED = 304;
    private const CATG_AGMT_DRAFT_UPDATED = 305;
    private const CATG_AGMT_DRAFT_DELETED = 306;

    public function handleCreated(AgmtDraftCreated $event): void
    {
        $this->writeLog(
            catgUin: self::CATG_AGMT_DRAFT_CREATED,
            desc: "Agreement draft created: {$event->agmtDraft->Agmt_Drft_UIN}",
            agmtDraft: $event->agmtDraft,
        );
    }

    public function handleUpdated(AgmtDraftUpdated $event): void
    {
        $changedFields = array_keys($event->agmtDraft->getChanges());
        $fieldsList = !empty($changedFields) ? implode(', ', $changedFields) : 'none';

        $this->writeLog(
            catgUin: self::CATG_AGMT_DRAFT_UPDATED,
            desc: "Agreement draft updated: {$event->agmtDraft->Agmt_Drft_UIN} (fields: {$fieldsList})",
            agmtDraft: $event->agmtDraft,
        );
    }

    public function handleDeleted(AgmtDraftDeleted $event): void
    {
        $this->writeLog(
            catgUin: self::CATG_AGMT_DRAFT_DELETED,
            desc: "Agreement draft deleted: {$event->agmtDraft->Agmt_Drft_UIN}",
            agmtDraft: $event->agmtDraft,
        );
    }

    private function writeLog(int $catgUin, string $desc, AgmtDraft $agmtDraft): void
    {
        MastSbcrLogBook::create([
            'Log_book_uin' => UinGeneratorService::generate(),
            'Sbcr_Rvnu_Catg_Name_UIN' => $catgUin,
            'Indv_Orga' => 'O',
            'Indv_Orga_Uin' => $agmtDraft->Orga_UIN ?? 1500000001,
            'Cont' => '1',
            'UOM' => 'Single',
            'Size' => 0.00,
            'Vert_Uin' => $agmtDraft->vert_uin ?? 113,
            'Desc' => $desc,
            'User_UIN' => Auth::id() ?? 1,
            'CrOn' => now(),
        ]);
    }
}
