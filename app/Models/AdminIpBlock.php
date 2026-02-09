<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminIpBlock extends Model
{
    protected $table = 'admin_ip_blocks';

    protected $fillable = [
        'ip',
        'reason',
        'blocked_by_user_id',
        'expires_at',
        'geo_country',
        'geo_region',
        'geo_city',
        'geo_lat',
        'geo_lng',
        'geo_timezone',
        'geo_isp',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by_user_id');
    }
}
