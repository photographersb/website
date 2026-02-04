<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminSitemapCheckResult extends Model
{
    use HasFactory;

    protected $table = 'admin_sitemap_check_results';

    protected $fillable = [
        'check_id',
        'module',
        'route_name',
        'url',
        'method',
        'status_code',
        'response_time_ms',
        'result_status',
        'error_summary',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the check this result belongs to
     */
    public function check(): BelongsTo
    {
        return $this->belongsTo(AdminSitemapCheck::class, 'check_id');
    }

    /**
     * Check if passed
     */
    public function isPassed(): bool
    {
        return $this->result_status === 'passed';
    }

    /**
     * Check if failed
     */
    public function isFailed(): bool
    {
        return $this->result_status === 'failed';
    }

    /**
     * Check if skipped
     */
    public function isSkipped(): bool
    {
        return $this->result_status === 'skipped';
    }

    /**
     * Get recommended fix based on error
     */
    public function getRecommendedFix(): string
    {
        return match($this->status_code) {
            404 => 'Route not found. Check if route is properly registered in routes/api.php or routes/web.php',
            403 => 'Access denied. Check user permissions and middleware. User may need admin role.',
            500 => 'Server error. Check application logs: storage/logs/laravel.log',
            null => match($this->result_status) {
                'skipped' => 'Route requires parameters. Test manually with specific IDs.',
                'failed' => 'Unable to test. Check route is accessible and middleware is configured.',
                default => 'Unknown error'
            },
            default => "HTTP {$this->status_code}: Review the error details above"
        };
    }

    /**
     * Get badge color class
     */
    public function getBadgeClass(): string
    {
        return match($this->result_status) {
            'passed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'skipped' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
