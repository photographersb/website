<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminBookingController extends Controller
{
    use ApiResponse;
    /**
     * Get all bookings with filters and pagination
     */
    public function index(Request $request)
    {
        try {
            $query = Booking::with(['client', 'photographer.user']);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('search')) {
                $query->where(function($q) use ($request) {
                    $q->whereHas('client', function($clientQ) use ($request) {
                        $clientQ->where('name', 'LIKE', "%{$request->search}%")
                                ->orWhere('email', 'LIKE', "%{$request->search}%");
                    })
                    ->orWhereHas('photographer.user', function($photoQ) use ($request) {
                        $photoQ->where('name', 'LIKE', "%{$request->search}%");
                    });
                });
            }

            if ($request->has('event_date')) {
                $query->whereDate('event_date', $request->event_date);
            }

            if ($request->has('date_from')) {
                $query->where('event_date', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('event_date', '<=', $request->date_to);
            }

            // Calculate stats before pagination (on full dataset)
            $stats = [
                'total' => Booking::count(),
                'pending' => Booking::where('status', 'pending')->count(),
                'confirmed' => Booking::where('status', 'confirmed')->count(),
                'in_progress' => Booking::where('status', 'in_progress')->count(),
                'completed' => Booking::where('status', 'completed')->count(),
                'cancelled' => Booking::where('status', 'cancelled')->count(),
                'revenue' => Booking::whereIn('status', ['completed', 'in_progress'])->sum('total_amount'),
                'monthly_revenue' => Booking::whereIn('status', ['completed', 'in_progress'])
                    ->whereMonth('created_at', now()->month)
                    ->sum('total_amount'),
            ];

            // Sort by newest first
            $query->orderBy('created_at', 'desc');

            $bookings = $query->paginate($request->per_page ?? 15);

            return $this->success([
                'data' => $bookings->items(),
                'stats' => $stats,
            ], 'Bookings retrieved successfully', 200, [
                'total' => $bookings->total(),
                'per_page' => $bookings->perPage(),
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch bookings: ' . $e->getMessage());
            return $this->error('Failed to fetch bookings', 500);
        }
    }

    /**
     * Get booking statistics
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => Booking::count(),
                'pending' => Booking::where('status', 'pending')->count(),
                'confirmed' => Booking::where('status', 'confirmed')->count(),
                'in_progress' => Booking::where('status', 'in_progress')->count(),
                'completed' => Booking::where('status', 'completed')->count(),
                'cancelled' => Booking::where('status', 'cancelled')->count(),
                'revenue' => Booking::whereIn('status', ['completed', 'in_progress'])->sum('total_amount'),
                'monthly_revenue' => Booking::whereIn('status', ['completed', 'in_progress'])
                    ->whereMonth('created_at', now()->month)
                    ->sum('total_amount'),
            ];

            return $this->success($stats, 'Booking statistics retrieved');
        } catch (\Exception $e) {
            Log::error('Failed to fetch booking stats: ' . $e->getMessage());
            return $this->error('Failed to fetch stats', 500);
        }
    }

    /**
     * Get single booking details
     */
    public function show($id)
    {
        try {
            $booking = Booking::with(['client', 'photographer.user', 'photographer.city'])
                ->findOrFail($id);

            return $this->success($booking, 'Booking retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to fetch booking: ' . $e->getMessage());
            return $this->notFound('Booking not found');
        }
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled'
        ]);

        try {
            $booking = Booking::findOrFail($id);
            $booking->update(['status' => $validated['status']]);

            Log::info("Booking #{$id} status updated to {$validated['status']} by admin " . \Illuminate\Support\Facades\Auth::id());

            return $this->success($booking->load(['client', 'photographer.user']), 'Booking status updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update booking status: ' . $e->getMessage());
            return $this->error('Failed to update booking', 500);
        }
    }

    /**
     * Delete booking
     */
    public function destroy($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $booking->delete();

            Log::info("Booking #{$id} deleted by admin " . \Illuminate\Support\Facades\Auth::id());

            return $this->success([], 'Booking deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete booking: ' . $e->getMessage());
            return $this->error('Failed to delete booking', 500);
        }
    }
}
