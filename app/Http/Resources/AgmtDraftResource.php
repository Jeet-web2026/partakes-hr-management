<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgmtDraftResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->Agmt_Name,
            'categoryLabel'           => $this->category?->Agmt_Catg_Name,
            'type'                    => $this->type?->Agmt_Type_Name,    
            'version'                 => $this->Agmt_Vson,
            'approvedBy'              => $this->Agmt_Aprv_By,
            'approvedDate'            => $this->Agmt_Aprv_Date?->format('Y-m-d'),
            'uploadDraftFileName'     => $this->Agmt_Drft_File_Name,
            'uploadChecklistFileName' => $this->Agmt_Chklst_File_Name,
        ];
    }
}