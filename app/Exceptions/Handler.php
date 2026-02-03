<?php

namespace App\Exceptions;

use App\Models\AdminErrorLog;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed when validation exceptions are thrown.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Report the exception to storage
     */
    public function report(Throwable $exception)
    {
        // Record critical errors to admin error logs
        // Skip certain exceptions to avoid noise
        if ($this->shouldNotReport($exception)) {
            return parent::report($exception);
        }

        try {
            AdminErrorLog::recordError($exception);
        } catch (\Exception $e) {
            // Silently fail if error logging fails
            logger('Error logging failed: ' . $e->getMessage());
        }

        parent::report($exception);
    }

    /**
     * Determine if exception should be reported to error logs
     */
    protected function shouldNotReport(Throwable $exception): bool
    {
        // Skip 404s in production
        if (app()->environment('production') && method_exists($exception, 'getStatusCode')) {
            if ($exception->getStatusCode() === 404) {
                return true;
            }
        }

        // Skip validation exceptions
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return true;
        }

        // Skip HTTP exceptions that are expected
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            if (in_array($exception->getStatusCode(), [400, 401, 403])) {
                return true;
            }
        }

        return false;
    }
}
