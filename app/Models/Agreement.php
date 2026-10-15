<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    protected $table = "tbl_tran_agmt";
    protected $primaryKey = 'Agmt_UIN';
    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'Orga_UIN',
        'User_UIN',

        'Agmt_Catg_UIN',
        'Agmt_Type_UIN',
        'Agmt_Name',
        'Agmt_File_no',

        'Agmt_Strt_Date',
        'Agmt_Expy_Date',
        'Agmt_Sign_Date',
        'Agmt_Ntry_Date',

        'Agmt_Vson',
        'Agmt_Drft_By',
        'Agmt_Judn',
        'Agmt_Atch',
        'Agmt_Remk',

        'Agmt_Parn_UIN',
        'Agmt_Prty_UIN',

        'CrBy',
        'MoBy',

        'Stau_UIN',
        'update_count',
        'Agmt_Size_KB',
        'Agmt_Ref_No',
        'Agmt_Depst_Amt',
        'RenwOn',
        'ExpOn',
        'Agmt_Signed',
        'Signed_Recv_Date',
        'Agmt_Recv_Type'
    ];

    protected $casts = [
        'Agmt_Strt_Date' => 'datetime',
        'Agmt_Expy_Date' => 'datetime',
        'Agmt_Sign_Date' => 'datetime',
        'Agmt_Ntry_Date' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($agreement) {
            if (empty($agreement->Agmt_UIN)) {
                do {
                    $candidate = now()->timestamp;
                } while (self::where('Agmt_UIN', $candidate)->exists());

                $agreement->Agmt_UIN = $candidate;
            }
            if ($agreement->Stau_UIN === 'expired') {
                $agreement->ExpOn = now();
            }
        });

        static::updating(function ($agreement) {
            if ($agreement->Stau_UIN === 'expired' && $agreement->isDirty('Stau_UIN')) {
                $agreement->ExpOn = now();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(
            AgreementCategories::class,
            'Agmt_Catg_UIN',
            'Agmt_Catg_UIN'
        );
    }

    public function type()
    {
        return $this->belongsTo(
            AgreementType::class,
            'Agmt_Type_UIN',
            'Agmt_Type_UIN'
        );
    }

    public function parties()
    {
        return $this->hasMany(
            AgreementParty::class,
            'Agmt_UIN',
            'Agmt_UIN'
        );
    }

    public function childAgreements()
    {
        return $this->hasMany(
            Agreement::class,
            'Agmt_Parn_UIN',
            'Agmt_UIN'
        );
    }

    public function parentAgreement()
    {
        return $this->belongsTo(
            Agreement::class,
            'Agmt_Parn_UIN',
            'Agmt_UIN'
        );
    }

    public function getRenewalHistory()
    {
        $root = $this;
        while ($root->parentAgreement) {
            $root = $root->parentAgreement;
        }

        $history = collect();
        $current = $root;

        while ($current) {
            $history->push($current);
            $current = $current->childAgreements()->first();
        }

        return $history->reverse()->values();
    }

    public function remarks()
    {
        return $this->hasMany(TranAgmtRemk::class, 'agmt_uin', 'Agmt_UIN')
            ->orderBy('agmt_remk_uin', 'desc');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'User_UIN', 'User_UIN');
    }

    public function organisationDetails()
    {
        return $this->hasOne(OrganisationDetails::class, 'Orga_UIN', 'Orga_UIN');
    }
}
