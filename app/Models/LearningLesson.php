<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'lesson_type',
        'content',
        'video_url',
        'attachments',
        'duration_minutes',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_published' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(LearningCourse::class, 'course_id');
    }
}
