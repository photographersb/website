<?php

namespace App\Http\Controllers\Api;

use App\Models\Review;
use App\Http\Traits\ApiResponse;
use App\Services\NotificationService;
use App\Services\PhotographerNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    use ApiResponse;

    /**
     * Get featured testimonials (public endpoint)
     */
    public function featured(Request $request)
    {
        $reviews = Review::where('status', 'published')
            ->where('rating', '>=', 4)
            ->with(['reviewer:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return $this->success($reviews, 'Featured testimonials retrieved successfully');
    }

    /**
     * Create review for booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'photographer_id' => 'required|exists:photographers,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'professionalism_score' => 'nullable|numeric|min:1|max:10',
            'quality_score' => 'nullable|numeric|min:1|max:10',
            'communication_score' => 'nullable|numeric|min:1|max:10',
            'value_score' => 'nullable|numeric|min:1|max:10',
            'delivery_score' => 'nullable|numeric|min:1|max:10',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10',
            'is_anonymous' => 'boolean',
            'photo_urls' => 'nullable|array|max:5',
        ]);

        $isVerifiedPurchase = false;

        // If booking_id is provided, verify it
        if (!empty($validated['booking_id'])) {
            $booking = \App\Models\Booking::find($validated['booking_id']);

            // Verify user is the reviewer
            if ($booking->client_id !== auth()->id()) {
                return $this->unauthorized('Unauthorized');
            }

            // Check if booking is completed
            if ($booking->status !== 'completed') {
                return $this->validationError(['booking_id' => ['Can only review completed bookings']], 'Can only review completed bookings');
            }

            $isVerifiedPurchase = true;
        }

        try {
            $review = Review::create([
                ...$validated,
                'reviewer_id' => auth()->id(),
                'photographer_id' => $validated['photographer_id'],
                'is_verified_purchase' => $isVerifiedPurchase,
                'status' => 'pending',
            ]);

            // Send new review notification
            $review->load(['photographer', 'reviewer']);
            
            // Notify photographer
            try {
                PhotographerNotificationService::notifyNewReview($review);
            } catch (\Exception $notificationError) {
                Log::warning('Failed to send photographer notification', [
                    'review_id' => $review->id,
                    'error' => $notificationError->getMessage(),
                ]);
            }

            // Track review achievement
            \App\Services\AchievementService::trackReviewReceived(
                $validated['photographer_id'],
                $validated['rating']
            );

            // Send system notification
            try {
                NotificationService::newReview($review);
            } catch (\Exception $notificationError) {
                Log::warning('Failed to send new review notification', [
                    'review_id' => $review->id,
                    'error' => $notificationError->getMessage(),
                ]);
            }

            Log::info('Review created successfully', [
                'review_id' => $review->id,
                'photographer_id' => $validated['photographer_id'],
                'reviewer_id' => auth()->id(),
                'is_verified' => $isVerifiedPurchase,
            ]);

            return $this->created($review, 'Review created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create review', [
                'error' => $e->getMessage(),
                'photographer_id' => $validated['photographer_id'] ?? null,
                'reviewer_id' => auth()->id(),
            ]);

            return $this->error('Failed to submit review. Please try again.', 500);
        }
    }

    /**
     * Get reviews for photographer
     */
    public function getPhotographerReviews(Request $request)
    {
        $photographer_id = $request->get('photographer_id');

        $reviews = Review::where('photographer_id', $photographer_id)
            ->where('status', 'published')
            ->with(['reviewer', 'replies'])
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        return $this->paginated($reviews, 'Reviews retrieved successfully');
    }

    /**
     * Get all reviews (Admin only)
     */
    public function index(Request $request)
    {
        // Check admin access
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return $this->unauthorized('Unauthorized. Admin access required.');
        }

        $query = Review::with(['reviewer', 'photographer.user', 'booking']);

        // Apply filters
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('comment', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('rating') && $request->rating !== 'all') {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->orderBy('created_at', 'desc')->get();

        return $this->success($reviews, 'Reviews retrieved successfully');
    }
}
