<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryMaster extends Model
{
    protected $table = 'tbl_mast_cutr';

    protected $primaryKey = 'Admn_Cutr_Mast_UIN';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Admn_Cutr_Mast_UIN',
        'Name',
        'Code',
        'Phon_Code',
        'Flag_Emoj',
        'MoNo_Digt',
        'Stau_UIN',
        'CrBy',
        'CrOn',
        'MoBy',
        'MoOn',
        'VfBy',
        'VfOn',
        'Del_By',
        'Del_On',
    ];

    protected $casts = [
        'Admn_Cutr_Mast_UIN' => 'integer',
        'MoNo_Digt'          => 'integer',
        'Stau_UIN'           => 'integer',
        'CrOn'               => 'datetime',
        'MoOn'               => 'datetime',
        'VfOn'               => 'datetime',
        'Del_On'             => 'datetime',
    ];
}
