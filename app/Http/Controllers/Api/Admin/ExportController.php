<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use App\Models\Photographer;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Competition;
use App\Models\Review;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    /**
     * Export users to CSV
     */
    public function exportUsers(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $users = $query->get();

        $csv = $this->generateCSV([
            'ID', 'Name', 'Email', 'Role', 'Email Verified', 'Created At'
        ], $users->map(function($user) {
            return [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->email_verified_at ? 'Yes' : 'No',
                $user->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray());

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export photographers to CSV
     */
    public function exportPhotographers(Request $request)
    {
        $query = Photographer::with('user');

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        $photographers = $query->get();

        $csv = $this->generateCSV([
            'ID', 'Name', 'Email', 'Slug', 'Experience', 'Rating', 'Bookings', 'Verified', 'Featured'
        ], $photographers->map(function($p) {
            return [
                $p->id,
                $p->user->name,
                $p->user->email,
                $p->slug,
                $p->experience_years ?? 0,
                $p->average_rating ?? 0,
                $p->total_bookings ?? 0,
                $p->is_verified ? 'Yes' : 'No',
                $p->is_featured ? 'Yes' : 'No',
            ];
        })->toArray());

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="photographers_' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export bookings to CSV
     */
    public function exportBookings(Request $request)
    {
        $query = Booking::with(['client', 'photographer.user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->get();

        $csv = $this->generateCSV([
            'ID', 'Client', 'Photographer', 'Event Date', 'Location', 'Status'
        ], $bookings->map(function($b) {
            return [
                $b->id,
                $b->client->name,
                $b->photographer->user->name,
                $b->event_date->format('Y-m-d'),
                $b->location,
                $b->status,
            ];
        })->toArray());

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings_' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export activity logs
     */
    public function exportActivityLogs(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        $logs = $query->orderBy('created_at', 'desc')->limit(10000)->get();

        $csv = $this->generateCSV([
            'ID', 'User', 'Action', 'Description', 'IP', 'Created At'
        ], $logs->map(function($log) {
            return [
                $log->id,
                $log->user->name ?? 'System',
                $log->action,
                $log->description,
                $log->ip_address ?? 'N/A',
                $log->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray());

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity_logs_' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Generate CSV from headers and data
     */
    private function generateCSV(array $headers, array $data): string
    {
        $output = fopen('php://temp', 'r+');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
        fputcsv($output, $headers);
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        return $csv;
    }
}
