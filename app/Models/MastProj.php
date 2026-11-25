<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MastProj extends Model
{
    protected $table = 'tbl_mast_proj';

    protected $primaryKey = 'Proj_UIN';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Proj_UIN',
        'Orga_UIN',
        'Proj_name',
        'parn_Proj_id',
        'Chld_grup',
        'ldgr',
        'Stau_UIN',
        'CrBy',
        'CrOn',
        'MoBy',
        'MoOn',
        'VfOn',
        'VfBy',
    ];

    protected function casts(): array
    {
        return [
            'Proj_UIN' => 'integer',
            'Orga_UIN' => 'integer',
            'parn_Proj_id' => 'integer',
            'Chld_grup' => 'integer',
            'ldgr' => 'integer',
            'Stau_UIN' => 'integer',
            'CrBy' => 'integer',
            'CrOn' => 'integer',
            'MoBy' => 'integer',
            'MoOn' => 'integer',
            'VfOn' => 'integer',
            'VfBy' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Replaces the BEFORE INSERT trigger from the SQL dump.
        static::creating(function (self $proj): void {
            if (empty($proj->Proj_UIN)) {
                $uin = now()->timestamp;

                while (static::query()->whereKey($uin)->exists()) {
                    $uin++;
                }

                $proj->Proj_UIN = $uin;
            }

            $proj->CrOn ??= now()->timestamp;
        });

        static::updating(function (self $proj): void {
            $proj->MoOn = now()->timestamp;
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parn_Proj_id', 'Proj_UIN');
    }

    public function children(): HasMany
    {
        // Excludes rows that are their own parent (e.g. "Start").
        return $this->hasMany(self::class, 'parn_Proj_id', 'Proj_UIN')
            ->whereColumn('Proj_UIN', '!=', 'parn_Proj_id');
    }
}
