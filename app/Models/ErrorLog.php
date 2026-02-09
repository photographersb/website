<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ErrorLog extends Model
{
    use HasFactory;
    
    protected $table = 'admin_error_logs';
    
    protected $fillable = [
        'severity',
        'environment',
        'url',
        'route_name',
        'method',
        'status_code',
        'user_id',
        'ip',
        'user_agent',
        'message',
        'exception_class',
        'file',
        'line',
        'trace',
        'is_resolved',
        'resolved_by_user_id',
        'resolved_at',
        'is_muted',
        'error_signature',
        'occurrence_count',
        'last_occurrence_at',
        'notes',
        'geo_country',
        'geo_region',
        'geo_city',
        'geo_lat',
        'geo_lng',
        'geo_timezone',
        'geo_isp',
    ];
    
    protected $casts = [
        'last_occurrence_at' => 'datetime',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
