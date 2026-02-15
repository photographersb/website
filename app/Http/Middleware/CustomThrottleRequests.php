<?php

namespace App\Http\Middleware;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Middleware\ThrottleRequests;
use App\Services\ThrottleEventLogger;

class CustomThrottleRequests extends ThrottleRequests
{
    public function handle($request, \Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        if (app()->environment('local')) {
            return $next($request);
        }

        return parent::handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
    }

    /**
     * Create a 'too many attempts' exception with a user-friendly message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @param  int  $maxAttempts
     * @param  callable|null  $responseCallback
    * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function buildException($request, $key, $maxAttempts, $responseCallback = null)
    {
        $retryAfter = $this->getTimeUntilNextRetry($key);

        try {
            $logger = app(ThrottleEventLogger::class);
            $logger->log($request, $retryAfter);
        } catch (\Throwable $e) {
            logger()->error('Throttle logging failed', [
                'error' => $e->getMessage(),
            ]);
        }

        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        $response = response()->json([
            'status' => 'error',
            'message' => 'Too many requests. Please slow down and try again later.',
            'retry_after' => $retryAfter . ' seconds',
        ], 429, $headers);

        return new HttpResponseException($response);
    }
}
