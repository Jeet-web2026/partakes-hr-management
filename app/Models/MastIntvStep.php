<?php

namespace App\Models;

use App\Enums\InterviewStepStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'Intv_Stage',
        'Intv_Step_Name',
        'Intv_Step_Placeholder_Type',
    ];

    protected function casts(): array
    {
        return [
            'Intv_Step_Numb' => 'integer',
            'Intv_Step_Stau' => InterviewStepStatus::class,
            'Cr_On' => 'datetime',
            'Mo_On' => 'datetime',
            'Intv_Step_Name'             => 'array',
            'Intv_Step_Placeholder_Type' => 'array',
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

    public function stage(): BelongsTo
    {
        return $this->belongsTo(InterviewStep::class, 'Intv_Stage', 'Stage_UIN');
    }

    protected function placeholderList(): Attribute
    {
        return Attribute::get(fn() => $this->toList($this->Intv_Step_Placeholder_Type));
    }

    protected function nameList(): Attribute
    {
        return Attribute::get(fn() => $this->toList($this->Intv_Step_Name));
    }

    private function toList(mixed $value): array
    {
        while (is_string($value)) {
            $decoded = json_decode($value, true);

            if ($decoded === null || $decoded === $value) {
                return [];
            }

            $value = $decoded;
        }

        return is_array($value) ? array_values($value) : [];
    }
}
