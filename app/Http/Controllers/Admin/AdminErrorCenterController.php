<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminErrorLog;
use App\Models\AdminErrorLogNote;
use App\Services\ErrorLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminErrorCenterController extends Controller
{
    protected ErrorLogService $errorLogService;

    public function __construct(ErrorLogService $errorLogService)
    {
        $this->errorLogService = $errorLogService;
        $this->middleware('auth');
    }

    /**
     * Display error logs dashboard
     */
    public function index(Request $request)
    {
        // Authorization
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized');
        }

        $query = AdminErrorLog::query();

        // Apply filters
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'open' => $query->where('is_resolved', false)->where('is_muted', false),
                'resolved' => $query->where('is_resolved', true),
                'muted' => $query->where('is_muted', true),
            };
        }

        if ($request->filled('environment')) {
            $query->where('environment', $request->environment);
        }

        if ($request->filled('status_code')) {
            $query->where('status_code', $request->status_code);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        }

        // Search
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('message', 'like', $term)
                  ->orWhere('url', 'like', $term)
                  ->orWhere('route_name', 'like', $term)
                  ->orWhere('exception_class', 'like', $term)
                  ->orWhere('file', 'like', $term);
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'last_seen_at');
        $sortOrder = $request->get('sort_order', 'desc');
        if (in_array($sortBy, ['last_seen_at', 'created_at', 'severity', 'occurrences'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('last_seen_at', 'desc');
        }

        // Paginate
        $errors = $query->paginate(25);

        // Get statistics
        $stats = $this->errorLogService->getStats();

        // Get filter options
        $severities = ['P0', 'P1', 'P2'];
        $statuses = ['open', 'resolved', 'muted'];
        $environments = ['local', 'staging', 'production'];
        $statusCodes = AdminErrorLog::distinct('status_code')
            ->whereNotNull('status_code')
            ->pluck('status_code')
            ->sort()
            ->values();

        return view('admin.error-center.index', [
            'errors' => $errors,
            'stats' => $stats,
            'severities' => $severities,
            'statuses' => $statuses,
            'environments' => $environments,
            'statusCodes' => $statusCodes,
            'filters' => $request->only(['severity', 'status', 'environment', 'status_code', 'date_from', 'date_to', 'search']),
        ]);
    }

    /**
     * Display a single error with details
     */
    public function show(AdminErrorLog $errorLog)
    {
        // Authorization
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized');
        }

        // Only super_admin can see trace
        if (!Auth::user()->hasRole('super_admin') && !empty($errorLog->trace)) {
            $errorLog->trace = '***REDACTED***';
        }

        // Get related errors (same signature)
        $relatedErrors = AdminErrorLog::where('signature_hash', $errorLog->signature_hash)
            ->where('id', '!=', $errorLog->id)
            ->latest('last_seen_at')
            ->limit(10)
            ->get();

        // Get notes
        $notes = $errorLog->notes()
            ->with('addedBy')
            ->latest()
            ->get();

        // Get similar errors
        $similarErrors = AdminErrorLog::where('status_code', $errorLog->status_code)
            ->where('id', '!=', $errorLog->id)
            ->where('is_resolved', false)
            ->latest('last_seen_at')
            ->limit(5)
            ->get();

        return view('admin.error-center.show', [
            'error' => $errorLog,
            'relatedErrors' => $relatedErrors,
            'notes' => $notes,
            'similarErrors' => $similarErrors,
        ]);
    }

    /**
     * Resolve an error (AJAX)
     */
    public function resolve(Request $request, AdminErrorLog $errorLog)
    {
        // Authorization
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized');
        }

        $this->errorLogService->resolveError($errorLog, Auth::user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Error resolved successfully',
                'error' => $errorLog->fresh(),
            ]);
        }

        return back()->with('success', 'Error resolved successfully');
    }

    /**
     * Reopen an error (AJAX)
     */
    public function reopen(Request $request, AdminErrorLog $errorLog)
    {
        // Authorization
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized');
        }

        $this->errorLogService->reopenError($errorLog);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Error reopened successfully',
                'error' => $errorLog->fresh(),
            ]);
        }

        return back()->with('success', 'Error reopened successfully');
    }

    /**
     * Mute errors with same signature (AJAX)
     */
    public function mute(Request $request, AdminErrorLog $errorLog)
    {
        // Authorization
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized');
        }

        $count = $this->errorLogService->muteSignature($errorLog);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Muted {$count} error(s) with this signature",
            ]);
        }

        return back()->with('success', "Muted {$count} error(s) with this signature");
    }

    /**
     * Unmute errors with same signature (AJAX)
     */
    public function unmute(Request $request, AdminErrorLog $errorLog)
    {
        // Authorization
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized');
        }

        $count = $this->errorLogService->unmuteSignature($errorLog);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Unmuted {$count} error(s) with this signature",
            ]);
        }

        return back()->with('success', "Unmuted {$count} error(s) with this signature");
    }

    /**
     * Add note to error
     */
    public function addNote(Request $request, AdminErrorLog $errorLog)
    {
        // Authorization
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'note' => 'required|string|min:3|max:1000',
        ]);

        $note = $errorLog->notes()->create([
            'note' => $validated['note'],
            'added_by_user_id' => Auth::id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Note added successfully',
                'note' => $note,
            ]);
        }

        return back()->with('success', 'Note added successfully');
    }

    /**
     * Delete note from error
     */
    public function deleteNote(Request $request, AdminErrorLogNote $note)
    {
        // Authorization: Only super_admin or note creator
        if (Auth::id() !== $note->added_by_user_id && !Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized');
        }

        $note->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Note deleted successfully',
            ]);
        }

        return back()->with('success', 'Note deleted successfully');
    }

    /**
     * Get error stats for dashboard widget
     */
    public function getStats(Request $request)
    {
        $stats = $this->errorLogService->getStats();

        return response()->json($stats);
    }

    /**
     * Export errors to CSV
     */
    public function export(Request $request)
    {
        // Authorization
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized');
        }

        $query = AdminErrorLog::query();

        // Apply same filters as index
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'open' => $query->where('is_resolved', false)->where('is_muted', false),
                'resolved' => $query->where('is_resolved', true),
                'muted' => $query->where('is_muted', true),
            };
        }

        if ($request->filled('environment')) {
            $query->where('environment', $request->environment);
        }

        $errors = $query->get();

        $filename = 'error-logs-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($errors) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID', 'Severity', 'Message', 'Exception', 'File', 'Line',
                'Route', 'Status Code', 'Environment', 'Occurrences',
                'First Seen', 'Last Seen', 'Status', 'User', 'IP'
            ]);

            // Data rows
            foreach ($errors as $error) {
                fputcsv($file, [
                    $error->id,
                    $error->severity,
                    $error->message,
                    $error->exception_class,
                    $error->file,
                    $error->line,
                    $error->route_name,
                    $error->status_code,
                    $error->environment,
                    $error->occurrences,
                    $error->first_seen_at,
                    $error->last_seen_at,
                    $error->is_muted ? 'Muted' : ($error->is_resolved ? 'Resolved' : 'Open'),
                    $error->user?->name ?? 'N/A',
                    $error->ip,
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }
}
