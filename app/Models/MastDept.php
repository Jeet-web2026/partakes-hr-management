<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MastDept extends Model
{
    protected $table = 'tbl_mast_dept';

    protected $primaryKey = 'Dept_UIN';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Dept_UIN',
        'Orga_UIN',
        'Dept_name',
        'parn_Dept_id',
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
            'Dept_UIN' => 'integer',
            'Orga_UIN' => 'integer',
            'parn_Dept_id' => 'integer',
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
        static::creating(function (self $dept): void {
            if (empty($dept->Dept_UIN)) {
                $uin = now()->timestamp;

                while (static::query()->whereKey($uin)->exists()) {
                    $uin++;
                }

                $dept->Dept_UIN = $uin;
            }

            $dept->CrOn ??= now()->timestamp;
        });

        static::updating(function (self $dept): void {
            $dept->MoOn = now()->timestamp;
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parn_Dept_id', 'Dept_UIN');
    }

    public function children(): HasMany
    {
        // Excludes rows that are their own parent (e.g. "Start").
        return $this->hasMany(self::class, 'parn_Dept_id', 'Dept_UIN')
            ->whereColumn('Dept_UIN', '!=', 'parn_Dept_id');
    }
}
