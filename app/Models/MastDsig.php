<?php

namespace App\Models;

use App\Enums\DesignationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'Dsig_Type',
        'Parn_Dsig_UIN',
    ];

    protected function casts(): array
    {
        return [
            'Dsig_Stau' => 'integer',
            'Org_UIN' => 'integer',
            'Parn_Dsig_UIN' => 'integer',
            'CrOn' => 'datetime',
            'MoOn' => 'datetime',
            'Dsig_Type' => DesignationType::class,
        ];
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(MastStau::class, 'Dsig_Stau', 'Stau_UIN');
    }

    // Parn_Dsig_UIN = 0 means "Primary Designation", so this returns null for those rows
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'Parn_Dsig_UIN', 'Dsig_UIN');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'Parn_Dsig_UIN', 'Dsig_UIN');
    }
}
