<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\ContactMessage;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of contact messages
     */
    public function index(Request $request): JsonResponse
    {
        $query = ContactMessage::query();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Get total counts
        $total = ContactMessage::count();
        $pending = ContactMessage::where('status', 'pending')->count();
        $read = ContactMessage::where('status', 'read')->count();
        $resolved = ContactMessage::where('status', 'resolved')->count();
        $contact = ContactMessage::where('type', 'contact')->count();
        $sponsorship = ContactMessage::where('type', 'sponsorship')->count();

        $messages = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $messages,
            'stats' => [
                'total' => $total,
                'pending' => $pending,
                'read' => $read,
                'resolved' => $resolved,
                'contact' => $contact,
                'sponsorship' => $sponsorship,
            ]
        ]);
    }

    /**
     * Display a specific contact message
     */
    public function show($id): JsonResponse
    {
        $message = ContactMessage::findOrFail($id);

        if ($message->status === 'pending') {
            $message->markAsRead();
        }

        return response()->json($message);
    }

    /**
     * Store a new contact message
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:contact,sponsorship,general,support',
        ]);

        $validated['user_id'] = Auth::id();

        $message = ContactMessage::create($validated);

        return response()->json($message, 201);
    }

    /**
     * Update a contact message
     */
    public function update(Request $request, $id): JsonResponse
    {
        $message = ContactMessage::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
            'phone' => 'nullable|string|max:20',
            'subject' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'type' => 'sometimes|in:contact,sponsorship,general,support',
            'status' => 'sometimes|in:pending,read,resolved,archived',
        ]);

        $message->update($validated);

        return response()->json($message);
    }

    /**
     * Update status of a contact message
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $message = ContactMessage::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,read,resolved,archived',
        ]);

        $message->update($validated);

        return response()->json($message);
    }

    /**
     * Delete a contact message
     */
    public function destroy($id): JsonResponse
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return response()->json(null, 204);
    }

    /**
     * Get statistics for contact messages
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'total' => ContactMessage::count(),
            'pending' => ContactMessage::where('status', 'pending')->count(),
            'responded' => ContactMessage::whereIn('status', ['read', 'resolved'])->count(),
            'archived' => ContactMessage::where('status', 'archived')->count(),
            'read' => ContactMessage::where('status', 'read')->count(),
            'resolved' => ContactMessage::where('status', 'resolved')->count(),
            'contact' => ContactMessage::where('type', 'contact')->count(),
            'sponsorship' => ContactMessage::where('type', 'sponsorship')->count(),
            'today' => ContactMessage::where('created_at', '>=', now()->startOfDay())->count(),
            'this_week' => ContactMessage::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => ContactMessage::where('created_at', '>=', now()->startOfMonth())->count(),
        ]);
    }

    /**
     * Mark message as responded
     */
    public function markAsResponded($id): JsonResponse
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['status' => 'resolved']);
        
        return response()->json([
            'message' => 'Message marked as responded',
            'data' => $message
        ]);
    }

    /**
     * Archive a message
     */
    public function archive($id): JsonResponse
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['status' => 'archived']);
        
        return response()->json([
            'message' => 'Message archived successfully',
            'data' => $message
        ]);
    }
}
