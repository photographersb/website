<?php

namespace App\Services;

use App\Models\AdminErrorLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ErrorLogService
{
    /**
     * Log an exception to the error center
     */
    public function logException(
        Throwable $exception,
        ?string $url = null,
        ?string $routeName = null,
        ?string $method = null,
        ?int $statusCode = null,
        ?int $userId = null,
        ?string $ip = null
    ): ?AdminErrorLog {
        try {
            // Skip logging for muted errors
            $signature = $this->generateSignature($exception);
            
            if ($this->isSignatureMuted($signature)) {
                return null;
            }

            // Check for duplicate within time window
            $existing = AdminErrorLog::where('signature_hash', $signature)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->first();

            if ($existing) {
                // Update existing error
                $existing->increment('occurrences');
                $existing->update(['last_seen_at' => now()]);
                return $existing;
            }

            // Create new error log
            $errorLog = AdminErrorLog::create([
                'severity' => $this->determineSeverity($exception),
                'environment' => app()->environment(),
                'url' => $url ?? request()->fullUrl(),
                'route_name' => $routeName ?? request()->route()?->getName(),
                'method' => $method ?? request()->method(),
                'status_code' => $statusCode ?? $this->extractStatusCode($exception),
                'user_id' => $userId ?? Auth::id(),
                'ip' => $ip ?? request()->ip(),
                'user_agent' => request()->userAgent(),
                'message' => $this->sanitizeMessage($exception->getMessage()),
                'exception_class' => get_class($exception),
                'file' => $this->sanitizePath($exception->getFile()),
                'line' => $exception->getLine(),
                'trace' => $this->formatTrace($exception->getTrace()),
                'signature_hash' => $signature,
                'occurrences' => 1,
                'first_seen_at' => now(),
                'last_seen_at' => now(),
            ]);

            // Send notification for P0 errors
            if ($errorLog->severity === 'P0') {
                $this->notifySuperAdmins($errorLog);
            }

            return $errorLog;
        } catch (Throwable $e) {
            // Fallback: Log to Laravel log if error logging fails
            Log::error('Failed to log error to Error Center', [
                'error' => $e->getMessage(),
                'original_exception' => $exception->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Generate unique signature for error deduplication
     */
    public function generateSignature(Throwable $exception): string
    {
        $data = sprintf(
            '%s::%s::%s::%d',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        return hash('sha256', $data);
    }

    /**
     * Check if signature is muted
     */
    private function isSignatureMuted(string $signature): bool
    {
        return AdminErrorLog::where('signature_hash', $signature)
            ->where('is_muted', true)
            ->exists();
    }

    /**
     * Determine error severity based on exception type and context
     */
    public function determineSeverity(Throwable $exception): string
    {
        $class = get_class($exception);
        $message = $exception->getMessage();

        // P0: Critical - System-breaking errors
        if (
            Str::contains($class, ['PDOException', 'QueryException', 'ConnectionException']) ||
            Str::contains($class, ['OutOfMemoryError', 'FatalErrorException']) ||
            Str::contains($message, ['database', 'connection refused', 'out of memory'])
        ) {
            return 'P0';
        }

        // P1: High - Business-critical features broken
        if (
            Str::contains($class, ['AuthenticationException', 'AuthorizationException']) ||
            Str::contains($class, ['PaymentException', 'BookingException']) ||
            $exception->getCode() >= 500
        ) {
            return 'P1';
        }

        // P2: Medium - Non-critical feature issues
        return 'P2';
    }

    /**
     * Sanitize error message (remove sensitive data)
     */
    private function sanitizeMessage(string $message): string
    {
        // Remove potential sensitive patterns
        $patterns = [
            '/password[=:]?\s*[\'"]?[\w\d!@#$%^&*()]+[\'"]?/i' => 'password=***',
            '/api[_-]?key[=:]?\s*[\'"]?[\w\d]+[\'"]?/i' => 'api_key=***',
            '/token[=:]?\s*[\'"]?[\w\d\-_\.]+[\'"]?/i' => 'token=***',
            '/secret[=:]?\s*[\'"]?[\w\d]+[\'"]?/i' => 'secret=***',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $message = preg_replace($pattern, $replacement, $message);
        }

        return Str::limit($message, 500);
    }

    /**
     * Sanitize file paths (remove absolute paths for security)
     */
    private function sanitizePath(string $path): string
    {
        $basePath = base_path();
        return str_replace($basePath, '', $path);
    }

    /**
     * Format trace for storage (limit depth and sanitize)
     */
    private function formatTrace(array $trace): string
    {
        $formatted = collect($trace)
            ->take(20) // Limit to 20 frames
            ->map(function ($frame) {
                return [
                    'file' => isset($frame['file']) ? $this->sanitizePath($frame['file']) : null,
                    'line' => $frame['line'] ?? null,
                    'function' => $frame['function'] ?? null,
                    'class' => $frame['class'] ?? null,
                ];
            })
            ->filter(fn($frame) => !empty($frame['file'])) // Remove frames without files
            ->values()
            ->toArray();

        return json_encode($formatted);
    }

    /**
     * Check if exception is HTTP exception
     */
    private function isHttpException(Throwable $exception): bool
    {
        return $exception instanceof HttpExceptionInterface;
    }

    /**
     * Extract status code only when exception supports it
     */
    private function extractStatusCode(Throwable $exception): ?int
    {
        if (!$this->isHttpException($exception)) {
            return null;
        }

        /** @var HttpExceptionInterface $exception */
        return $exception->getStatusCode();
    }

    /**
     * Send notification to super admins for P0 errors
     */
    private function notifySuperAdmins(AdminErrorLog $errorLog): void
    {
        try {
            $superAdmins = User::whereHas('roles', function ($query) {
                $query->where('name', 'super_admin');
            })->get();

            foreach ($superAdmins as $admin) {
                // Send email notification (implement as needed)
                // Mail::to($admin->email)->send(new CriticalErrorAlert($errorLog));
                
                // Log for now
                Log::warning('P0 Error detected - notification sent to super admin', [
                    'admin_id' => $admin->id,
                    'error_id' => $errorLog->id,
                    'message' => $errorLog->message,
                ]);
            }
        } catch (Throwable $e) {
            Log::error('Failed to notify super admins', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Resolve an error
     */
    public function resolveError(AdminErrorLog $errorLog, User $user): bool
    {
        return $errorLog->update([
            'is_resolved' => true,
            'resolved_by_user_id' => $user->id,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Reopen a resolved error
     */
    public function reopenError(AdminErrorLog $errorLog): bool
    {
        return $errorLog->update([
            'is_resolved' => false,
            'resolved_by_user_id' => null,
            'resolved_at' => null,
        ]);
    }

    /**
     * Mute errors with same signature
     */
    public function muteSignature(AdminErrorLog $errorLog): int
    {
        return AdminErrorLog::where('signature_hash', $errorLog->signature_hash)
            ->update(['is_muted' => true]);
    }

    /**
     * Unmute errors with same signature
     */
    public function unmuteSignature(AdminErrorLog $errorLog): int
    {
        return AdminErrorLog::where('signature_hash', $errorLog->signature_hash)
            ->update(['is_muted' => false]);
    }

    /**
     * Get error statistics
     */
    public function getStats(): array
    {
        return [
            'errors_today' => AdminErrorLog::whereDate('created_at', today())->count(),
            'open_errors' => AdminErrorLog::where('is_resolved', false)
                ->where('is_muted', false)
                ->count(),
            'p0_errors' => AdminErrorLog::where('severity', 'P0')
                ->where('is_resolved', false)
                ->where('is_muted', false)
                ->count(),
            'muted_errors' => AdminErrorLog::where('is_muted', true)->count(),
            'total_occurrences' => AdminErrorLog::sum('occurrences'),
            'errors_by_severity' => AdminErrorLog::selectRaw('severity, COUNT(*) as count')
                ->groupBy('severity')
                ->pluck('count', 'severity')
                ->toArray(),
        ];
    }

    /**
     * Clean old resolved errors (run in scheduled task)
     */
    public function cleanOldErrors(int $daysToKeep = 90): int
    {
        return AdminErrorLog::where('is_resolved', true)
            ->where('resolved_at', '<', now()->subDays($daysToKeep))
            ->delete();
    }
}
