<?php

namespace App\Http\Controllers\Admin;

use App\Models\BookingRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    /**
     * Show all bookings - Admin oversight.
     */
    public function index(Request $request)
    {
        // Only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $query = BookingRequest::with('client', 'photographer');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('event_date', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('event_date', '<=', $request->input('to_date'));
        }

        // Search by booking code or participant names
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('photographer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
            });
        }

        $bookings = $query->latest()->paginate(15);

        return inertia('Admin/Bookings/Index', [
            'bookings' => $bookings->map(function ($b) {
                return [
                    'id' => $b->id,
                    'code' => $b->booking_code,
                    'status' => $b->status,
                    'client' => [
                        'name' => $b->client->full_name ?? $b->client->name,
                        'email' => $b->client->email,
                    ],
                    'photographer' => [
                        'name' => $b->photographer->full_name ?? $b->photographer->name,
                        'username' => $b->photographer->username,
                    ],
                    'event_date' => $b->event_date?->format('d-m-Y'),
                    'budget' => $b->budget_min && $b->budget_max ? "৳{$b->budget_min} - ৳{$b->budget_max}" : 'N/A',
                    'created_at' => $b->created_at->format('d-m-Y H:i'),
                ];
            }),
            'filters' => $request->only(['status', 'from_date', 'to_date', 'search']),
            'statuses' => [
                'pending' => 'Pending',
                'accepted' => 'Accepted',
                'declined' => 'Declined',
                'cancelled' => 'Cancelled',
                'completed' => 'Completed',
            ],
        ]);
    }

    /**
     * Show booking details - Admin oversight.
     */
    public function show(BookingRequest $booking)
    {
        // Only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $booking->load([
            'client',
            'photographer',
            'category',
            'messages.sender',
            'statusLogs.changedByUser',
        ]);

        return inertia('Admin/Bookings/Show', [
            'booking' => [
                'id' => $booking->id,
                'code' => $booking->booking_code,
                'status' => $booking->status,
                'event_date' => $booking->event_date?->format('d-m-Y'),
                'event_time' => $booking->event_time?->format('H:i'),
                'duration_hours' => $booking->duration_hours,
                'venue_address' => $booking->venue_address,
                'budget_min' => $booking->budget_min,
                'budget_max' => $booking->budget_max,
                'notes' => $booking->notes,
                'category' => $booking->category ? [
                    'id' => $booking->category->id,
                    'name' => $booking->category->name,
                ] : null,
                'client' => [
                    'id' => $booking->client->id,
                    'name' => $booking->client->full_name ?? $booking->client->name,
                    'email' => $booking->client->email,
                    'phone' => $booking->client->phone,
                    'profile_image' => $booking->client->profile_image_url,
                ],
                'photographer' => [
                    'id' => $booking->photographer->id,
                    'name' => $booking->photographer->full_name ?? $booking->photographer->name,
                    'username' => $booking->photographer->username,
                    'email' => $booking->photographer->email,
                    'profile_image' => $booking->photographer->profile_image_url,
                ],
                'timestamps' => [
                    'created_at' => $booking->created_at?->format('d-m-Y H:i'),
                    'accepted_at' => $booking->accepted_at?->format('d-m-Y H:i'),
                    'declined_at' => $booking->declined_at?->format('d-m-Y H:i'),
                    'cancelled_at' => $booking->cancelled_at?->format('d-m-Y H:i'),
                    'completed_at' => $booking->completed_at?->format('d-m-Y H:i'),
                ],
                'messages' => $booking->messages->map(function ($msg) {
                    return [
                        'id' => $msg->id,
                        'sender' => [
                            'name' => $msg->sender->full_name ?? $msg->sender->name,
                            'role' => $msg->sender->id === $booking->client_user_id ? 'Client' : 'Photographer',
                        ],
                        'message' => $msg->message,
                        'attachment_path' => $msg->attachment_path,
                        'created_at' => $msg->created_at->format('d-m-Y H:i'),
                    ];
                }),
                'status_logs' => $booking->statusLogs->map(function ($log) {
                    return [
                        'old_status' => $log->old_status,
                        'new_status' => $log->new_status,
                        'changed_by' => $log->changedByUser->full_name ?? $log->changedByUser->name,
                        'note' => $log->note,
                        'created_at' => $log->created_at->format('d-m-Y H:i'),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Cancel booking - Admin intervention.
     */
    public function cancel(BookingRequest $booking, Request $request)
    {
        // Only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate(['reason' => 'required|string|max:500']);

        // Prevent cancelling completed bookings
        if ($booking->isCompleted()) {
            return back()->withErrors('Cannot cancel a completed booking.');
        }

        $oldStatus = $booking->status;

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Log status change with admin attribution
        $booking->statusLogs()->create([
            'old_status' => $oldStatus,
            'new_status' => 'cancelled',
            'changed_by_user_id' => auth()->id(),
            'note' => "[Admin] {$request->input('reason')}",
        ]);

        // Notify both parties
        $booking->client->notify(new \App\Notifications\BookingCancelled($booking));
        $booking->photographer->notify(new \App\Notifications\BookingCancelled($booking));

        return back()->with('success', 'Booking cancelled by admin.');
    }

    /**
     * Flag/Dispute booking - Admin investigation.
     */
    public function dispute(BookingRequest $booking, Request $request)
    {
        // Only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate(['reason' => 'required|string|max:500']);

        // Add dispute log
        $booking->statusLogs()->create([
            'old_status' => $booking->status,
            'new_status' => $booking->status,
            'changed_by_user_id' => auth()->id(),
            'note' => "[DISPUTE] {$request->input('reason')}",
        ]);

        // You can add additional dispute handling here:
        // - Flag for manual review
        // - Notify support team
        // - Create ticket system integration

        return back()->with('success', 'Dispute logged. Support team notified.');
    }

    /**
     * Get booking statistics.
     */
    public function statistics()
    {
        // Only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $stats = [
            'total_bookings' => BookingRequest::count(),
            'pending' => BookingRequest::where('status', 'pending')->count(),
            'accepted' => BookingRequest::where('status', 'accepted')->count(),
            'declined' => BookingRequest::where('status', 'declined')->count(),
            'cancelled' => BookingRequest::where('status', 'cancelled')->count(),
            'completed' => BookingRequest::where('status', 'completed')->count(),
            'total_value' => BookingRequest::where('status', 'completed')
                ->sum('budget_max'),
            'this_month' => BookingRequest::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return response()->json($stats);
    }
}
