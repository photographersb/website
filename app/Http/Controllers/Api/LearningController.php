<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Event;
use App\Models\LearningCourse;
use App\Models\LearningCourseReview;
use App\Models\LearningEnrollment;
use App\Models\LearningInstructorProfile;
use App\Models\LearningLesson;
use App\Services\LearningService;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    use ApiResponse;

    public function hub(Request $request)
    {
        $featuredCourses = LearningCourse::query()
            ->with(['instructor:id,name,username', 'reviews:id,course_id,rating,status'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->latest('published_at')
            ->limit(8)
            ->get();

        $courseCategories = LearningCourse::query()
            ->selectRaw('category, COUNT(*) as total')
            ->where('status', 'published')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(12)
            ->get();

        $instructors = LearningInstructorProfile::query()
            ->with('user:id,name,username')
            ->where('is_approved', true)
            ->where('is_active', true)
            ->orderByDesc('student_rating')
            ->orderByDesc('students_count')
            ->limit(8)
            ->get();

        $workshops = $this->workshopsQuery()->limit(8)->get();

        $user = $request->user();
        $myLearning = null;

        if ($user) {
            $myLearning = [
                'active_courses' => LearningEnrollment::query()
                    ->where('user_id', $user->id)
                    ->where('status', 'enrolled')
                    ->count(),
                'completed_courses' => LearningEnrollment::query()
                    ->where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
                'certificates_earned' => LearningEnrollment::query()
                    ->where('user_id', $user->id)
                    ->whereNotNull('certificate_id')
                    ->count(),
            ];
        }

        return $this->success([
            'featured_courses' => $featuredCourses,
            'course_categories' => $courseCategories,
            'recommended_workshops' => $workshops,
            'instructors_spotlight' => $instructors,
            'my_learning' => $myLearning,
        ], 'Learning hub loaded successfully');
    }

    public function courses(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 18), 50);

        $query = LearningCourse::query()
            ->with(['instructor:id,name,username'])
            ->withCount('enrollments')
            ->withAvg([
                'reviews as average_rating' => function ($inner) {
                    $inner->where('status', 'published');
                },
            ], 'rating')
            ->where('status', 'published')
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at');

        if ($request->filled('category')) {
            $query->where('category', (string) $request->input('category'));
        }

        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', (string) $request->input('difficulty_level'));
        }

        if ($request->filled('price_type')) {
            $priceType = (string) $request->input('price_type');
            if ($priceType === 'free') {
                $query->where('price', '<=', 0);
            }
            if ($priceType === 'paid') {
                $query->where('price', '>', 0);
            }
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($inner) use ($q) {
                $inner->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%");
            });
        }

        $items = $query->paginate($perPage);

        return $this->paginated($items, 'Learning courses retrieved successfully');
    }

    public function showCourse(Request $request, LearningCourse $course)
    {
        if ($course->status !== 'published') {
            return $this->notFound('Course not found');
        }

        $course->load([
            'instructor:id,name,username',
            'lessons' => function ($query) {
                $query->where('is_published', true)->orderBy('sort_order');
            },
            'reviews' => function ($query) {
                $query->where('status', 'published')
                    ->with('user:id,name,username')
                    ->latest('reviewed_at')
                    ->limit(20);
            },
        ]);

        $summary = [
            'average_rating' => (float) LearningCourseReview::query()
                ->where('course_id', $course->id)
                ->where('status', 'published')
                ->avg('rating'),
            'reviews_count' => LearningCourseReview::query()
                ->where('course_id', $course->id)
                ->where('status', 'published')
                ->count(),
            'enrollments_count' => LearningEnrollment::query()
                ->where('course_id', $course->id)
                ->count(),
        ];

        $user = $request->user();
        $enrollment = null;

        if ($user) {
            $enrollment = LearningEnrollment::query()
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();
        }

        return $this->success([
            'course' => $course,
            'summary' => $summary,
            'my_enrollment' => $enrollment,
        ], 'Course details retrieved successfully');
    }

    public function workshops(Request $request)
    {
        $limit = min((int) $request->input('limit', 20), 50);
        $items = $this->workshopsQuery()->limit($limit)->get();

        return $this->success($items, 'Workshops retrieved successfully');
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        if (mb_strlen($q) < 2) {
            return $this->error('Search query must be at least 2 characters', 422);
        }

        $courses = LearningCourse::query()
            ->where('status', 'published')
            ->where(function ($inner) use ($q) {
                $inner->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'slug', 'title', 'category', 'difficulty_level', 'price', 'is_featured']);

        $instructors = LearningInstructorProfile::query()
            ->with('user:id,name,username')
            ->where('is_approved', true)
            ->where('is_active', true)
            ->where(function ($inner) use ($q) {
                $inner->where('bio', 'like', "%{$q}%")
                    ->orWhereJsonContains('expertise', $q);
            })
            ->limit(8)
            ->get();

        $workshops = $this->workshopsQuery($q)->limit(8)->get();

        return $this->success([
            'courses' => $courses,
            'instructors' => $instructors,
            'workshops' => $workshops,
        ], 'Learning search results loaded successfully');
    }

    public function enroll(Request $request, LearningCourse $course)
    {
        if ($course->status !== 'published') {
            return $this->error('Only published courses can be enrolled', 422);
        }

        $enrollment = LearningService::enroll($request->user()->id, $course);

        return $this->success($enrollment->fresh(), 'Course enrollment successful');
    }

    public function myDashboard(Request $request)
    {
        $user = $request->user();

        $enrollments = LearningEnrollment::query()
            ->with(['course:id,slug,title,cover_image_url,difficulty_level,price,status'])
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->get();

        $summary = [
            'active_courses' => $enrollments->where('status', 'enrolled')->count(),
            'completed_courses' => $enrollments->where('status', 'completed')->count(),
            'certificates_earned' => $enrollments->whereNotNull('certificate_id')->count(),
            'avg_completion' => round((float) $enrollments->avg('completion_percentage'), 2),
        ];

        return $this->success([
            'summary' => $summary,
            'enrollments' => $enrollments,
        ], 'Learning dashboard retrieved successfully');
    }

    public function trackLessonProgress(Request $request, LearningCourse $course, LearningLesson $lesson)
    {
        if ((int) $lesson->course_id !== (int) $course->id) {
            return $this->error('Lesson does not belong to this course', 422);
        }

        $validated = $request->validate([
            'progress_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $user = $request->user();
        $enrollment = LearningEnrollment::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            [
                'status' => 'enrolled',
                'enrolled_at' => now(),
                'completion_percentage' => 0,
                'completed_lessons' => 0,
            ]
        );

        $progress = (float) ($validated['progress_percentage'] ?? 100);
        $lessonProgress = LearningService::trackLessonProgress($enrollment, $lesson, $progress);

        return $this->success([
            'lesson_progress' => $lessonProgress,
            'enrollment' => $enrollment->fresh(),
        ], 'Lesson progress tracked successfully');
    }

    public function submitReview(Request $request, LearningCourse $course)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $enrollment = LearningEnrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment || (float) $enrollment->completion_percentage < 100) {
            return $this->error('Complete this course before submitting a review', 422);
        }

        $review = LearningCourseReview::query()->updateOrCreate(
            [
                'course_id' => $course->id,
                'user_id' => $user->id,
            ],
            [
                'rating' => $validated['rating'],
                'feedback' => $validated['feedback'] ?? null,
                'status' => 'published',
                'reviewed_at' => now(),
            ]
        );

        return $this->success($review->fresh(), 'Course review submitted successfully');
    }

    public function upsertInstructorProfile(Request $request)
    {
        $validated = $request->validate([
            'bio' => 'nullable|string|max:2500',
            'expertise' => 'nullable|array|max:20',
            'expertise.*' => 'nullable|string|max:80',
            'is_active' => 'nullable|boolean',
        ]);

        $profile = LearningInstructorProfile::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'bio' => $validated['bio'] ?? null,
                'expertise' => $validated['expertise'] ?? [],
                'is_active' => $validated['is_active'] ?? true,
            ]
        );

        return $this->success($profile->fresh(), 'Instructor profile updated successfully');
    }

    public function myInstructorProfile(Request $request)
    {
        $profile = LearningInstructorProfile::query()
            ->where('user_id', $request->user()->id)
            ->first();

        return $this->success($profile, 'Instructor profile retrieved successfully');
    }

    public function instructors(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 20), 50);

        $items = LearningInstructorProfile::query()
            ->with('user:id,name,username')
            ->where('is_approved', true)
            ->where('is_active', true)
            ->orderByDesc('student_rating')
            ->orderByDesc('students_count')
            ->paginate($perPage);

        return $this->paginated($items, 'Instructors retrieved successfully');
    }

    private function workshopsQuery(?string $query = null)
    {
        return Event::query()
            ->where('status', 'published')
            ->where(function ($inner) {
                $inner->where('type', 'workshop')
                    ->orWhere('title', 'like', '%workshop%')
                    ->orWhere('description', 'like', '%workshop%');
            })
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('venue_name', 'like', "%{$query}%");
                });
            })
            ->orderByDesc('event_date')
            ->getQuery()
            ->select(['id', 'title', 'slug', 'description', 'event_date', 'venue_name', 'event_mode', 'ticket_price', 'is_featured']);
    }
}
