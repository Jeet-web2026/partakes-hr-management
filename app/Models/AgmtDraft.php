<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgmtDraft extends Model
{
    use HasFactory;

    protected $table = 'tbl_tran_agmt_draft';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'Orga_UIN',
        'User_UIN',

        'Agmt_Catg_UIN',
        'Agmt_Type_UIN',
        'Agmt_Name',

        'Agmt_Vson',

        'Agmt_Aprv_By',
        'Agmt_Aprv_Date',

        'Agmt_Drft_File_Name',
        'Agmt_Chklst_File_Name',
        'CrOn'
    ];

    protected $casts = [
        'Agmt_Aprv_Date' => 'date',
    ];

    /**
     * The organisation this draft belongs to.
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(OrganisationDetails::class, 'Orga_UIN', 'Orga_UIN');
    }

    /**
     * The agreement category (child-level, e.g. "Banking < Finance Agreements").
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AgreementCategories::class, 'Agmt_Catg_UIN', 'Agmt_Catg_UIN');
    }

    /**
     * The agreement type.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(AgreementType::class, 'Agmt_Type_UIN', 'Agmt_Type_UIN');
    }
}
