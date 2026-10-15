<?php

namespace App\Listeners;

use App\Events\AgreementCreated;
use App\Events\AgreementDeleted;
use App\Events\AgreementUpdated;
use App\Models\Agreement;
use App\Models\MastSbcrLogBook;
use App\Services\Common\UinGeneratorService;
use Illuminate\Support\Facades\Auth;

class LogAgreementActivity
{
    private const CATG_AGREEMENT_CREATED = 301;
    private const CATG_AGREEMENT_UPDATED = 302;
    private const CATG_AGREEMENT_DELETED = 303;

    public function handleCreated(AgreementCreated $event): void
    {
        $this->writeLog(
            catgUin: self::CATG_AGREEMENT_CREATED,
            desc: "Agreement created: {$event->agreement->Agmt_UIN}",
            agreement: $event->agreement,
        );
    }

    public function handleUpdated(AgreementUpdated $event): void
    {
        $changedFields = array_keys($event->agreement->getChanges());
        $fieldsList = !empty($changedFields) ? implode(', ', $changedFields) : 'none';

        $this->writeLog(
            catgUin: self::CATG_AGREEMENT_UPDATED,
            desc: "Agreement updated: {$event->agreement->Agmt_UIN} (fields: {$fieldsList})",
            agreement: $event->agreement,
        );
    }

    public function handleDeleted(AgreementDeleted $event): void
    {
        $this->writeLog(
            catgUin: self::CATG_AGREEMENT_DELETED,
            desc: "Agreement deleted: {$event->agreement->Agmt_UIN}",
            agreement: $event->agreement,
        );
    }

    private function writeLog(int $catgUin, string $desc, Agreement $agreement): void
    {
        MastSbcrLogBook::create([
            'Log_book_uin' => UinGeneratorService::generate(),
            'Sbcr_Rvnu_Catg_Name_UIN' => $catgUin,
            'Indv_Orga' => 'O',
            'Indv_Orga_Uin' => $agreement->Orga_UIN ?? 1500000001,
            'Cont' => '1',
            'UOM' => 'Single',
            'Size' => 0.00,
            'Vert_Uin' => $agreement->vert_uin ?? 113,
            'Desc' => $desc,
            'User_UIN' => Auth::id() ?? 1,
            'CrOn' => now(),
        ]);
    }
}
