<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'category',
        'difficulty_level',
        'cover_image_url',
        'instructor_user_id',
        'price',
        'duration_minutes',
        'lessons_count',
        'is_featured',
        'status',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_user_id');
    }

    public function lessons()
    {
        return $this->hasMany(LearningLesson::class, 'course_id')->orderBy('sort_order');
    }

    public function enrollments()
    {
        return $this->hasMany(LearningEnrollment::class, 'course_id');
    }

    public function reviews()
    {
        return $this->hasMany(LearningCourseReview::class, 'course_id');
    }
}
