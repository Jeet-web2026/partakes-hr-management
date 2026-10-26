<?php

namespace App\Models;

use App\Enums\InterviewStepStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MastIntvQstn extends Model
{
    use HasUuids;

    public const CREATED_AT = 'Cr_On';
    public const UPDATED_AT = 'Mo_On';

    protected $table = 'tbl_mast_intv_qstn';
    protected $primaryKey = 'intv_qstn_UIN';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Question_Text',
        'Intv_qstn_Stau',
        'Orga_UIN',
        'Cr_By',
        'Mo_By',
        'Del_By',
    ];

    protected function casts(): array
    {
        return [
            'Intv_qstn_Stau' => InterviewStepStatus::class,
            'Cr_On' => 'datetime',
            'Mo_On' => 'datetime',
        ];
    }

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
        return $query->where('Intv_qstn_Stau', InterviewStepStatus::Active->value);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'Cr_By', 'User_UIN');
    }
}
