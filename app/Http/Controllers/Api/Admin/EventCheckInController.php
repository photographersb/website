<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class EventCheckInController extends Controller
{
    /**
     * Get check-in page for event
     */
    public function index(Event $event): JsonResponse
    {
        $stats = [
            'total_confirmed' => $event->registrations()->where('status', 'confirmed')->count(),
            'total_attended' => $event->registrations()->where('status', 'attended')->count(),
            'pending_checkin' => $event->registrations()
                ->where('status', 'confirmed')
                ->count(),
        ];

        return response()->json([
            'event' => $event->load(['city', 'organizer']),
            'stats' => $stats,
        ]);
    }

    /**
     * Scan QR code and check in
     */
    public function scan(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'qr_token' => 'required|string',
        ]);

        try {
            return DB::transaction(function () use ($event, $validated) {
                $registration = EventRegistration::where('event_id', $event->id)
                    ->where('qr_token', $validated['qr_token'])
                    ->first();

                if (!$registration) {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR code not found or invalid',
                    ], 404);
                }

                // Check if already attended
                if ($registration->isAttended()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This ticket has already been checked in at ' . $registration->attended_at->format('H:i:s'),
                    ], 400);
                }

                // Check if confirmed
                if (!$registration->canCheckIn()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Registration status: ' . $registration->status,
                    ], 400);
                }

                // Mark as attended
                $registration->markAsAttended(Auth::id());

                return response()->json([
                    'success' => true,
                    'message' => 'Check-in successful',
                    'registration' => $registration->load('user', 'event', 'ticket'),
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing check-in: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of registrations for check-in
     */
    public function getRegistrations(Event $event, Request $request): JsonResponse
    {
        $query = $event->registrations()
            ->where('status', 'confirmed')
            ->with('user', 'ticket')
            ->orderBy('created_at', 'asc');

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $registrations = $query->paginate(50);

        return response()->json($registrations);
    }

    /**
     * Get registration by QR token
     */
    public function getByQrToken(Event $event, $qrToken): JsonResponse
    {
        $registration = $event->registrations()
            ->where('qr_token', $qrToken)
            ->with('user', 'ticket')
            ->first();

        if (!$registration) {
            return response()->json(['error' => 'Registration not found'], 404);
        }

        return response()->json($registration);
    }

    /**
     * Mark manual check-in (without QR)
     */
    public function manualCheckIn(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'registration_id' => 'required|exists:event_registrations,id',
        ]);

        try {
            return DB::transaction(function () use ($event, $validated) {
                $registration = EventRegistration::findOrFail($validated['registration_id']);

                if ($registration->event_id !== $event->id) {
                    return response()->json(['error' => 'Registration not found for this event'], 400);
                }

                if ($registration->isAttended()) {
                    return response()->json([
                        'error' => 'Already checked in at ' . $registration->attended_at->format('H:i:s'),
                    ], 400);
                }

                $registration->markAsAttended(Auth::id());

                return response()->json([
                    'message' => 'Check-in successful',
                    'registration' => $registration->load('user', 'ticket'),
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Undo check-in
     */
    public function undoCheckIn(EventRegistration $registration): JsonResponse
    {
        if (!$registration->isAttended()) {
            return response()->json(['error' => 'Registration not checked in'], 400);
        }

        $registration->update([
            'status' => 'confirmed',
            'attended_at' => null,
            'checked_in_by' => null,
        ]);

        return response()->json([
            'message' => 'Check-in undone',
            'registration' => $registration,
        ]);
    }

    /**
     * Export check-in report
     */
    public function exportCheckInReport(Event $event)
    {
        $registrations = $event->registrations()
            ->where('status', 'attended')
            ->with('user', 'checkedInBy', 'ticket')
            ->orderBy('attended_at', 'desc')
            ->get();

        $csv = "Name,Email,Ticket,Check-in Time,Checked In By\n";

        foreach ($registrations as $reg) {
            $csv .= implode(',', [
                $reg->user->name,
                $reg->user->email,
                $reg->ticket?->title ?? 'N/A',
                $reg->attended_at->format('Y-m-d H:i:s'),
                $reg->checkedInBy?->name ?? 'Unknown',
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$event->slug}-checkin-report.csv");
    }
}
