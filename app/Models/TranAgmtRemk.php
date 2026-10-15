<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranAgmtRemk extends Model
{
    protected $table = 'tran_agmt_remk';

    protected $primaryKey = 'agmt_remk_uin';

    public $timestamps = false;

    protected $fillable = [
        'agmt_uin',
        'agmt_remk_date',
        'agmt_remk',
        'stau_uin',
        'crby',
        'moby',
    ];

    protected $casts = [
        'agmt_remk_date' => 'date',
    ];
}
