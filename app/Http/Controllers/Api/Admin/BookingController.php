<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Get all bookings with pagination and filters
     */
    public function index(Request $request)
    {
        $query = Booking::with(['client', 'photographer']);
        
        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('photographer', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        $bookings = $query->paginate($request->input('per_page', 10));
        
        return response()->json([
            'data' => $bookings->items(),
            'pagination' => [
                'total' => $bookings->total(),
                'per_page' => $bookings->perPage(),
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
            ]
        ]);
    }
    
    /**
     * Get a single booking
     */
    public function show(Booking $booking)
    {
        return response()->json(['data' => $booking->load(['client', 'photographer'])]);
    }
    
    /**
     * Update booking
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
            'booking_date' => 'nullable|date',
        ]);
        
        $booking->update($validated);
        
        return response()->json(['data' => $booking]);
    }
    
    /**
     * Cancel booking
     */
    public function cancel(Booking $booking)
    {
        $booking->update(['status' => 'cancelled']);
        
        return response()->json(['data' => $booking]);
    }
    
    /**
     * Delete booking
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        
        return response()->json(null, 204);
    }
}
