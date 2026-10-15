<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranFdbk extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'tbl_tran_fdbk';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'Fdbk_UIN';

    /**
     * This table only has a CrOn column (no MoOn), so Laravel's
     * default created_at/updated_at handling is disabled.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'Orga_UIN',
        'Page_UIN',
        'Page_load_speed',
        'Page_Dsin',
        'Page_Ease_Of_Use',
        'Page_Oall',
        'Page_InfoClarity',
        'Fdbk_Type',
        'Lokn_for',
        'Rcmd_Page',
        'Cnta_Us',
        'Mesg_Fdbk',
        'PinCod',
        'CrBy',
        'CrOn',
        'Fdbk_Attachment',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'Orga_UIN' => 'integer',
        'Page_UIN' => 'integer',
        'PinCod'   => 'integer',
        'CrBy'     => 'integer',
        // CrOn is stored as a Unix timestamp (int), cast to Carbon automatically.
        'CrOn'     => 'timestamp',
    ];

    /**
     * Relationship: the organisation this feedback belongs to.
     * Adjust the related model/FK to match your Organisation model.
     */
    public function organisation()
    {
        return $this->belongsTo(OrganisationDetails::class, 'Orga_UIN', 'Orga_UIN');
    }

    /**
     * Relationship: the user who created this feedback entry.
     * Adjust the related model/FK to match your User model.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'CrBy', 'Usr_UIN');
    }
}
