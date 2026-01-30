<?php

namespace App\Http\Controllers\Api;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
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
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Check if booking is completed
            if ($booking->status !== 'completed') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Can only review completed bookings',
                ], 422);
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

            Log::info('Review created successfully', [
                'review_id' => $review->id,
                'photographer_id' => $validated['photographer_id'],
                'reviewer_id' => auth()->id(),
                'is_verified' => $isVerifiedPurchase,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Review created successfully',
                'data' => $review,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create review', [
                'error' => $e->getMessage(),
                'photographer_id' => $validated['photographer_id'] ?? null,
                'reviewer_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit review. Please try again.',
            ], 500);
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

        return response()->json([
            'status' => 'success',
            'data' => $reviews->items(),
            'meta' => [
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
            ],
        ]);
    }

    /**
     * Get all reviews (Admin only)
     */
    public function index(Request $request)
    {
        // Check admin access
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
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

        return response()->json([
            'status' => 'success',
            'data' => $reviews,
        ]);
    }
}
