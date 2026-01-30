<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotographerFavorite extends Model
{
    protected $fillable = [
        'user_id',
        'photographer_id',
    ];

    /**
     * Get the user who favorited the photographer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the photographer that was favorited.
     */
    public function photographer(): BelongsTo
    {
        return $this->belongsTo(Photographer::class);
    }
}
