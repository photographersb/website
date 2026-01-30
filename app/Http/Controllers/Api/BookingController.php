<?php

namespace App\Http\Controllers\Api;

use App\Models\Inquiry;
use App\Models\Quote;
use App\Models\Booking;
use App\Notifications\BookingCreated;
use App\Notifications\BookingStatusUpdated;
use App\Notifications\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Create inquiry
     */
    public function createInquiry(Request $request)
    {
        $validated = $request->validate([
            'photographer_id' => 'required|exists:photographers,id',
            'package_id' => 'nullable|exists:packages,id',
            'event_date' => 'required|date',
            'event_location' => 'required|string',
            'guest_count' => 'required|integer|min:1',
            'budget_min' => 'nullable|numeric',
            'budget_max' => 'nullable|numeric',
            'requirements' => 'nullable|string',
        ]);

        try {
            $result = DB::transaction(function () use ($validated) {
                $inquiry = Inquiry::create([
                    ...$validated,
                    'client_id' => auth()->id(),
                    'status' => 'new',
                    'expires_at' => now()->addDays(7),
                ]);

                // Create booking record
                $booking = Booking::create([
                    'inquiry_id' => $inquiry->id,
                    'client_id' => auth()->id(),
                    'photographer_id' => $validated['photographer_id'],
                    'package_id' => $validated['package_id'] ?? null,
                    'event_date' => $validated['event_date'],
                    'total_amount' => 0, // Will be updated when quote is accepted
                    'status' => 'pending_payment',
                ]);

                return ['inquiry' => $inquiry, 'booking' => $booking];
            });

            $inquiry = $result['inquiry'];
            $booking = $result['booking'];

            // Send notifications (outside transaction)
            $booking->load(['client', 'photographer.user']);
            
            try {
                // Check if notification classes exist before sending
                if (class_exists(\App\Notifications\BookingCreated::class)) {
                    $booking->client->notify(new \App\Notifications\BookingCreated($booking, 'client'));
                    $booking->photographer->user->notify(new \App\Notifications\BookingCreated($booking, 'photographer'));
                }
            } catch (\Exception $notificationError) {
                // Log notification errors but don't fail the request
                Log::warning('Failed to send booking notifications', [
                    'booking_id' => $booking->id,
                    'error' => $notificationError->getMessage(),
                ]);
            }

            Log::info('Booking inquiry created successfully', [
                'inquiry_id' => $inquiry->id,
                'booking_id' => $booking->id,
                'client_id' => auth()->id(),
                'photographer_id' => $validated['photographer_id'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Inquiry created successfully',
                'data' => [
                    'inquiry' => $inquiry,
                    'booking' => $booking,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create booking inquiry', [
                'error' => $e->getMessage(),
                'client_id' => auth()->id(),
                'photographer_id' => $validated['photographer_id'] ?? null,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create inquiry. Please try again.',
            ], 500);
        }
    }

    /**
     * Get client bookings
     */
    public function myBookings(Request $request)
    {
        $bookings = Booking::where('client_id', auth()->id())
            ->with(['photographer', 'package', 'reviews'])
            ->orderBy('event_date', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $bookings->items(),
            'meta' => [
                'total' => $bookings->total(),
                'per_page' => $bookings->perPage(),
                'current_page' => $bookings->currentPage(),
            ],
        ]);
    }

    /**
     * Get booking details
     */
    public function getBooking(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load([
            'photographer',
            'package',
            'inquiry',
            'quote',
            'reviews',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $booking,
        ]);
    }

    /**
     * Cancel booking
     */
    public function cancelBooking(Booking $booking, Request $request)
    {
        $this->authorize('update', $booking);

        if (!in_array($booking->status, ['pending_payment', 'confirmed'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking cannot be cancelled in current status',
            ], 422);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->get('reason'),
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking cancelled successfully',
            'data' => $booking,
        ]);
    }

    /**
     * Update booking status (for photographers)
     */
    public function updateStatus(Booking $booking, Request $request)
    {
        // Check if user is the photographer for this booking
        $photographer = auth()->user()->photographer;
        if (!$photographer || $booking->photographer_id !== $photographer->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:confirmed,rejected,completed,cancelled',
        ]);

        $oldStatus = $booking->status;

        $booking->update([
            'status' => $validated['status'],
        ]);

        // Send notification to client about status change
        $booking->load(['client', 'photographer.user', 'package']);
        $booking->client->notify(new BookingStatusUpdated($booking, $oldStatus, $validated['status']));

        // If completed, send review request after 1 day
        if ($validated['status'] === 'completed') {
            $booking->client->notify((new ReviewRequest($booking))->delay(now()->addDay()));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Booking status updated successfully',
            'data' => $booking->fresh(['client', 'photographer', 'package']),
        ]);
    }
}
