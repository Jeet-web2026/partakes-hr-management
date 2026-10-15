<?php

namespace App\Listeners;

use App\Events\AgreementNameCreated;
use App\Events\AgreementNameDeleted;
use App\Events\AgreementNameUpdated;
use App\Models\AgreementName;
use App\Models\MastSbcrLogBook;
use App\Services\Common\UinGeneratorService;
use Illuminate\Support\Facades\Auth;

class LogAgreementNameActivity
{
    // NOTE: pick real, unused category UINs from your MastSbcrCatg lookup —
    // placeholders continuing on from your Draft set (304/305/306).
    private const CATG_AGMT_NAME_CREATED = 307;
    private const CATG_AGMT_NAME_UPDATED = 308;
    private const CATG_AGMT_NAME_DELETED = 309;

    public function handleCreated(AgreementNameCreated $event): void
    {
        $this->writeLog(
            catgUin: self::CATG_AGMT_NAME_CREATED,
            desc: "Agreement name created: {$event->agreementName->Agmt_Name_UIN}",
            agreementName: $event->agreementName,
        );
    }

    public function handleUpdated(AgreementNameUpdated $event): void
    {
        $changedFields = array_keys($event->agreementName->getChanges());
        $fieldsList = !empty($changedFields) ? implode(', ', $changedFields) : 'none';

        $this->writeLog(
            catgUin: self::CATG_AGMT_NAME_UPDATED,
            desc: "Agreement name updated: {$event->agreementName->Agmt_Name_UIN} (fields: {$fieldsList})",
            agreementName: $event->agreementName,
        );
    }

    public function handleDeleted(AgreementNameDeleted $event): void
    {
        $this->writeLog(
            catgUin: self::CATG_AGMT_NAME_DELETED,
            desc: "Agreement name deleted: {$event->agreementName->Agmt_Name_UIN}",
            agreementName: $event->agreementName,
        );
    }

    private function writeLog(int $catgUin, string $desc, AgreementName $agreementName): void
    {
        MastSbcrLogBook::create([
            'Log_book_uin' => UinGeneratorService::generate(),
            'Sbcr_Rvnu_Catg_Name_UIN' => $catgUin,
            'Indv_Orga' => 'O',
            'Indv_Orga_Uin' => $agreementName->Orga_UIN ?? 1500000001,
            'Cont' => '1',
            'UOM' => 'Single',
            'Size' => 0.00,
            'Vert_Uin' => $agreementName->vert_uin ?? 113,
            'Desc' => $desc,
            'User_UIN' => Auth::id() ?? 1,
            'CrOn' => now(),
        ]);
    }
}
