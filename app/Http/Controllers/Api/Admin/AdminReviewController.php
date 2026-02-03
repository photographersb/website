<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminReviewController extends Controller
{
    use ApiResponse;
    /**
     * Get all reviews with filters and pagination
     */
    public function index(Request $request)
    {
        try {
            // Calculate stats before applying pagination
            $statsQuery = Review::query();
            
            // Apply search filter to stats if present
            if ($request->has('search')) {
                $statsQuery->where(function($q) use ($request) {
                    $q->where('comment', 'LIKE', "%{$request->search}%")
                      ->orWhereHas('reviewer', function($userQ) use ($request) {
                          $userQ->where('name', 'LIKE', "%{$request->search}%");
                      });
                });
            }
            
            $stats = [
                'total' => $statsQuery->count(),
                'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
                'published' => (clone $statsQuery)->where('status', 'published')->count(),
                'rejected' => (clone $statsQuery)->where('status', 'rejected')->count(),
                'reported' => (clone $statsQuery)->whereNotNull('flag_reason')->count(),
                'avg_rating' => round((clone $statsQuery)->where('status', 'published')->avg('rating') ?: 0, 2),
            ];

            $query = Review::with(['reviewer', 'photographer.user']);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('rating')) {
                $query->where('rating', '>=', $request->rating);
            }

            if ($request->has('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('comment', 'LIKE', "%{$request->search}%")
                      ->orWhereHas('reviewer', function($userQ) use ($request) {
                          $userQ->where('name', 'LIKE', "%{$request->search}%");
                      });
                });
            }

            if ($request->has('photographer_id')) {
                $query->where('photographer_id', $request->photographer_id);
            }

            // Sort by newest first
            $query->orderBy('created_at', 'desc');

            $reviews = $query->paginate($request->per_page ?? 15);

            return $this->success([
                'reviews' => $reviews->items(),
                'stats' => $stats,
            ], 'Reviews retrieved successfully', 200, [
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch reviews: ' . $e->getMessage());
            return $this->error('Failed to fetch reviews', 500);
        }
    }

    /**
     * Get review statistics
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => Review::count(),
                'pending' => Review::where('status', 'pending')->count(),
                'published' => Review::where('status', 'published')->count(),
                'rejected' => Review::where('status', 'rejected')->count(),
                'reported' => Review::where('is_reported', true)->count(),
                'avg_rating' => round(Review::where('status', 'published')->avg('rating'), 2),
            ];

            return $this->success($stats, 'Review statistics retrieved');
        } catch (\Exception $e) {
            Log::error('Failed to fetch review stats: ' . $e->getMessage());
            return $this->error('Failed to fetch stats', 500);
        }
    }

    /**
     * Update review status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,published,rejected'
        ]);

        try {
            $review = Review::findOrFail($id);
            $review->update(['status' => $validated['status']]);

            Log::info("Review #{$id} status updated to {$validated['status']} by admin " . auth()->user()->id);

            return $this->success($review->load(['user', 'photographer.user']), 'Review status updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update review status: ' . $e->getMessage());
            return $this->error('Failed to update review', 500);
        }
    }

    /**
     * Delete review
     */
    public function destroy($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete();

            Log::info("Review #{$id} deleted by admin " . auth()->user()->id);

            return $this->success([], 'Review deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete review: ' . $e->getMessage());
            return $this->error('Failed to delete review', 500);
        }
    }

    /**
     * Mark review as reported/spam
     */
    public function markAsReported(Request $request, $id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->update(['is_reported' => true]);

            Log::info("Review #{$id} marked as reported by admin " . auth()->user()->id);

            return $this->success($review, 'Review marked as reported');
        } catch (\Exception $e) {
            Log::error('Failed to mark review as reported: ' . $e->getMessage());
            return $this->error('Failed to mark review', 500);
        }
    }

    /**
     * Bulk update review statuses
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'required|exists:reviews,id',
            'status' => 'required|in:pending,published,rejected'
        ]);

        try {
            Review::whereIn('id', $validated['review_ids'])
                ->update(['status' => $validated['status']]);

            Log::info("Bulk updated " . count($validated['review_ids']) . " reviews to {$validated['status']} by admin " . auth()->user()->id);

            return $this->success([], 'Reviews updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to bulk update reviews: ' . $e->getMessage());
            return $this->error('Failed to update reviews', 500);
        }
    }
}
