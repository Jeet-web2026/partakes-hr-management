<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntvQueryAndStatus extends Model
{
    protected $table = 'tbl_mast_intv_query_and_status_update';
    protected $primaryKey = 'Qury_UIN';

    const CREATED_AT = 'CrOn';
    const UPDATED_AT = 'MoOn';

    protected $fillable = [
        'Org_UIN',
        'Appl_UIN',
        'Stage_UIN',
        'Intv_Step_UIN',
        'Qury_Selected',
        'Ques_UIN',
        'Qury_Answer',
        'Qury_Marks',
        'CrBy',
        'MoBy',
    ];

    protected $casts = [
        'Qury_Selected' => 'boolean',
        'Qury_Marks'    => 'decimal:2',
    ];
}
