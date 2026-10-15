<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgreementParty extends Model
{
    use HasFactory;

    protected $table = 'tbl_tran_agmt_prty';

    protected $primaryKey = 'Agmt_Prty_UIN';

    protected $fillable = [
        'Agmt_UIN',
        'Agmt_Prty_No',
        'Orga_UIN',
        'Agmt_Athr_Sgnt',
        'Agmt_Prty_Org_Name',
        'CrBy',
        'MoBy',
    ];

    public function agreement()
    {
        return $this->belongsTo(
            Agreement::class,
            'Agmt_UIN',
            'Agmt_UIN'
        );
    }

    public function organisationDetails()
    {
        return $this->belongsTo(
            OrganisationDetails::class,
            'Orga_UIN',
            'Orga_UIN'
        );
    }
}
