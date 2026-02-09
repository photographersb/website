<?php

namespace App\Http\Middleware;

use App\Models\AdminIpBlock;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BlockIp
{
    public function handle(Request $request, Closure $next)
    {
        if (!Schema::hasTable('admin_ip_blocks')) {
            return $next($request);
        }

        $ip = $request->ip();
        if (!$ip) {
            return $next($request);
        }

        $block = AdminIpBlock::where('ip', $ip)->first();
        if (!$block) {
            return $next($request);
        }

        if ($block->expires_at && $block->expires_at->isPast()) {
            $block->delete();
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access blocked for this IP address.',
                'reason' => $block->reason,
                'expires_at' => optional($block->expires_at)->toIso8601String(),
            ], 403);
        }

        abort(403, 'Access blocked for this IP address.');
    }
}
