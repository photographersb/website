<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningInstructorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'expertise',
        'is_approved',
        'is_active',
        'student_rating',
        'courses_created',
        'students_count',
    ];

    protected $casts = [
        'expertise' => 'array',
        'is_approved' => 'boolean',
        'is_active' => 'boolean',
        'student_rating' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
