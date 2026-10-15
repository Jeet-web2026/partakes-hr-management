<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AgmtNameResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->Agmt_Name_UIN,
            'name'          => $this->Agmt_Name,
            'categoryLabel' => $this->category?->Agmt_Catg_Name,
            'type'          => $this->type?->Agmt_Type_Name,
            'createdAt'     => $this->CrOn?->toDateString(),
            'draftCount'    => $this->drafts_count ?? 0,
        ];
    }
}
