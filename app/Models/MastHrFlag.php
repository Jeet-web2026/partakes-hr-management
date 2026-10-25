<?php

namespace App\Models;

use App\Enums\HrFlagStage;
use App\Enums\InterviewStepStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MastHrFlag extends Model
{
    use HasUuids;

    public const CREATED_AT = 'Cr_On';
    public const UPDATED_AT = 'Mo_On';

    protected $table = 'tbl_mast_hr_flag';
    protected $primaryKey = 'hr_flag_UIN';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'hr_flag_stge_name',
        'hr_flag_name',
        'hr_flag_stau',
        'Orga_UIN',
        'Cr_By',
        'Mo_By',
        'Del_By',
    ];

    protected function casts(): array
    {
        return [
            'hr_flag_stge_name' => HrFlagStage::class,
            'hr_flag_stau' => InterviewStepStatus::class,
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
        return $query->where('hr_flag_stau', InterviewStepStatus::Active->value);
    }

    public function scopeForStage(Builder $query, HrFlagStage $stage): Builder
    {
        return $query->where('hr_flag_stge_name', $stage->value);
    }
}
