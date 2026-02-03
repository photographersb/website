<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ErrorCenterController extends Controller
{
    /**
     * Get all error logs with filters
     */
    public function index(Request $request)
    {
        $query = AdminErrorLog::with(['user', 'resolvedByUser']);

        // Filter by status
        if ($request->has('status')) {
            match ($request->status) {
                'open' => $query->open(),
                'resolved' => $query->resolved(),
                'muted' => $query->muted(),
                default => null,
            };
        }

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by environment
        if ($request->filled('environment')) {
            $query->byEnvironment($request->environment);
        }

        // Filter by status code
        if ($request->filled('status_code')) {
            $query->where('status_code', $request->status_code);
        }

        // Filter by method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Group by error signature to show unique errors
        if ($request->boolean('group_by_signature')) {
            $query->selectRaw('*, MAX(created_at) as latest_occurrence')
                ->groupBy('error_signature')
                ->orderByRaw('MAX(created_at) DESC');
        } else {
            $query->latest();
        }

        $errors = $query->paginate($request->integer('per_page', 25));

        // For non-super-admin: redact sensitive info
        if (!auth()->user()->hasRole('super_admin')) {
            $errors->getCollection()->transform(function ($error) {
                $error->message = $error->getSafeMessage();
                $error->file = null;
                $error->line = null;
                $error->trace = null;
                return $error;
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => $errors,
            'filters' => [
                'severities' => ['P0', 'P1', 'P2', 'P3', 'P4'],
                'statuses' => ['open', 'resolved', 'muted'],
                'environments' => ['local', 'staging', 'production'],
                'methods' => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
            ],
        ]);
    }

    /**
     * Get single error detail
     */
    public function show(AdminErrorLog $error)
    {
        // Only super_admin can see full details
        if (!auth()->user()->hasRole('super_admin')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Only super admin can view error details'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $error->load('user', 'resolvedByUser'),
        ]);
    }

    /**
     * Mark error as resolved
     */
    public function markResolved(Request $request, AdminErrorLog $error)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $error->markResolved(auth()->user(), $request->notes);

        return response()->json([
            'status' => 'success',
            'message' => 'Error marked as resolved',
            'data' => $error,
        ]);
    }

    /**
     * Mark error as unresolved
     */
    public function markUnresolved(AdminErrorLog $error)
    {
        $error->markUnresolved();

        return response()->json([
            'status' => 'success',
            'message' => 'Error marked as unresolved',
            'data' => $error,
        ]);
    }

    /**
     * Mute similar errors
     */
    public function mute(AdminErrorLog $error)
    {
        $error->mute();

        return response()->json([
            'status' => 'success',
            'message' => 'Error signature muted - similar errors will be ignored',
            'data' => $error,
        ]);
    }

    /**
     * Unmute errors
     */
    public function unmute(AdminErrorLog $error)
    {
        $error->unmute();

        return response()->json([
            'status' => 'success',
            'message' => 'Error signature unmuted',
            'data' => $error,
        ]);
    }

    /**
     * Get error statistics
     */
    public function statistics()
    {
        $totalErrors = AdminErrorLog::count();
        $openErrors = AdminErrorLog::open()->count();
        $resolvedErrors = AdminErrorLog::resolved()->count();
        $criticalErrors = AdminErrorLog::critical()->count();

        $byEnvironment = AdminErrorLog::selectRaw('environment, COUNT(*) as count')
            ->groupBy('environment')
            ->get()
            ->pluck('count', 'environment');

        $bySeverity = AdminErrorLog::selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->get()
            ->pluck('count', 'severity');

        $todayErrors = AdminErrorLog::whereDate('created_at', today())->count();
        $last24HoursErrors = AdminErrorLog::where('created_at', '>=', now()->subHours(24))->count();

        // Most common errors
        $commonErrors = AdminErrorLog::selectRaw('error_signature, message, COUNT(*) as count')
            ->groupBy('error_signature', 'message')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_errors' => $totalErrors,
                'open_errors' => $openErrors,
                'resolved_errors' => $resolvedErrors,
                'critical_errors' => $criticalErrors,
                'today_errors' => $todayErrors,
                'last_24_hours_errors' => $last24HoursErrors,
                'by_environment' => $byEnvironment,
                'by_severity' => $bySeverity,
                'most_common_errors' => $commonErrors,
            ],
        ]);
    }

    /**
     * Clear resolved errors
     */
    public function clearResolved()
    {
        $count = AdminErrorLog::where('is_resolved', true)->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Deleted {$count} resolved errors",
        ]);
    }

    /**
     * Export errors as CSV
     */
    public function export(Request $request)
    {
        $query = AdminErrorLog::query();

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'open' => $query->open(),
                'resolved' => $query->resolved(),
                default => null,
            };
        }

        $errors = $query->get(['severity', 'environment', 'url', 'message', 'exception_class', 'status_code', 'created_at', 'is_resolved']);

        $csv = "Severity,Environment,URL,Message,Exception,Status Code,Date,Resolved\n";
        foreach ($errors as $error) {
            $csv .= implode(',', [
                $error->severity,
                $error->environment,
                '"' . addslashes($error->url) . '"',
                '"' . addslashes($error->message) . '"',
                $error->exception_class,
                $error->status_code,
                $error->created_at->format('Y-m-d H:i:s'),
                $error->is_resolved ? 'Yes' : 'No',
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="errors-' . date('Y-m-d-His') . '.csv"');
    }
}
