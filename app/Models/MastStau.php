<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MastStau extends Model
{
    use HasFactory;

    protected $table = 'tbl_mast_stau';

    protected $primaryKey = 'Stau_UIN';

    // UINs are assigned manually (2, 101, 100201...)
    public $incrementing = false;

    protected $keyType = 'int';

    // Table has no CrOn/created_at/updated_at columns
    public $timestamps = false;

    protected $fillable = [
        'Stau_UIN',
        'Orga_UIN',
        'Stau_Name',
        'Stau_Desp',
        'CrBy',
        'MoBy',
        'MoOn',
        'VfOn',
        'VfBy',
    ];

    protected function casts(): array
    {
        return [
            'Stau_UIN' => 'integer',
            'Orga_UIN' => 'integer',
            'MoOn' => 'datetime',
            'VfOn' => 'datetime',
        ];
    }

    public function designations(): HasMany
    {
        return $this->hasMany(MastDsig::class, 'Dsig_Stau', 'Stau_UIN');
    }
}
