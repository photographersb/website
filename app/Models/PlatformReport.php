<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'metrics',
        'alerts',
        'security_summary',
    ];

    protected $casts = [
        'report_date' => 'date',
        'metrics' => 'array',
        'alerts' => 'array',
        'security_summary' => 'array',
    ];
}
