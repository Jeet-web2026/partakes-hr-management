<?php

namespace App\Services\Common;

use App\Models\TranFdbk;

class FeedbackService
{

    public function __construct(
        protected FileStorageService $fileStorageService
    ) {}

    public function store(array $data): TranFdbk
    {

        $feedback = TranFdbk::create([
            'Orga_UIN'          => $data['orga_uin'],
            'Page_UIN'          => $data['page_uin'],
            'Page_load_speed'   => $data['page_load_speed'],
            'Page_Dsin'         => $data['page_design'],
            'Page_Ease_Of_Use'  => $data['ease_of_use'],
            'Page_Oall'         => $data['overall_experience'],
            'Page_InfoClarity'  => $data['information_clarity'],
            'Fdbk_Type'         => $data['feedback_type'],
            'Lokn_for'          => $data['Lokn_for'],
            'Rcmd_Page'         => $data['Rcmd_Page'],
            'Cnta_Us'           => $data['Cnta_Us'],
            'Mesg_Fdbk'         => $data['comments'] ?? null,
            'PinCod'            => $data['pincode'],
            'CrBy'              => $data['created_by'],
            'CrOn'              => now()->timestamp,
            'Fdbk_Attachment'   => null,
        ]);

        if (!empty($data['attachment']) && $data['attachment']->isValid()) {
            $path = $this->fileStorageService->store(
                file: $data['attachment'],
                folder: "attachments/{$feedback->Orga_UIN}",
                primaryKey: $feedback->Fdbk_UIN,
                orgUin: $feedback->Orga_UIN,
                userUin: $feedback->CrBy,
                storageType: 'draft'
            );

            $feedback->update(['Fdbk_Attachment' => $path]);
        }

        return $feedback;
    }
}
