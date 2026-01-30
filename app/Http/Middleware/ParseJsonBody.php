<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ParseJsonBody
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldParse($request)) {
            $decoded = json_decode($request->getContent(), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $request->merge($decoded);
            }
        }

        return $next($request);
    }

    private function shouldParse(Request $request): bool
    {
        $contentType = $request->header('Content-Type', '');
        $looksJson = str_contains($contentType, 'application/json') || $request->isJson();

        return $looksJson && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    }
}
