<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinCode extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'tbl_mast_pin_code';

    /**
     * Uses Laravel's default auto-increment `id` primary key.
     *
     * NOTE: Pin_code_UIN is NOT unique in the source data — the same
     * pincode UIN legitimately repeats across multiple districts /
     * locality types, so it can't serve as the primary key despite
     * the *_UIN naming convention used elsewhere in this project.
     */

    /**
     * This table only tracks creation, not updates.
     */
    const CREATED_AT = 'CrOn';
    const UPDATED_AT = null;

    protected $fillable = [
        'Pin_code_UIN',
        'pin_code_No',
        'Cutr_UIN',
        'Stat_UIN',
        'Dist_UIN',
        'Loca_Type',
        'Stau_UIN',
        'CrBy',
    ];

    protected $casts = [
        'Pin_code_UIN' => 'integer',
        'pin_code_No'  => 'integer',
        'Cutr_UIN'     => 'integer',
        'Stat_UIN'     => 'integer',
        'Dist_UIN'     => 'integer',
        'Stau_UIN'     => 'integer',
        'CrBy'         => 'integer',
    ];

    /*
    |--------------------------------------------------------------------
    | Relationships — uncomment / rename once you confirm the matching
    | master model classes in your project.
    |--------------------------------------------------------------------
    */

    // public function country()
    // {
    //     return $this->belongsTo(Country::class, 'Cutr_UIN', 'Cutr_UIN');
    // }

    // public function state()
    // {
    //     return $this->belongsTo(State::class, 'Stat_UIN', 'Stat_UIN');
    // }

    // public function district()
    // {
    //     return $this->belongsTo(District::class, 'Dist_UIN', 'Dist_UIN');
    // }

    // public function status()
    // {
    //     return $this->belongsTo(Status::class, 'Stau_UIN', 'Stau_UIN');
    // }
}