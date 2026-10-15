<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOrgaRela extends Model
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'User_UIN', 'User_UIN');
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(OrganisationDetails::class, 'Orga_UIN', 'Orga_UIN');
    }
}