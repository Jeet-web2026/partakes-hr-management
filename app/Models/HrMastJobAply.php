<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'App_Stus',
        'Distr',
        'State',
        'Stage_UIN',
        'Intv_Step_UIN',
        'IS_Vf',
        'Vr_By',
        'Vr_At',
        'Vr_Type'
    ];

    protected $casts = [
        'DOB'       => 'date',
        'Own_Mo'    => 'boolean',
        'Own_Lptp'  => 'boolean',
        'Drvi_Lisc' => 'boolean',
        'Rlca'      => 'boolean',
        'Vr_At'     => 'datetime',
    ];

    public function country(): HasOne
    {
        return $this->hasOne(CountryMaster::class, 'Admn_Cutr_Mast_UIN', 'Cutr_UIN');
    }

    public function interestedpositionDetails(): HasOne
    {
        return $this->hasOne(MastDsig::class, 'Dsig_UIN', 'Job_Intr');
    }

    public function sharedLinkDetails(): HasOne
    {
        return $this->hasOne(ShareLink::class, 'Link_UIN', 'Link_UIN');
    }

    public function company(): HasOne
    {
        return $this->hasOne(OrganisationDetails::class, 'Orga_UIN', 'Orga_UIN');
    }

    public function flags(): BelongsToMany
    {
        return $this->belongsToMany(
            MastHrFlag::class,
            'HR_Aply_Flag',
            'Aply_UIN',
            'hr_flag_UIN'
        );
    }

    public function queryHistory(): HasMany
    {
        return $this->hasMany(IntvQueryAndStatus::class, 'Appl_UIN', 'Aply_UIN');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(InterviewStep::class, 'Stage_UIN', 'Stage_UIN');
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(MastIntvStep::class, 'Intv_Step_UIN', 'Intv_Step_UIN');
    }

    public function isVerified(): HasOne
    {
        return $this->hasOne(MastStau::class, 'Stau_UIN', 'IS_Vf');
    }

    public function verifiedBy(): HasOne
    {
        return $this->hasOne(User::class, 'User_UIN', 'Vr_By');
    }

    public function posting(): HasOne
    {
        return $this->hasOne(EmplPosting::class, 'Aply_UIN', 'Aply_UIN');
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([$this->FaNm, $this->MiNm, $this->SuNm])));
    }
}
