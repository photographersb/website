<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminErrorLogNote extends Model
{
    protected $fillable = [
        'error_log_id',
        'note',
        'added_by_user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function errorLog(): BelongsTo
    {
        return $this->belongsTo(AdminErrorLog::class, 'error_log_id');
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }
}
