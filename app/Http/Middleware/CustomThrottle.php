<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class CustomThrottle
{
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        if (app()->environment('local')) {
            return $next($request);
        }

        $key = $this->resolveRequestSignature($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Too many requests. Please try again in ' . $seconds . ' seconds.',
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        return $response->header('X-RateLimit-Limit', $maxAttempts)
                        ->header('X-RateLimit-Remaining', RateLimiter::remaining($key, $maxAttempts));
    }

    protected function resolveRequestSignature(Request $request)
    {
        $user = $request->user();
        
        // More specific throttling based on endpoint + user/IP
        $endpoint = $request->path();
        
        if ($user) {
            return 'throttle:' . sha1($endpoint . '|' . $user->id);
        }
        
        return 'throttle:' . sha1($endpoint . '|' . $request->ip());
    }
}
