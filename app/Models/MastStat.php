<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MastStat extends Model
{
    protected $table = 'tbl_mast_stat';
    protected $primaryKey = 'Stat_UIN';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'Stat_UIN',
        'Cutr_UIN',
        'Stat_Comp_Name',
        'Stat_Type',
        'Stat_Shrt_Name',
        'City_UIN',
        'Stau_UIN',
        'CrBy',
        'MoBy',
        'MoOn',
        'VfOn',
        'VfBy',
    ];

    protected $casts = [
        'MoOn' => 'datetime',
        'VfOn' => 'datetime',
    ];

    public function districts(): HasMany
    {
        return $this->hasMany(MastDist::class, 'Stat_UIN', 'Stat_UIN');
    }
}
