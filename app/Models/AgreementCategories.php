<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgreementCategories extends Model
{
    protected $table = 'tbl_mast_agmt_catg';
    

    protected $fillable = [
        'Agmt_Catg_Name',
        'Agmt_Parn_Catg_UIN',
        'Stau_UIN',
        'CrBy',
        'MoBy',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            AgreementCategories::class,
            'Agmt_Parn_Catg_UIN',
            'Agmt_Catg_UIN'
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            AgreementCategories::class,
            'Agmt_Parn_Catg_UIN',
            'Agmt_Catg_UIN'
        );
    }
}
