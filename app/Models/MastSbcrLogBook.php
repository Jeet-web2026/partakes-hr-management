<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MastSbcrLogBook extends Model
{
    protected $table = 'tbl_mast_sbcr_log_book';
    protected $primaryKey = 'Log_book_uin';
    public $incrementing = false;
    protected $keyType = 'integer';
    public $timestamps = false;

    protected $fillable = [
        'Log_book_uin',
        'Sbcr_Rvnu_Catg_Name_UIN',
        'Indv_Orga',
        'Indv_Orga_Uin',
        'Cont',
        'UOM',
        'Size',
        'Vert_Uin',
        'Desc',
        'User_UIN',
        'CrOn',
    ];

    protected $casts = [
        'Size' => 'decimal:2',
        'CrOn' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_UIN', 'User_UIN');
    }
}
