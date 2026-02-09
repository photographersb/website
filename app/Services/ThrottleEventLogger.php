<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ThrottleEventLogger
{
    public function __construct(private GeoIpService $geoIpService)
    {
    }

    public function log(Request $request, ?int $retryAfterSeconds = null): void
    {
        $ip = $request->ip();
        $path = '/' . ltrim($request->path(), '/');
        $cacheKey = 'throttle-event:' . sha1($path . '|' . $ip);

        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, 60);

        $geo = $this->geoIpService->lookup($ip);
        $signature = hash('sha256', '429|' . $path . '|' . $ip);

        $existing = ErrorLog::where('error_signature', $signature)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($existing) {
            $existing->increment('occurrence_count');
            $existing->update(['last_occurrence_at' => now()]);
            return;
        }

        ErrorLog::create([
            'severity' => 'P4',
            'environment' => app()->environment(),
            'url' => $request->fullUrl(),
            'route_name' => $request->route()?->getName(),
            'method' => $request->method(),
            'status_code' => 429,
            'user_id' => auth()->id(),
            'ip' => $ip,
            'user_agent' => $request->userAgent(),
            'message' => 'Too many attempts (rate limited)',
            'exception_class' => 'ThrottleRequestsException',
            'error_signature' => $signature,
            'occurrence_count' => 1,
            'last_occurrence_at' => now(),
            'geo_country' => $geo['country'] ?? null,
            'geo_region' => $geo['region'] ?? null,
            'geo_city' => $geo['city'] ?? null,
            'geo_lat' => $geo['lat'] ?? null,
            'geo_lng' => $geo['lng'] ?? null,
            'geo_timezone' => $geo['timezone'] ?? null,
            'geo_isp' => $geo['isp'] ?? null,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'throttle',
            'model_type' => null,
            'model_id' => null,
            'description' => 'Rate limit exceeded for ' . $path,
            'properties' => [
                'endpoint' => $path,
                'retry_after' => $retryAfterSeconds,
                'ip' => $ip,
                'geo' => $geo,
            ],
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
        ]);
    }
}
