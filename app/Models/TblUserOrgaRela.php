<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblUserOrgaRela extends Model
{
    protected $table = 'tbl_user_orga_rela';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'Orga_User_Assc_UIN',
        'Orga_UIN',
        'Vert_UIN',
        'User_UIN',
        'Expy',
        'Stau_UIN',
        'CrOn',
        'CrBy',
        'MoOn',
        'MoBy',
    ];

    protected $casts = [
        'Expy' => 'datetime',
        'CrOn' => 'datetime',
        'MoOn' => 'datetime',
    ];
}
