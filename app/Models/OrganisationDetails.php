<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganisationDetails extends Model
{
    protected $table = "tbl_mast_orga";

    protected $fillable = [
        'Orga_Type_UIN',
        'Orga_Name',
        'orga_Logo',
        'Dt_of_Incp',
        'Fnal_Year_Strt_Dt',
        'Actn_Strt_Dt',
        'Web_Addr',
        'Regi_Addr',
        'Cnta_Pers',
        'Admt_Pers_1st',
        'Admt_Pers_2nd',
        'Admt_Pers_3rd',
        'Admt_Pers_4th',
        'Assc_Part_Code',
        'Sytm_Admt_Pers',
        'Eft_Dt',
        'Renw_Dt',
        'Expy_Dt',
        'Orga_Del_Dt',
        'Stau_UIN',
        'CrBy',
        'CrOn',
        'MoBy',
        'MoOn',
        'VfOn',
        'VfBy',
        'Stat_UIN',
        'pin_UIN',
        'Prmy_Emai',
        'Prmy_Emai_Vefi',
        'mpos_key',
    ];

    public function userRelations(): HasMany
    {
        return $this->hasMany(UserOrgaRela::class, 'Orga_UIN', 'Orga_UIN');
    }

    public function agreementNames(): HasMany
    {
        return $this->hasMany(AgreementName::class, 'Orga_UIN', 'Orga_UIN');
    }
}
