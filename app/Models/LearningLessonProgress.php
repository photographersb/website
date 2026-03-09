<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningLessonProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'lesson_id',
        'user_id',
        'progress_percentage',
        'last_viewed_at',
        'completed_at',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'last_viewed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(LearningEnrollment::class, 'enrollment_id');
    }

    public function lesson()
    {
        return $this->belongsTo(LearningLesson::class, 'lesson_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
