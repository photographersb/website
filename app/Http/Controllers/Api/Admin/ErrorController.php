<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminIpBlock;
use App\Models\ActivityLog;
use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;

class ErrorController extends Controller
{
    public function index(Request $request)
    {
        $query = ErrorLog::query();
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('file', 'like', "%{$search}%")
                  ->orWhere('exception_class', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->filled('environment')) {
            $query->where('environment', $request->input('environment'));
        }
        
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'resolved') {
                $query->where('is_resolved', true);
            } elseif ($status === 'open') {
                $query->where('is_resolved', false);
            } elseif ($status === 'muted') {
                $query->where('is_muted', true);
            }
        }

        if ($request->has('is_resolved')) {
            $query->where('is_resolved', $request->boolean('is_resolved'));
        }

        if ($request->has('is_muted')) {
            $query->where('is_muted', $request->boolean('is_muted'));
        }

        $from = $request->input('from');
        $to = $request->input('to');
        if ($from || $to) {
            $fromDate = $from ? Carbon::parse($from)->startOfDay() : null;
            $toDate = $to ? Carbon::parse($to)->endOfDay() : null;

            if ($fromDate && $toDate) {
                $query->whereBetween('last_occurrence_at', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->where('last_occurrence_at', '>=', $fromDate);
            } elseif ($toDate) {
                $query->where('last_occurrence_at', '<=', $toDate);
            }
        }
        
        $errors = $query->orderBy('last_occurrence_at', 'desc')->paginate($request->input('per_page', 20));
        
        return response()->json([
            'status' => 'success',
            'data' => $errors->items(),
            'pagination' => [
                'total' => $errors->total(),
                'per_page' => $errors->perPage(),
                'current_page' => $errors->currentPage(),
                'last_page' => $errors->lastPage(),
            ]
        ]);
    }

    public function statistics(Request $request)
    {
        $query = ErrorLog::query();

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->filled('environment')) {
            $query->where('environment', $request->input('environment'));
        }

        $from = $request->input('from');
        $to = $request->input('to');
        if ($from || $to) {
            $fromDate = $from ? Carbon::parse($from)->startOfDay() : null;
            $toDate = $to ? Carbon::parse($to)->endOfDay() : null;

            if ($fromDate && $toDate) {
                $query->whereBetween('last_occurrence_at', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->where('last_occurrence_at', '>=', $fromDate);
            } elseif ($toDate) {
                $query->where('last_occurrence_at', '<=', $toDate);
            }
        }

        $total = (clone $query)->count();
        $open = (clone $query)->where('is_resolved', false)->count();
        $resolved = (clone $query)->where('is_resolved', true)->count();
        $critical = (clone $query)->where('severity', 'P0')->count();
        $muted = (clone $query)->where('is_muted', true)->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $total,
                'open' => $open,
                'resolved' => $resolved,
                'critical' => $critical,
                'muted' => $muted,
            ]
        ]);
    }

    public function show(ErrorLog $error)
    {
        $ipBlocked = false;
        if ($error->ip) {
            $ipBlocked = AdminIpBlock::where('ip', $error->ip)->exists();
        }

        return response()->json([
            'status' => 'success',
            'data' => array_merge($error->toArray(), [
                'ip_blocked' => $ipBlocked,
            ]),
        ]);
    }

    public function blockIp(Request $request, ErrorLog $error)
    {
        if (!$error->ip) {
            return response()->json([
                'status' => 'error',
                'message' => 'No IP address found for this error.',
            ], 422);
        }

        $block = AdminIpBlock::firstOrCreate([
            'ip' => $error->ip,
        ], [
            'reason' => $request->input('reason', 'Blocked from Error Center'),
            'blocked_by_user_id' => auth()->id(),
            'expires_at' => $request->filled('expires_at') ? Carbon::parse($request->input('expires_at')) : null,
            'geo_country' => $error->geo_country,
            'geo_region' => $error->geo_region,
            'geo_city' => $error->geo_city,
            'geo_lat' => $error->geo_lat,
            'geo_lng' => $error->geo_lng,
            'geo_timezone' => $error->geo_timezone,
            'geo_isp' => $error->geo_isp,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'ip_blocked' => true,
                'block' => $block,
            ],
        ]);
    }

    public function unblockIp(ErrorLog $error)
    {
        if ($error->ip) {
            AdminIpBlock::where('ip', $error->ip)->delete();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'ip_blocked' => false,
            ],
        ]);
    }

    public function unlockThrottle(ErrorLog $error)
    {
        if (!$error->ip) {
            return response()->json([
                'status' => 'error',
                'message' => 'No IP address found for this error.',
            ], 422);
        }

        $keys = $this->getThrottleKeys($error->ip, $error->url);
        foreach ($keys as $key) {
            RateLimiter::clear($key);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'throttle_unlock',
            'model_type' => null,
            'model_id' => null,
            'description' => 'Throttle cleared for IP ' . $error->ip,
            'properties' => [
                'ip' => $error->ip,
                'keys' => $keys,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'unlocked' => true,
            ],
        ]);
    }

    private function getThrottleKeys(string $ip, ?string $url): array
    {
        $host = $url ? parse_url($url, PHP_URL_HOST) : '';
        $host = is_string($host) ? $host : '';

        $values = array_unique([
            $host . '|' . $ip,
            '|' . $ip,
        ]);

        $keys = [];
        foreach ($values as $value) {
            $keys[] = sha1($value);
            $keys[] = $value;
        }

        return array_values(array_unique($keys));
    }

    public function resolve(Request $request, ErrorLog $error)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $error->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by_user_id' => auth()->id(),
            'notes' => $validated['notes'] ?? $error->notes,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $error,
        ]);
    }

    public function unresolve(ErrorLog $error)
    {
        $error->update([
            'is_resolved' => false,
            'resolved_at' => null,
            'resolved_by_user_id' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $error,
        ]);
    }

    public function mute(ErrorLog $error)
    {
        $error->update([
            'is_muted' => true,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $error,
        ]);
    }

    public function unmute(ErrorLog $error)
    {
        $error->update([
            'is_muted' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $error,
        ]);
    }

    public function export(Request $request)
    {
        $query = ErrorLog::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('file', 'like', "%{$search}%")
                  ->orWhere('exception_class', 'like', "%{$search}%");
            });
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->filled('environment')) {
            $query->where('environment', $request->input('environment'));
        }

        $from = $request->input('from');
        $to = $request->input('to');
        if ($from || $to) {
            $fromDate = $from ? Carbon::parse($from)->startOfDay() : null;
            $toDate = $to ? Carbon::parse($to)->endOfDay() : null;

            if ($fromDate && $toDate) {
                $query->whereBetween('last_occurrence_at', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->where('last_occurrence_at', '>=', $fromDate);
            } elseif ($toDate) {
                $query->where('last_occurrence_at', '<=', $toDate);
            }
        }

        $errors = $query->orderBy('last_occurrence_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="error-logs.csv"',
        ];

        $callback = function () use ($errors) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'Severity',
                'Environment',
                'Message',
                'File',
                'Line',
                'Occurrences',
                'Last Occurred',
                'Resolved',
                'Muted',
            ]);

            foreach ($errors as $error) {
                fputcsv($handle, [
                    $error->id,
                    $error->severity,
                    $error->environment,
                    $error->message,
                    $error->file,
                    $error->line,
                    $error->occurrence_count,
                    optional($error->last_occurrence_at)->toDateTimeString(),
                    $error->is_resolved ? 'yes' : 'no',
                    $error->is_muted ? 'yes' : 'no',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'is_resolved' => 'required|boolean',
        ]);
        
        $error = ErrorLog::findOrFail($id);
        $error->update([
            'is_resolved' => $validated['is_resolved'],
            'resolved_at' => $validated['is_resolved'] ? now() : null,
            'resolved_by_user_id' => $validated['is_resolved'] ? auth()->id() : null,
        ]);
        
        return response()->json(['data' => $error]);
    }
}
