<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionSubmissionFile extends Model
{
    protected $fillable = [
        'submission_id',
        'image_path',
        'sort_order',
        'exif_json',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'exif_json' => 'array',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(CompetitionSubmission::class, 'submission_id');
    }
}
