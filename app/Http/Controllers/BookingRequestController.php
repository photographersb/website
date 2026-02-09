<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingRequestController extends Controller
{
    /**
     * Show booking request form for a photographer.
     */
    public function create($photographerUsername)
    {
        $photographer = User::where('username', $photographerUsername)->firstOrFail();

        if (auth()->check() && auth()->id() === $photographer->id) {
            return redirect()
                ->route('home')
                ->with('error', 'You cannot create a booking request for your own profile.');
        }

        return inertia('Bookings/Create', [
            'photographer' => [
                'id' => $photographer->id,
                'name' => $photographer->full_name ?? $photographer->name,
                'username' => $photographer->username,
                'profile_image' => $photographer->profile_image_url,
            ],
            'categories' => Category::all(['id', 'name']),
            'locations' => Location::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
            'currentUserId' => auth()->id(),
        ]);
    }

    /**
     * Store new booking request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'photographer_user_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'city_id' => 'nullable|exists:locations,id',
            'venue_address' => 'nullable|string|max:500',
            'event_date' => 'required|date|after_or_equal:today',
            'event_time' => 'nullable|date_format:H:i',
            'duration_hours' => 'nullable|integer|min:1|max:24',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        if (auth()->check() && (int) $validated['photographer_user_id'] === (int) auth()->id()) {
            return back()->withErrors([
                'photographer_user_id' => 'You cannot create a booking request for your own profile.'
            ])->withInput();
        }

        $validated['client_user_id'] = auth()->id();
        $validated['booking_code'] = $this->generateBookingCode();
        $validated['status'] = 'pending';

        $booking = BookingRequest::create($validated);

        // Log status change
        $booking->statusLogs()->create([
            'old_status' => null,
            'new_status' => 'pending',
            'changed_by_user_id' => auth()->id(),
            'note' => 'Booking request created',
        ]);

        // Notify photographer
        $photographer = $booking->photographer;
        $photographer->notify(new \App\Notifications\BookingRequestCreated($booking));

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Booking request sent successfully!');
    }

    /**
     * Show booking details.
     */
    public function show(BookingRequest $booking)
    {
        $this->authorize('view', $booking);

        $booking->load([
            'client',
            'photographer',
            'messages.sender',
            'statusLogs.changedByUser',
        ]);

        // Mark messages as read for current user
        $booking->messages()
            ->where('sender_user_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return inertia('Bookings/Show', [
            'booking' => [
                'id' => $booking->id,
                'code' => $booking->booking_code,
                'status' => $booking->status,
                'status_badge' => $booking->getStatusBadgeClass(),
                'event_date' => $booking->event_date?->format('Y-m-d'),
                'event_time' => $booking->event_time?->format('H:i'),
                'duration_hours' => $booking->duration_hours,
                'venue_address' => $booking->venue_address,
                'budget_min' => $booking->budget_min,
                'budget_max' => $booking->budget_max,
                'notes' => $booking->notes,
                'client' => [
                    'id' => $booking->client->id,
                    'name' => $booking->client->full_name ?? $booking->client->name,
                    'profile_image' => $booking->client->profile_image_url,
                ],
                'photographer' => [
                    'id' => $booking->photographer->id,
                    'name' => $booking->photographer->full_name ?? $booking->photographer->name,
                    'profile_image' => $booking->photographer->profile_image_url,
                ],
                'messages' => $booking->messages->map(function ($msg) {
                    return [
                        'id' => $msg->id,
                        'sender' => [
                            'id' => $msg->sender->id,
                            'name' => $msg->sender->full_name ?? $msg->sender->name,
                        ],
                        'message' => $msg->message,
                        'attachment_path' => $msg->attachment_path,
                        'created_at' => $msg->created_at->format('Y-m-d H:i'),
                    ];
                }),
                'status_logs' => $booking->statusLogs->map(function ($log) {
                    return [
                        'old_status' => $log->old_status,
                        'new_status' => $log->new_status,
                        'changed_by' => $log->changedByUser->full_name ?? $log->changedByUser->name,
                        'note' => $log->note,
                        'created_at' => $log->created_at->format('Y-m-d H:i'),
                    ];
                }),
                'can_accept' => auth()->user()->can('accept', $booking),
                'can_decline' => auth()->user()->can('decline', $booking),
                'can_cancel' => auth()->user()->can('cancel', $booking),
                'can_complete' => auth()->user()->can('complete', $booking),
                'unread_count' => $booking->getUnreadMessageCount(),
            ],
        ]);
    }

    /**
     * List bookings for client.
     */
    public function clientBookings()
    {
        $bookings = BookingRequest::forClient(auth()->id())
            ->with('photographer')
            ->latest()
            ->paginate(10);

        return inertia('Bookings/ClientList', [
            'bookings' => $bookings->map(function ($b) {
                return [
                    'id' => $b->id,
                    'code' => $b->booking_code,
                    'status' => $b->status,
                    'photographer_name' => $b->photographer->full_name ?? $b->photographer->name,
                    'event_date' => $b->event_date?->format('d M Y'),
                    'budget' => $b->budget_min && $b->budget_max ? "৳{$b->budget_min} - ৳{$b->budget_max}" : 'Not specified',
                    'unread_count' => $b->getUnreadMessageCount(),
                ];
            }),
        ]);
    }

    /**
     * List bookings for photographer.
     */
    public function photographerBookings()
    {
        $bookings = BookingRequest::forPhotographer(auth()->id())
            ->with('client')
            ->latest()
            ->paginate(10);

        return inertia('Bookings/PhotographerList', [
            'bookings' => $bookings->map(function ($b) {
                return [
                    'id' => $b->id,
                    'code' => $b->booking_code,
                    'status' => $b->status,
                    'client_name' => $b->client->full_name ?? $b->client->name,
                    'event_date' => $b->event_date?->format('d M Y'),
                    'budget' => $b->budget_min && $b->budget_max ? "৳{$b->budget_min} - ৳{$b->budget_max}" : 'Not specified',
                    'unread_count' => $b->getUnreadMessageCount(),
                ];
            }),
        ]);
    }

    /**
     * Accept booking request.
     */
    public function accept(BookingRequest $booking)
    {
        $this->authorize('accept', $booking);

        $oldStatus = $booking->status;

        $booking->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Log status change
        $booking->statusLogs()->create([
            'old_status' => $oldStatus,
            'new_status' => 'accepted',
            'changed_by_user_id' => auth()->id(),
            'note' => 'Booking accepted by photographer',
        ]);

        // Notify client
        $booking->client->notify(new \App\Notifications\BookingAccepted($booking));

        return back()->with('success', 'Booking accepted!');
    }

    /**
     * Decline booking request.
     */
    public function decline(BookingRequest $booking, Request $request)
    {
        $this->authorize('decline', $booking);

        $request->validate(['reason' => 'nullable|string|max:500']);

        $oldStatus = $booking->status;

        $booking->update([
            'status' => 'declined',
            'declined_at' => now(),
        ]);

        // Log status change
        $booking->statusLogs()->create([
            'old_status' => $oldStatus,
            'new_status' => 'declined',
            'changed_by_user_id' => auth()->id(),
            'note' => $request->input('reason') ?? 'Booking declined by photographer',
        ]);

        // Notify client
        $booking->client->notify(new \App\Notifications\BookingDeclined($booking));

        return back()->with('success', 'Booking declined.');
    }

    /**
     * Cancel booking.
     */
    public function cancel(BookingRequest $booking, Request $request)
    {
        $this->authorize('cancel', $booking);

        $request->validate(['reason' => 'nullable|string|max:500']);

        $oldStatus = $booking->status;

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Log status change
        $booking->statusLogs()->create([
            'old_status' => $oldStatus,
            'new_status' => 'cancelled',
            'changed_by_user_id' => auth()->id(),
            'note' => $request->input('reason') ?? 'Booking cancelled',
        ]);

        // Notify other party
        $otherUser = $booking->client_user_id === auth()->id() 
            ? $booking->photographer 
            : $booking->client;
        $otherUser->notify(new \App\Notifications\BookingCancelled($booking));

        return back()->with('success', 'Booking cancelled.');
    }

    /**
     * Complete booking.
     */
    public function complete(BookingRequest $booking)
    {
        $this->authorize('complete', $booking);

        $oldStatus = $booking->status;

        $booking->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Log status change
        $booking->statusLogs()->create([
            'old_status' => $oldStatus,
            'new_status' => 'completed',
            'changed_by_user_id' => auth()->id(),
            'note' => 'Booking marked as completed',
        ]);

        // Notify client
        $booking->client->notify(new \App\Notifications\BookingCompleted($booking));

        return back()->with('success', 'Booking completed!');
    }

    /**
     * Generate unique booking code.
     */
    protected function generateBookingCode(): string
    {
        $year = now()->year;
        $sequence = BookingRequest::whereYear('created_at', $year)->count() + 1;

        return sprintf('SB-BK-%d-%04d', $year, $sequence);
    }
}
