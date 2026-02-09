<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Get all reviews with pagination and filters
     */
    public function index(Request $request)
    {
        $query = Review::with(['reviewer', 'reviewed']);
        
        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('reviewer', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by rating
        if ($request->has('rating')) {
            $rating = (int) $request->input('rating');
            $query->where('rating', '>=', $rating);
        }
        
        // Filter by status (approved/pending/rejected)
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'pending') {
                $query->whereNull('approved_at')->whereNull('rejected_at');
            } elseif ($status === 'approved') {
                $query->whereNotNull('approved_at');
            } elseif ($status === 'rejected') {
                $query->whereNotNull('rejected_at');
            }
        }
        
        $reviews = $query->paginate($request->input('per_page', 10));
        
        return response()->json([
            'data' => $reviews->items(),
            'pagination' => [
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
            ]
        ]);
    }
    
    /**
     * Get a single review
     */
    public function show(Review $review)
    {
        return response()->json(['data' => $review->load(['reviewer', 'reviewed'])]);
    }
    
    /**
     * Approve a review
     */
    public function approve(Review $review)
    {
        $review->update([
            'approved_at' => now(),
            'rejected_at' => null,
        ]);
        
        return response()->json(['data' => $review]);
    }
    
    /**
     * Bulk approve reviews
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'review_ids' => 'required|array|min:1',
            'review_ids.*' => 'exists:reviews,id',
        ]);
        
        Review::whereIn('id', $validated['review_ids'])->update([
            'approved_at' => now(),
            'rejected_at' => null,
        ]);
        
        return response()->json(['message' => 'Reviews approved']);
    }
    
    /**
     * Delete/reject a review
     */
    public function destroy(Review $review)
    {
        $review->update(['rejected_at' => now()]);
        $review->delete();
        
        return response()->json(null, 204);
    }
}
