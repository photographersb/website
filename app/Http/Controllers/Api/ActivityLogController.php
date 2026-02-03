<?php

namespace App\Http\Controllers\Api;

use App\Models\ActivityLog;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    use ApiResponse;
    /**
     * Get activity logs (admin only)
     */
    public function index(Request $request)
    {
        // Only admins can view all activity logs
        if (!Auth::user()?->hasRole('admin')) {
            return $this->unauthorized('Unauthorized');
        }

        $query = ActivityLog::query();

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->has('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Get paginated results
        $logs = $query->latest()->paginate($request->get('per_page', 50));

        return $this->paginated($logs, 'Activity logs retrieved successfully');
    }

    /**
     * Get user's own activity logs
     */
    public function myActivity(Request $request)
    {
        $user = Auth::user();

        $query = ActivityLog::where('user_id', $user->id);

        // Filter by action
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->has('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        $logs = $query->latest()->paginate($request->get('per_page', 50));

        return $this->paginated($logs, 'Activity logs retrieved successfully');
    }

    /**
     * Get activity logs for a specific model
     */
    public function modelActivity(string $modelType, int $modelId, Request $request)
    {
        $logs = ActivityLog::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->latest()
            ->paginate($request->get('per_page', 50));

        return $this->paginated($logs, 'Model activity logs retrieved successfully');
    }

    /**
     * Get activity statistics (admin only)
     */
    public function statistics(Request $request)
    {
        if (!Auth::user()?->hasRole('admin')) {
            return $this->unauthorized('Unauthorized');
        }

        $days = $request->get('days', 30);

        // Total activities
        $totalActivities = ActivityLog::where('created_at', '>=', now()->subDays($days))->count();

        // Activities by action
        $activitiesByAction = ActivityLog::where('created_at', '>=', now()->subDays($days))
            ->groupBy('action')
            ->selectRaw('action, count(*) as count')
            ->orderBy('count', 'desc')
            ->get();

        // Most active users
        $activeUsers = ActivityLog::where('created_at', '>=', now()->subDays($days))
            ->groupBy('user_id')
            ->selectRaw('user_id, count(*) as count')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->with('user')
            ->get();

        // Activities by date
        $activitiesByDate = ActivityLog::where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return $this->success([
            'period_days' => $days,
            'total_activities' => $totalActivities,
            'activities_by_action' => $activitiesByAction,
            'most_active_users' => $activeUsers->map(fn($log) => [
                'user' => $log->user?->name,
                'activity_count' => $log->count,
            ]),
            'activities_by_date' => $activitiesByDate,
        ], 'Activity statistics retrieved successfully');
    }

    /**
     * Export activity logs to CSV (admin only)
     */
    public function export(Request $request)
    {
        if (!Auth::user()?->hasRole('admin')) {
            return $this->unauthorized('Unauthorized');
        }

        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';

        $handle = fopen('php://output', 'w');
        ob_start();

        fputcsv($handle, ['ID', 'User', 'Action', 'Model Type', 'Description', 'IP Address', 'Created At']);

        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->user?->name ?? 'System',
                $log->action,
                $log->model_type,
                $log->description,
                $log->ip_address,
                $log->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
