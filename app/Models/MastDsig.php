<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MastDsig extends Model
{
    use HasFactory;

    public const CREATED_AT = 'CrOn';
    public const UPDATED_AT = 'MoOn';

    protected $table = 'tbl_mast_dsig';

    protected $primaryKey = 'Dsig_UIN';

    protected $fillable = [
        'Dsig_Name',
        'Dsig_Desp',
        'Dsig_Stau',
        'Org_UIN',
        'CrBy',
        'MoBy',
    ];

    protected function casts(): array
    {
        return [
            'Dsig_Stau' => 'integer',
            'Org_UIN' => 'integer',
            'CrOn' => 'datetime',
            'MoOn' => 'datetime',
        ];
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(MastStau::class, 'Dsig_Stau', 'Stau_UIN');
    }
}
