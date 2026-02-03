<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotographerAward extends Model
{
    use HasFactory;

    protected $fillable = [
        'photographer_id',
        'title',
        'organization',
        'year',
        'description',
        'certificate_url',
        'type',
        'display_order',
    ];

    protected $casts = [
        'year' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Get the photographer that owns the award
     */
    public function photographer()
    {
        return $this->belongsTo(Photographer::class);
    }
}
