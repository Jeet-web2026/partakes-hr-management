<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgreementType extends Model
{
    protected $table = 'tbl_mast_agmt_type';

    protected $fillable = [
        'Agmt_Catg_UIN',
        'Agmt_Type_Name',
        'Stau_UIN',
        'CrBy',
        'MoBy',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            AgreementCategories::class,
            'Agmt_Catg_UIN',
            'Agmt_Catg_UIN'
        );
    }
}
