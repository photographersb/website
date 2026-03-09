<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\LearningCourse;
use App\Models\LearningEnrollment;
use App\Models\LearningLesson;
use App\Models\LearningLessonProgress;
use Illuminate\Support\Str;

class LearningService
{
    public static function enroll(int $userId, LearningCourse $course): LearningEnrollment
    {
        return LearningEnrollment::query()->firstOrCreate(
            [
                'user_id' => $userId,
                'course_id' => $course->id,
            ],
            [
                'status' => 'enrolled',
                'enrolled_at' => now(),
                'completion_percentage' => 0,
                'completed_lessons' => 0,
            ]
        );
    }

    public static function trackLessonProgress(LearningEnrollment $enrollment, LearningLesson $lesson, float $progress = 100): LearningLessonProgress
    {
        $progress = max(0, min(100, $progress));

        $lessonProgress = LearningLessonProgress::query()->updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'user_id' => $enrollment->user_id,
                'progress_percentage' => $progress,
                'last_viewed_at' => now(),
                'completed_at' => $progress >= 100 ? now() : null,
            ]
        );

        self::refreshEnrollmentProgress($enrollment);

        return $lessonProgress;
    }

    public static function refreshEnrollmentProgress(LearningEnrollment $enrollment): void
    {
        $totalLessons = LearningLesson::query()
            ->where('course_id', $enrollment->course_id)
            ->where('is_published', true)
            ->count();

        if ($totalLessons === 0) {
            $enrollment->update([
                'completed_lessons' => 0,
                'completion_percentage' => 0,
            ]);
            return;
        }

        $completedLessons = LearningLessonProgress::query()
            ->where('enrollment_id', $enrollment->id)
            ->whereNotNull('completed_at')
            ->count();

        $percentage = round(($completedLessons / $totalLessons) * 100, 2);
        $isCompleted = $percentage >= 100;

        $enrollment->update([
            'completed_lessons' => $completedLessons,
            'completion_percentage' => $percentage,
            'status' => $isCompleted ? 'completed' : 'enrolled',
            'completed_at' => $isCompleted ? ($enrollment->completed_at ?? now()) : null,
        ]);

        if ($isCompleted && !$enrollment->certificate_id) {
            $certificate = self::issueCourseCertificate($enrollment);
            if ($certificate) {
                $enrollment->update(['certificate_id' => $certificate->id]);
            }
        }
    }

    public static function issueCourseCertificate(LearningEnrollment $enrollment): ?Certificate
    {
        $course = $enrollment->course;
        $user = $enrollment->user;

        if (!$course || !$user) {
            return null;
        }

        $code = self::generateCertificateCode($course->id);

        return Certificate::query()->create([
            'certificate_code' => $code,
            'source_type' => 'learning_course',
            'source_id' => $course->id,
            'issued_to_user_id' => $user->id,
            'issued_to_name' => $user->name,
            'issued_to_email' => $user->email,
            'issue_date' => now()->toDateString(),
            'status' => 'issued',
            'created_by_user_id' => $course->instructor_user_id,
            'notes' => 'Learning course completion certificate: ' . $course->title,
        ]);
    }

    private static function generateCertificateCode(int $courseId): string
    {
        $year = now()->format('Y');
        $sequence = str_pad((string) (Certificate::query()->whereYear('created_at', now()->year)->count() + 1), 5, '0', STR_PAD_LEFT);

        return 'LRN-' . $year . '-' . str_pad((string) $courseId, 4, '0', STR_PAD_LEFT) . '-' . $sequence . '-' . Str::upper(Str::random(4));
    }
}
