<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    protected $fillable = [
        'photographer_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
        'status',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'time',
        'end_time' => 'time',
        'is_available' => 'boolean',
    ];

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(Photographer::class);
    }
}
