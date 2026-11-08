<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HierKey extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_mast_hierkey';
    protected $primaryKey = 'Hiky_UIN';

    public $timestamps = false;
    const DELETED_AT = 'DeOn';

    protected $fillable = [
        'Orga_UIN',
        'Hiky_Levl',
        'Hiky_Levl_Name',
        'Stau_UIN',
        'CrBy',
        'MoBy',
        'MoOn',
        'DeBy',
        'DeOn',
    ];

    protected $casts = [
        'Hiky_Levl' => 'integer',
        'Stau_UIN' => 'integer',
        'CrOn' => 'datetime',
        'MoOn' => 'datetime',
        'DeOn' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'CrBy');
    }

    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'MoBy');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'DeBy');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(MastStat::class, 'Stau_UIN', 'Stat_UIN');
    }
}
