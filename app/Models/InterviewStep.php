<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InterviewStep extends Model
{
    public const CREATED_AT = 'Cr_On';
    public const UPDATED_AT = 'Mo_On';

    protected $table = 'tbl_mast_intv_stage';

    protected $fillable = [
        'Stage_Name',
        'Stage_Status',
        'Orga_UIN',
        'Cr_By',
        'Mo_By'
    ];

    protected function casts(): array
    {
        return [
            'Stage_Status' => 'boolean',
            'Cr_On' => 'datetime',
            'Mo_On' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('Stage_Status', true);
    }

    public function scopeForOrganisation(Builder $query, string $orgaUin): Builder
    {
        return $query->where('Orga_UIN', $orgaUin);
    }
}
