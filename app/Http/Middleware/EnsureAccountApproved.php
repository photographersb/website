<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AccountApprovalValidator;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountApproved
{
    /**
     * Handle an incoming request.
     * 
     * Ensures user account is approved before accessing paid features.
     * Can be used on routes that require account approval.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If not authenticated, let auth middleware handle it
        if (!$request->user()) {
            return $next($request);
        }

        $user = $request->user();

        // Check if account is approved
        $validation = AccountApprovalValidator::isApprovedForPaidAccess($user);

        if (!$validation['approved']) {
            // Log the denial
            \Log::warning('Paid access denied: account not approved', [
                'user_id' => $user->id,
                'reason' => $validation['reason'],
            ]);

            // Return JSON error for API requests
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['reason'] ?? 'Your account is not approved.',
                    'status' => $validation['user_status'] ?? 'not_approved',
                ], 403);
            }

            // Redirect with message for web requests
            return redirect()
                ->back()
                ->with('error', $validation['reason'] ?? 'Your account is not approved.')
                ->with('status', $validation['user_status'] ?? 'not_approved');
        }

        return $next($request);
    }
}
