<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InterviewStep extends Model
{
    public const CREATED_AT = 'Cr_On';
    public const UPDATED_AT = 'Mo_On';

    protected $table = 'tbl_mast_intv_stage';
    protected $primaryKey = 'Stage_UIN';

    protected $fillable = [
        'Stage_Name',
        'Stage_Status',
        'Orga_UIN',
        'Cr_By',
        'Mo_By',
        'Stage_Sort'
    ];

    protected function casts(): array
    {
        return [
            'Cr_On' => 'datetime',
            'Mo_On' => 'datetime',
            'Stage_Status' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('Stage_Status', 100201);
    }

    public function scopeForOrganisation(Builder $query, string $orgaUin): Builder
    {
        return $query->where('Orga_UIN', $orgaUin);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(MastStau::class, 'Stage_Status', 'Stau_UIN');
    }

    public function hasStep(): HasOne
    {
        return $this->hasOne(HrMastJobAply::class, 'Stage_UIN', 'Stage_UIN');
    }
}
