<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingMessage;
use App\Models\Booking;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class BookingMessageController extends Controller
{
    use ApiResponse;

    /**
     * Get all messages for a booking
     */
    public function index(Booking $booking)
    {
        $this->authorize('viewMessages', $booking);

        $messages = $booking->messages()
            ->with(['sender' => function ($query) {
                $query->select('id', 'name', 'email', 'avatar');
            }])
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return $this->success($messages, 'Messages retrieved successfully');
    }

    /**
     * Store a new message
     */
    public function store(Request $request, Booking $booking)
    {
        $this->authorize('sendMessage', $booking);

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240'
        ]);

        try {
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('booking-messages', 'public');
                    $attachments[] = [
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ];
                }
            }

            $message = $booking->messages()->create([
                'sender_id' => auth()->id(),
                'sender_type' => auth()->user()->isPhotographer() ? 'photographer' : 'client',
                'message' => $validated['message'],
                'attachments' => $attachments ?: null
            ]);

            return $this->success($message->load('sender'), 'Message sent successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to send message: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Show a specific message
     */
    public function show(Booking $booking, BookingMessage $message)
    {
        $this->authorize('viewMessages', $booking);

        if ($message->booking_id !== $booking->id) {
            return $this->error('Message not found in this booking', 404);
        }

        return $this->success($message->load('sender'), 'Message retrieved successfully');
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Booking $booking, BookingMessage $message)
    {
        $this->authorize('viewMessages', $booking);

        if ($message->booking_id !== $booking->id) {
            return $this->error('Message not found in this booking', 404);
        }

        if ($message->sender_id !== auth()->id()) {
            $message->markAsRead();
        }

        return $this->success($message, 'Message marked as read');
    }

    /**
     * Mark all messages as read for a booking
     */
    public function markAllAsRead(Request $request, Booking $booking)
    {
        $this->authorize('viewMessages', $booking);

        $booking->messages()
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return $this->success(null, 'All messages marked as read');
    }

    /**
     * Delete a message
     */
    public function destroy(Booking $booking, BookingMessage $message)
    {
        $this->authorize('deleteMessage', [$booking, $message]);

        if ($message->booking_id !== $booking->id) {
            return $this->error('Message not found in this booking', 404);
        }

        if ($message->sender_id !== auth()->id()) {
            return $this->error('You can only delete your own messages', 403);
        }

        if ($message->is_system_message) {
            return $this->error('System messages cannot be deleted', 403);
        }

        $message->delete();

        return $this->success(null, 'Message deleted successfully');
    }
}
