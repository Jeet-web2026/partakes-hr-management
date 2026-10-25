<?php

namespace App\Models;

use App\Enums\InterviewStepStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MastIntvStep extends Model
{
    use HasUuids;

    public const CREATED_AT = 'Cr_On';
    public const UPDATED_AT = 'Mo_On';

    protected $table = 'tbl_mast_intv_step';
    protected $primaryKey = 'Intv_Step_UIN';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Intv_Step_Numb',
        'Intv_Step_Name',
        'Intv_Step_Stau',
        'Orga_UIN',
        'Cr_By',
        'Mo_By',
        'Del_By',
    ];

    protected function casts(): array
    {
        return [
            'Intv_Step_Numb' => 'integer',
            'Intv_Step_Stau' => InterviewStepStatus::class,
            'Cr_On' => 'datetime',
            'Mo_On' => 'datetime',
        ];
    }

    // Soft delete is marked by Del_By (the schema has no deleted_at).
    protected static function booted(): void
    {
        static::addGlobalScope('notDeleted', function (Builder $query) {
            $query->whereNull($query->getModel()->getTable() . '.Del_By');
        });
    }

    public function scopeForOrganisation(Builder $query, string $orgaUin): Builder
    {
        return $query->where('Orga_UIN', $orgaUin);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('Intv_Step_Stau', InterviewStepStatus::Active->value);
    }
}
