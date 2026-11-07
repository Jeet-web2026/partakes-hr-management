<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class MastIntvStep extends Model
{
    public const CREATED_AT = 'Cr_On';
    public const UPDATED_AT = 'Mo_On';

    protected $table = 'tbl_mast_intv_step';
    protected $primaryKey = 'Intv_Step_UIN';

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
            'Intv_Step_Stau' => 'integer',
            'Cr_On' => 'datetime',
            'Mo_On' => 'datetime'
        ];
    }

    // Soft delete is marked by Del_By (the schema has no deleted_at).
    protected static function booted(): void
    {
        static::addGlobalScope('notDeleted', function (Builder $query) {
            $query->whereNull($query->getModel()->getTable() . '.Del_By');
        });

        static::saved(function (self $question) {
            $question->forgetQuestionCache();
        });

        static::deleted(function (self $question) {
            $question->forgetQuestionCache();
        });
    }

    public function scopeForOrganisation(Builder $query, string $orgaUin): Builder
    {
        return $query->where('Orga_UIN', $orgaUin);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('Intv_Step_Stau', 100201);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(InterviewStep::class, 'Intv_Stage', 'Stage_UIN');
    }

    protected function placeholderList(): Attribute
    {
        return Attribute::get(fn() => $this->Intv_Step_Placeholder_Type);
    }

    protected function nameList(): Attribute
    {
        return Attribute::get(fn() => json_decode($this->Intv_Step_Name));
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(MastStau::class, 'Intv_Step_Stau', 'Stau_UIN');
    }

    public function forgetQuestionCache(): void
    {
        Cache::clear();
    }

}
