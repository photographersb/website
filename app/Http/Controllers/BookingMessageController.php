<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\BookingMessage;
use Illuminate\Http\Request;

class BookingMessageController extends Controller
{
    /**
     * Store new message in booking thread.
     */
    public function store(BookingRequest $booking, Request $request)
    {
        $this->authorize('view', $booking);

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $attachmentPath = null;

        // Handle file attachment if provided
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store(
                "booking-attachments/{$booking->id}",
                'public'
            );
        }

        $message = $booking->messages()->create([
            'sender_user_id' => auth()->id(),
            'message' => $validated['message'],
            'attachment_path' => $attachmentPath,
            'is_read' => false,
        ]);

        // Notify other party about new message
        $otherUser = $booking->client_user_id === auth()->id()
            ? $booking->photographer
            : $booking->client;

        // You can add notification here if needed
        // $otherUser->notify(new BookingMessageReceived($booking, $message));

        return back()->with('success', 'Message sent!');
    }

    /**
     * Delete message.
     */
    public function delete(BookingMessage $message)
    {
        $booking = $message->bookingRequest;

        $this->authorize('view', $booking);

        // Only allow deletion by sender within 1 hour
        if ($message->sender_user_id !== auth()->id()) {
            abort(403, 'You cannot delete this message.');
        }

        if ($message->created_at->diffInHours(now()) > 1) {
            abort(403, 'Messages can only be deleted within 1 hour of posting.');
        }

        // Delete attachment if exists
        if ($message->attachment_path) {
            \Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete();

        return back()->with('success', 'Message deleted.');
    }

    /**
     * Mark message(s) as read.
     */
    public function markAsRead(BookingRequest $booking)
    {
        $this->authorize('view', $booking);

        $booking->messages()
            ->where('sender_user_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back();
    }
}
