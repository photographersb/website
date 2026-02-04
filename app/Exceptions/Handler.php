<?php

namespace App\Exceptions;

use App\Services\ErrorLogService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        // Skip certain exceptions to avoid noise
        if (!$this->shouldReportToErrorCenter($exception)) {
            return parent::report($exception);
        }

        try {
            $errorLogService = app(ErrorLogService::class);
            $errorLogService->logException(
                exception: $exception,
                url: request()->fullUrl(),
                routeName: request()->route()?->getName(),
                method: request()->method(),
                statusCode: $this->getStatusCode($exception),
                userId: auth()->id(),
                ip: request()->ip()
            );
        } catch (Throwable $e) {
            // Silently fail if error logging fails to prevent infinite loops
            logger()->error('Error Center logging failed', [
                'error' => $e->getMessage(),
                'original_exception' => $exception->getMessage(),
            ]);
        }

        parent::report($exception);
    }

    /**
     * Get HTTP status code from exception
     */
    protected function getStatusCode(Throwable $exception): ?int
    {
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        if ($exception instanceof HttpException) {
            return $exception->getStatusCode();
        }

        return null;
    }

    /**
     * Determine if exception should be reported to Error Center
     */
    protected function shouldReportToErrorCenter(Throwable $exception): bool
    {
        // Skip 404s (unless in development)
        if ($exception instanceof NotFoundHttpException && !app()->environment('local')) {
            return false;
        }

        // Skip validation exceptions (user input errors)
        if ($exception instanceof ValidationException) {
            return false;
        }

        // Skip expected HTTP exceptions (400, 401, 403, 429)
        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
            if (in_array($code, [400, 401, 403, 404, 429])) {
                return false;
            }
        }

        // Skip authentication/authorization exceptions (expected behavior)
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return false;
        }

        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return false;
        }

        // Report everything else
        return true;
    }
}
