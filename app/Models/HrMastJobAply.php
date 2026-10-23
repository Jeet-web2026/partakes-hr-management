<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HrMastJobAply extends Model
{
    protected $table = 'HR_Mast_Job_Aply';
    protected $primaryKey = 'Aply_UIN';

    const CREATED_AT = 'CrBy';
    const UPDATED_AT = 'UpBy';

    protected $fillable = [
        'FaNm',
        'MiNm',
        'SuNm',
        'Gend',
        'DOB',
        'MoTu',
        'Cutr_UIN',
        'PinCod_UIN',
        'Mo_No',
        'Emai',
        'Own_Mo',
        'Mo_Type',
        'MO_OS',
        'Own_Lptp',
        'Lptp_OS',
        'Drvi_Lisc',
        'Vcle_Type_Lisc',
        'Rlca',
        'Rlca_Area',
        'Job_Intr',
        'Job_Type',
        'Bio_Data_Path',
        'Link_UIN',
        'Orga_UIN',
        'App_Stus'
    ];

    protected $casts = [
        'DOB'       => 'date',
        'Own_Mo'    => 'boolean',
        'Own_Lptp'  => 'boolean',
        'Drvi_Lisc' => 'boolean',
        'Rlca'      => 'boolean',
    ];

    public function country(): HasOne
    {
        return $this->hasOne(CountryMaster::class, 'Admn_Cutr_Mast_UIN', 'Cutr_UIN');
    }
}
