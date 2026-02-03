<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsInProduction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $forceHttpsProduction = config('https.force_https_production', true);
        $forceHttpsNonProduction = config('https.force_https_non_production', false);
        $hstsConfig = config('https.hsts', []);
        $hstsEnabled = $hstsConfig['enabled'] ?? true;

        $shouldForceHttps = false;

        // Determine if we should force HTTPS
        if (app()->environment('production') && $forceHttpsProduction) {
            $shouldForceHttps = true;
        } elseif (!app()->environment('production') && $forceHttpsNonProduction) {
            $shouldForceHttps = true;
        }

        // Redirect HTTP to HTTPS if needed
        if ($shouldForceHttps && !$request->isSecure()) {
            $hstsHeader = $this->buildHstsHeader($hstsConfig);

            return redirect(
                url: $request->getBaseUrl() . $request->getRequestUri(),
                status: 301,
                headers: $hstsHeader ? ['Strict-Transport-Security' => $hstsHeader] : []
            )->secure();
        }

        // Process the request
        $response = $next($request);

        // Add HSTS header to HTTPS responses if enabled
        if ($hstsEnabled && $request->isSecure()) {
            $hstsHeader = $this->buildHstsHeader($hstsConfig);
            if ($hstsHeader) {
                $response->header('Strict-Transport-Security', $hstsHeader);
            }
        }

        return $response;
    }

    /**
     * Build HSTS header value from configuration
     */
    private function buildHstsHeader(array $hstsConfig): string
    {
        if (!($hstsConfig['enabled'] ?? true)) {
            return '';
        }

        $maxAge = $hstsConfig['max_age'] ?? 31536000;
        $header = "max-age={$maxAge}";

        if ($hstsConfig['include_subdomains'] ?? true) {
            $header .= '; includeSubDomains';
        }

        if ($hstsConfig['preload'] ?? true) {
            $header .= '; preload';
        }

        return $header;
    }
}
