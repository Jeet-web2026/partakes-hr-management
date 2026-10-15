<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ShareLink extends Model
{
    protected $table = 'tbl_tran_share_links';

    protected $primaryKey = 'Link_UIN';

    public $incrementing = false;

    protected $keyType = 'int';

    const CREATED_AT = 'CrOn';
    const UPDATED_AT = 'MoOn';

    protected $fillable = [
        'Link_UIN',
        'Link_Foreign_Id_UIN',
        'Link_Token',
        'Link_Type',
        'Link_Expy_On',
        'CrBy',
        'MoBy',
    ];

    protected $casts = [
        'CrOn'         => 'datetime',
        'MoOn'         => 'datetime',
        'Link_Expy_On' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($link) {
            if (empty($link->Link_UIN)) {
                do {
                    $candidate = now()->timestamp;
                } while (self::where('Link_UIN', $candidate)->exists());

                $link->Link_UIN = $candidate;
            }
        });
    }

    public function foreign(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'Link_Type', 'Link_Foreign_Id_UIN');
    }

    public function isExpired(): bool
    {
        return $this->Link_Expy_On !== null && $this->Link_Expy_On->isPast();
    }
}
