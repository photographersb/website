<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Models\EventAttendanceLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventAttendanceController extends Controller
{
    /**
     * Display event attendance panel (QR scanner)
     */
    public function index(Event $event)
    {
        $this->authorize('update', $event);

        $registrations = $event->registrations()
            ->with(['user'])
            ->orderBy('registered_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_registered' => $event->registrations()->count(),
            'attended_count' => EventAttendanceLog::where('event_id', $event->id)->count(),
            'attendance_rate' => 0,
        ];

        if ($stats['total_registered'] > 0) {
            $stats['attendance_rate'] = round(($stats['attended_count'] / $stats['total_registered']) * 100, 1);
        }

        return view('admin.events.attendance.index', [
            'event' => $event,
            'registrations' => $registrations,
            'stats' => $stats,
        ]);
    }

    /**
     * Process QR code scan
     */
    public function scan(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        // Find registration by QR code (registration_code)
        $registration = $event->registrations()
            ->where('registration_code', $validated['qr_code'])
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration code',
            ], 404);
        }

        // Check if already marked as attended
        $existing = EventAttendanceLog::where('event_id', $event->id)
            ->where('event_registration_id', $registration->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in',
                'attendance' => $existing,
            ]);
        }

        // Create attendance log
        $attendance = EventAttendanceLog::create([
            'event_id' => $event->id,
            'event_registration_id' => $registration->id,
            'user_id' => $registration->user_id,
            'scanned_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful',
            'attendance' => $attendance,
            'user' => $registration->user->name,
        ]);
    }

    /**
     * Generate attendance report
     */
    public function report(Request $request, Event $event)
    {
        $this->authorize('view', $event);

        $attendances = EventAttendanceLog::where('event_id', $event->id)
            ->with(['user', 'registration'])
            ->orderBy('scanned_at', 'desc')
            ->paginate(50);

        return view('admin.events.attendance.report', [
            'event' => $event,
            'attendances' => $attendances,
        ]);
    }

    /**
     * Export attendance data
     */
    public function export(Request $request, Event $event)
    {
        $this->authorize('view', $event);

        $format = $request->input('format', 'csv'); // csv or excel

        $attendances = EventAttendanceLog::where('event_id', $event->id)
            ->with(['user', 'registration'])
            ->orderBy('scanned_at', 'desc')
            ->get();

        if ($format === 'csv') {
            return $this->exportAsCSV($event, $attendances);
        }

        return response()->json(['error' => 'Format not supported'], 400);
    }

    /**
     * Export as CSV
     */
    private function exportAsCSV(Event $event, $attendances)
    {
        $filename = $event->slug . '-attendance-' . date('Y-m-d-His') . '.csv';

        $callback = function () use ($event, $attendances) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Registration Code', 'Name', 'Email', 'City', 'Scanned At', 'Registered At']);

            // Data
            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->registration->registration_code,
                    $attendance->user->name,
                    $attendance->user->email,
                    $event->city?->name,
                    $attendance->scanned_at->format('Y-m-d H:i:s'),
                    $attendance->registration->registered_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
