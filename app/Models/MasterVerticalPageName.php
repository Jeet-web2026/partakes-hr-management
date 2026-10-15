<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterVerticalPageName extends Model
{
    protected $table = 'tbl_mast_page';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'Page_UIN';

    /**
     * Indicates if the model should be timestamped.
     * The table has no created_at / updated_at columns.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Page_name',
        'Page_Desp',
        'Dvlp_name',
        'Data_Hit',
        'Walt_Chag_Pont',
        'Rate_Pont',
        'Data_Add',
        'Data_Updt',
        'Data_Del',
        'Data_VfBy',
        'Data_Adit',
        'Data_Prnt',
        'Data_Expt',
        'Data_Prnt_Scrn',
        'Data_Sele_Copy',
        'Data_Pste',
        'Data_Link',
        'CrBy',
        'CrOn',
        'MoBy',
        'MoOn',
        'VfBy',
        'VfOn',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'Page_UIN' => 'integer',
        'Dvlp_name' => 'integer',
        'Data_Hit' => 'integer',
        'Walt_Chag_Pont' => 'integer',
        'Rate_Pont' => 'integer',
        'Data_Add' => 'integer',
        'Data_Updt' => 'integer',
        'Data_Del' => 'integer',
        'Data_VfBy' => 'integer',
        'Data_Adit' => 'integer',
        'Data_Prnt' => 'integer',
        'Data_Expt' => 'integer',
        'Data_Prnt_Scrn' => 'integer',
        'Data_Sele_Copy' => 'integer',
        'Data_Pste' => 'integer',
        'Data_Link' => 'integer',
        'CrBy' => 'integer',
        'CrOn' => 'integer',
        'MoBy' => 'integer',
        'MoOn' => 'integer',
        'VfBy' => 'integer',
        'VfOn' => 'integer',
    ];
}
