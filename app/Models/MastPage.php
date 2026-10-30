<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MastPage extends Model
{
    protected $table = 'tbl_mast_page';
    protected $primaryKey = 'Page_UIN';
    public $timestamps = false;

    protected $guarded = [];
}
