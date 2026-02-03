<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewReply;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewReplyController extends Controller
{
    use ApiResponse;
    public function store(Request $request, $reviewId)
    {
        $review = Review::with('booking')->findOrFail($reviewId);

        // Check if authenticated user is the photographer for this review
        if ($request->user()->photographer->id !== $review->photographer_id) {
            return $this->unauthorized('Unauthorized to reply to this review');
        }

        // Check if reply already exists
        if ($review->reply) {
            return $this->error('You have already replied to this review', 400);
        }

        $validator = Validator::make($request->all(), [
            'reply' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), 'Validation failed');
        }

        $reviewReply = ReviewReply::create([
            'review_id' => $review->id,
            'reply' => $request->reply,
        ]);

        // Notify the client about the reply
        $review->booking->user->notify(new \App\Notifications\ReviewReplyReceived($reviewReply));

        return $this->created($reviewReply, 'Reply posted successfully');
    }

    public function update(Request $request, $id)
    {
        $reviewReply = ReviewReply::with('review')->findOrFail($id);

        // Check authorization
        if ($request->user()->photographer->id !== $reviewReply->review->photographer_id) {
            return $this->unauthorized('Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'reply' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), 'Validation failed');
        }

        $reviewReply->update([
            'reply' => $request->reply,
        ]);

        return $this->success($reviewReply, 'Reply updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $reviewReply = ReviewReply::with('review')->findOrFail($id);

        // Check authorization
        if ($request->user()->photographer->id !== $reviewReply->review->photographer_id) {
            return $this->unauthorized('Unauthorized');
        }

        $reviewReply->delete();

        return $this->success([], 'Reply deleted successfully');
    }
}
