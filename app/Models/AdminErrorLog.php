<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminErrorLog extends Model
{
    protected $table = 'admin_error_logs';

    protected $fillable = [
        'severity',
        'environment',
        'url',
        'route_name',
        'method',
        'status_code',
        'user_id',
        'ip',
        'user_agent',
        'message',
        'exception_class',
        'file',
        'line',
        'trace',
        'signature_hash',
        'occurrences',
        'first_seen_at',
        'last_seen_at',
        'is_resolved',
        'resolved_by_user_id',
        'resolved_at',
        'is_muted',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'is_muted' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    protected $appends = ['severity_badge', 'status_label'];

    /**
     * Generate error signature for grouping similar errors
     */
    public static function generateSignature(string $exceptionClass, string $message, string $file = '', int $line = 0): string
    {
        return hash('sha256', "{$exceptionClass}::{$message}::{$file}::{$line}");
    }

    /**
     * Find or create error log entry
     */
    public static function recordError(
        \Throwable $exception,
        ?string $url = null,
        ?string $routeName = null,
        ?string $method = null,
        ?int $statusCode = null,
        ?int $userId = null,
        ?string $ip = null
    ): self {
        $severity = self::determineSeverity($exception);
        $signature = self::generateSignature(
            $exception::class,
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        // Check if similar error exists and increment count
        $existing = self::where('signature_hash', $signature)
            ->where('is_muted', false)
            ->latest()
            ->first();

        if ($existing && $existing->created_at->diffInMinutes(now()) < 5) {
            // Similar error within 5 minutes - increment count
            $existing->increment('occurrences');
            $existing->update(['last_seen_at' => now()]);
            return $existing;
        }

        // Create new error log
        return self::create([
            'severity' => $severity,
            'environment' => app()->environment(),
            'url' => $url ?? request()->fullUrl(),
            'route_name' => $routeName ?? request()->route()?->getName(),
            'method' => $method ?? request()->method(),
            'status_code' => $statusCode,
            'user_id' => $userId ?? Auth::id(),
            'ip' => $ip ?? request()->ip(),
            'user_agent' => request()->userAgent(),
            'message' => $exception->getMessage(),
            'exception_class' => $exception::class,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => json_encode(collect($exception->getTrace())
                ->map(fn ($line) => [
                    'function' => $line['function'] ?? null,
                    'class' => $line['class'] ?? null,
                    'file' => $line['file'] ?? null,
                    'line' => $line['line'] ?? null,
                ])
                ->take(20) // Limit trace depth
                ->toArray()),
            'signature_hash' => $signature,
            'occurrences' => 1,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);
    }

    /**
     * Determine severity based on exception type
     */
    public static function determineSeverity(\Throwable $exception): string
    {
        $class = $exception::class;

        // P0: Critical errors
        if (Str::contains($class, ['DatabaseException', 'ConnectionException', 'OutOfMemory'])) {
            return 'P0';
        }

        // P1: High priority errors
        if (Str::contains($class, ['AuthenticationException', 'AuthorizationException', 'ValidationException'])) {
            return 'P1';
        }

        // P2: Medium priority errors
        if (Str::contains($class, ['ModelNotFoundException', 'RouteNotFoundException'])) {
            return 'P2';
        }

        // P3: Low priority
        if (Str::contains($class, ['Warning', 'Notice', 'Deprecated'])) {
            return 'P3';
        }

        return 'P2'; // Default
    }

    /**
     * Get severity badge color
     */
    public function getSeverityBadgeAttribute(): string
    {
        return match ($this->severity) {
            'P0' => '🔴 Critical',
            'P1' => '🟠 High',
            'P2' => '🟡 Medium',
            'P3' => '🟢 Low',
            'P4' => '⚪ Info',
            default => '⚪ Unknown'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->is_muted) {
            return '🔇 Muted';
        }
        return $this->is_resolved ? '✅ Resolved' : '⚠️ Open';
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Guest User',
            'email' => 'guest@system.local'
        ]);
    }

    public function resolvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_user_id')->withDefault([
            'name' => 'System Admin',
            'email' => 'admin@system.local'
        ]);
    }
    
    public function notes()
    {
        return $this->hasMany(AdminErrorLogNote::class, 'error_log_id');
    }

    /**
     * Scopes
     */
    public function scopeOpen($query)
    {
        return $query->where('is_resolved', false)->where('is_muted', false);
    }

    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    public function scopeMuted($query)
    {
        return $query->where('is_muted', true);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'P0');
    }

    public function scopeByEnvironment($query, string $environment)
    {
        return $query->where('environment', $environment);
    }

    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where('message', 'like', "%{$term}%")
            ->orWhere('url', 'like', "%{$term}%")
            ->orWhere('route_name', 'like', "%{$term}%")
            ->orWhere('exception_class', 'like', "%{$term}%");
    }

    /**): void
    {
        $this->update([
            'is_resolved' => true,
            'resolved_by_user_id' => $user->id,
            'resolved_at' => now()true,
            'resolved_by_user_id' => $user->id,
            'resolved_at' => now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Mark as unresolved
     */
    public function markUnresolved(): void
    {
        $this->update([
            'is_resolved' => false,
            'resolved_by_user_id' => null,
            'resolved_at' => null,
        ]);
    }

    /**
     * Mute similar errors
     */
    public function mute(): void
    {
        $this->update(['is_muted' => true]);
    }

    /**
     * Unmute
     */
    public function unmute(): void
    {
        $this->update(['is_muted' => false]);
    }

    /**
     * Get formatted trace for display
     */
    public function getFormattedTrace(): array
    {
        return $this->trace ?? [];
    }

    /**
     * Get safe message (for non-super-admin)
     */
    public function getSafeMessage(): string
    {
        if (Auth::user()?->role === 'super_admin') {
            return $this->message;
        }
        
        // Redact sensitive information
        $message = $this->message;
        $message = preg_replace('/password|token|secret|key/i', '***', $message);
        return $message;
    }
}
