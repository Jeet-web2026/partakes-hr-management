<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MastOfic extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_mast_ofic';
    protected $primaryKey = 'Ofic_UIN';

    public const CREATED_AT = 'CrOn';
    public const UPDATED_AT = 'MoOn';
    public const DELETED_AT = 'DeOn';

    protected $guarded = [];

    protected $casts = [
        'Ofic_Strt_Dt' => 'date',
        'Ofic_Clos_Dt' => 'date',
    ];

    public function hierKey(): BelongsTo
    {
        return $this->belongsTo(HierKey::class, 'Hiky_UIN', 'Hiky_UIN');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'Parn_Ofic_UIN', 'Ofic_UIN');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'Parn_Ofic_UIN', 'Ofic_UIN');
    }

    // Copy the same relation you already have on HierKey (same user model and keys).
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'CrBy', 'User_UIN');
    }
}
