<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAuthenticationEvents
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $wasAuthenticated = auth()->check();

        $response = $next($request);

        $isNowAuthenticated = auth()->check();

        // Log logout if user was authenticated and now isn't
        if ($wasAuthenticated && !$isNowAuthenticated) {
            try {
                ActivityLogService::logLogout($request->session()->get('logged_out_user_id'));
            } catch (\Exception $e) {
                \Log::error('Failed to log logout: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
