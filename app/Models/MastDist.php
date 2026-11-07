<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MastDist extends Model
{
    protected $table = 'tbl_mast_dist';
    protected $primaryKey = 'Dist_UIN';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'Dist_UIN',
        'Stat_UIN',
        'Dist_Comp_Name',
        'Dist_Not_List',
        'Dist_Shrt_Name',
        'Stau_UIN',
        'CrBy',
        'CrOn',
        'MoBy',
        'MoOn',
        'VfBy',
        'VfOn',
        'Del_BY',
        'Del_On',
    ];

    protected $casts = [
        'CrOn'   => 'date',
        'MoOn'   => 'date',
        'VfOn'   => 'date',
        'Del_On' => 'date',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(MastStat::class, 'Stat_UIN', 'Stat_UIN');
    }
}
