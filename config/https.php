<?php

/**
 * HTTPS Configuration
 * 
 * Controls HTTPS enforcement and security headers for the application.
 * 
 * Only production environment enforces HTTPS by default.
 * Use APP_FORCE_HTTPS to override (useful for staging on HTTPS).
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Force HTTPS in Production
    |--------------------------------------------------------------------------
    |
    | When true, all requests in production will be redirected to HTTPS.
    | This prevents sensitive data (passwords, tokens) from being transmitted
    | over unencrypted connections.
    |
    | Set to false to disable (not recommended for production).
    | Can be overridden with APP_FORCE_HTTPS environment variable.
    |
    */
    'force_https_production' => env('APP_FORCE_HTTPS', true),

    /*
    |--------------------------------------------------------------------------
    | Force HTTPS in Non-Production
    |--------------------------------------------------------------------------
    |
    | When true, all requests in development/staging will also be redirected
    | to HTTPS if available. Useful for testing HTTPS-dependent features.
    |
    | Default: false (development typically uses HTTP)
    |
    */
    'force_https_non_production' => env('APP_FORCE_HTTPS_STAGING', false),

    /*
    |--------------------------------------------------------------------------
    | HSTS (HTTP Strict Transport Security)
    |--------------------------------------------------------------------------
    |
    | HSTS tells browsers to always use HTTPS for this domain.
    | 
    | max_age: How long (in seconds) browsers should remember this setting.
    |          31536000 = 1 year (recommended for production)
    |          86400 = 1 day (good for testing)
    |
    | include_subdomains: Include all subdomains in the HSTS policy
    |
    | preload: Allow domain to be included in browser preload lists
    |          (further strengthens HTTPS enforcement)
    |
    */
    'hsts' => [
        'enabled' => env('HSTS_ENABLED', true),
        'max_age' => env('HSTS_MAX_AGE', 31536000), // 1 year
        'include_subdomains' => env('HSTS_INCLUDE_SUBDOMAINS', true),
        'preload' => env('HSTS_PRELOAD', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | If running behind a load balancer or reverse proxy that handles HTTPS,
    | list the proxy IPs here so Laravel knows to trust their headers.
    |
    | Common scenarios:
    | - AWS ELB/ALB: 10.0.0.0/8
    | - Cloudflare: See https://www.cloudflare.com/ips/
    | - Local development: [] (empty array)
    |
    */
    'trusted_proxies' => env('TRUSTED_PROXIES', '*'),

    /*
    |--------------------------------------------------------------------------
    | X-Forwarded-Proto Header
    |--------------------------------------------------------------------------
    |
    | If behind a proxy, use X-Forwarded-Proto header to detect the original
    | request scheme (HTTP vs HTTPS).
    |
    | Set to false if your proxy doesn't send this header.
    |
    */
    'trust_forwarded_proto' => env('TRUST_FORWARDED_PROTO', true),
];
