<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // If user is not authenticated, return 401
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // If no roles specified, allow the request
        if (empty($roles)) {
            return $next($request);
        }

        // Get user role
        $userRole = $request->user()->role ?? 'user';
        
        // Super admin has access to everything
        if ($userRole === 'super_admin') {
            return $next($request);
        }
        
        // Check if user has one of the required roles
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // User doesn't have required role
        return response()->json([
            'message' => 'You do not have permission to access this resource.'
        ], 403);
    }
}
