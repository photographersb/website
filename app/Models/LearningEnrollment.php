<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'completed_lessons',
        'completion_percentage',
        'enrolled_at',
        'completed_at',
        'certificate_id',
    ];

    protected $casts = [
        'completion_percentage' => 'decimal:2',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(LearningCourse::class, 'course_id');
    }

    public function certificate()
    {
        return $this->belongsTo(Certificate::class, 'certificate_id');
    }

    public function lessonProgress()
    {
        return $this->hasMany(LearningLessonProgress::class, 'enrollment_id');
    }
}
