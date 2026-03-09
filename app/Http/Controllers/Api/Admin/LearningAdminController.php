<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\LearningCourse;
use App\Models\LearningCourseReview;
use App\Models\LearningEnrollment;
use App\Models\LearningInstructorProfile;
use Illuminate\Http\Request;

class LearningAdminController extends Controller
{
    use ApiResponse;

    private function ensureAdmin(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return $this->unauthorized('Admin access required');
        }

        return null;
    }

    public function overview(Request $request)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $topCourses = LearningCourse::query()
            ->with('instructor:id,name,username')
            ->withCount('enrollments')
            ->withAvg([
                'reviews as average_rating' => function ($query) {
                    $query->where('status', 'published');
                },
            ], 'rating')
            ->orderByDesc('enrollments_count')
            ->limit(10)
            ->get(['id', 'title', 'slug', 'status', 'is_featured', 'instructor_user_id']);

        return $this->success([
            'metrics' => [
                'total_courses' => LearningCourse::query()->count(),
                'published_courses' => LearningCourse::query()->where('status', 'published')->count(),
                'total_enrollments' => LearningEnrollment::query()->count(),
                'completed_enrollments' => LearningEnrollment::query()->where('status', 'completed')->count(),
                'approved_instructors' => LearningInstructorProfile::query()->where('is_approved', true)->count(),
                'pending_instructors' => LearningInstructorProfile::query()->where('is_approved', false)->count(),
                'reviews_count' => LearningCourseReview::query()->count(),
                'average_rating' => round((float) LearningCourseReview::query()->where('status', 'published')->avg('rating'), 2),
            ],
            'top_courses' => $topCourses,
        ], 'Learning admin overview loaded successfully');
    }

    public function courses(Request $request)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $perPage = min((int) $request->input('per_page', 30), 100);

        $items = LearningCourse::query()
            ->with('instructor:id,name,username')
            ->withCount('enrollments')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $this->paginated($items, 'Learning courses loaded successfully');
    }

    public function updateCourseStatus(Request $request, LearningCourse $course)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
        ]);

        $course->update([
            'status' => $validated['status'],
            'is_featured' => $validated['is_featured'] ?? $course->is_featured,
            'published_at' => $validated['status'] === 'published'
                ? ($course->published_at ?? now())
                : null,
        ]);

        return $this->success($course->fresh(), 'Course status updated successfully');
    }

    public function instructors(Request $request)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $perPage = min((int) $request->input('per_page', 30), 100);

        $items = LearningInstructorProfile::query()
            ->with('user:id,name,username,email')
            ->latest()
            ->paginate($perPage);

        return $this->paginated($items, 'Instructor profiles loaded successfully');
    }

    public function approveInstructor(Request $request, LearningInstructorProfile $profile)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $validated = $request->validate([
            'is_approved' => 'required|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $profile->update([
            'is_approved' => $validated['is_approved'],
            'is_active' => $validated['is_active'] ?? $profile->is_active,
        ]);

        return $this->success($profile->fresh(), 'Instructor profile moderation updated successfully');
    }

    public function reviews(Request $request)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $perPage = min((int) $request->input('per_page', 40), 100);

        $items = LearningCourseReview::query()
            ->with(['user:id,name,username', 'course:id,title,slug'])
            ->latest('reviewed_at')
            ->paginate($perPage);

        return $this->paginated($items, 'Course reviews loaded successfully');
    }

    public function updateReviewStatus(Request $request, LearningCourseReview $review)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $validated = $request->validate([
            'status' => 'required|in:published,hidden',
        ]);

        $review->update([
            'status' => $validated['status'],
            'reviewed_at' => now(),
        ]);

        return $this->success($review->fresh(), 'Review status updated successfully');
    }
}
