<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewReplyController extends Controller
{
    public function store(Request $request, $reviewId)
    {
        $review = Review::with('booking')->findOrFail($reviewId);

        // Check if authenticated user is the photographer for this review
        if ($request->user()->photographer->id !== $review->photographer_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to reply to this review'
            ], 403);
        }

        // Check if reply already exists
        if ($review->reply) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already replied to this review'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'reply' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $reviewReply = ReviewReply::create([
            'review_id' => $review->id,
            'reply' => $request->reply,
        ]);

        // Notify the client about the reply
        $review->booking->user->notify(new \App\Notifications\ReviewReplyReceived($reviewReply));

        return response()->json([
            'status' => 'success',
            'message' => 'Reply posted successfully',
            'data' => $reviewReply
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $reviewReply = ReviewReply::with('review')->findOrFail($id);

        // Check authorization
        if ($request->user()->photographer->id !== $reviewReply->review->photographer_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'reply' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $reviewReply->update([
            'reply' => $request->reply,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Reply updated successfully',
            'data' => $reviewReply
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $reviewReply = ReviewReply::with('review')->findOrFail($id);

        // Check authorization
        if ($request->user()->photographer->id !== $reviewReply->review->photographer_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $reviewReply->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Reply deleted successfully'
        ]);
    }
}
