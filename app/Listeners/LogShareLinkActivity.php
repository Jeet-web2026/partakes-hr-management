<?php

namespace App\Listeners;

use App\Events\ShareLinkCreated;
use App\Events\ShareLinkDeleted;
use App\Events\ShareLinkUpdated;
use App\Models\MastSbcrLogBook;
use App\Models\ShareLink;
use App\Services\Common\UinGeneratorService;
use Illuminate\Support\Facades\Auth;

class LogShareLinkActivity
{
    // NOTE: pick real, unused category UINs from your MastSbcrCatg lookup —
    // these are placeholders following on from your 304/305/306 draft set.
    private const CATG_SHARE_LINK_CREATED = 307;
    private const CATG_SHARE_LINK_UPDATED = 308;
    private const CATG_SHARE_LINK_DELETED = 309;

    public function handleCreated(ShareLinkCreated $event): void
    {
        $this->writeLog(
            catgUin: self::CATG_SHARE_LINK_CREATED,
            desc: "Share link created: {$event->shareLink->Link_UIN} (type: {$event->shareLink->Link_Type}, target: {$event->shareLink->Link_Foreign_Id_UIN})",
            shareLink: $event->shareLink,
        );
    }

    public function handleUpdated(ShareLinkUpdated $event): void
    {
        $changedFields = array_keys($event->shareLink->getChanges());
        $fieldsList = !empty($changedFields) ? implode(', ', $changedFields) : 'none';

        $this->writeLog(
            catgUin: self::CATG_SHARE_LINK_UPDATED,
            desc: "Share link updated: {$event->shareLink->Link_UIN} (fields: {$fieldsList})",
            shareLink: $event->shareLink,
        );
    }

    public function handleDeleted(ShareLinkDeleted $event): void
    {
        $this->writeLog(
            catgUin: self::CATG_SHARE_LINK_DELETED,
            desc: "Share link deleted: {$event->shareLink->Link_UIN}",
            shareLink: $event->shareLink,
        );
    }

    private function writeLog(int $catgUin, string $desc, ShareLink $shareLink): void
    {
        $foreign = $shareLink->foreign;

        MastSbcrLogBook::create([
            'Log_book_uin' => UinGeneratorService::generate(),
            'Sbcr_Rvnu_Catg_Name_UIN' => $catgUin,
            'Indv_Orga' => 'O',
            'Indv_Orga_Uin' => $foreign->Orga_UIN ?? 1500000001,
            'Cont' => '1',
            'UOM' => 'Single',
            'Size' => 0.00,
            'Vert_Uin' => $foreign->vert_uin ?? 113,
            'Desc' => $desc,
            'User_UIN' => Auth::id() ?? 1,
            'CrOn' => now(),
        ]);
    }
}
