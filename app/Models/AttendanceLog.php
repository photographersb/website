<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $table = 'attendance_logs';

    protected $fillable = [
        'event_id',
        'registration_id',
        'user_id',
        'scanned_by_user_id',
        'method',
        'scanned_at',
        'notes',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the event this attendance log belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the registration associated with this attendance
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class);
    }

    /**
     * Get the user who attended
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin/staff who scanned this attendance
     */
    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by_user_id');
    }

    /**
     * Scopes
     */
    
    /**
     * Filter by event
     */
    public function scopeByEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Filter by scan method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('method', $method);
    }

    /**
     * Filter by QR scans only
     */
    public function scopeQrScans($query)
    {
        return $query->where('method', 'qr');
    }

    /**
     * Filter by manual scans only
     */
    public function scopeManualScans($query)
    {
        return $query->where('method', 'manual');
    }

    /**
     * Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('scanned_at', [$startDate, $endDate]);
    }

    /**
     * Filter by scanner/staff
     */
    public function scopeScannedBy($query, $userId)
    {
        return $query->where('scanned_by_user_id', $userId);
    }

    /**
     * Methods
     */

    /**
     * Mark this attendance as verified (QR scan successful)
     */
    public function markQrScanned($scannedByUserId)
    {
        $this->update([
            'method' => 'qr',
            'scanned_by_user_id' => $scannedByUserId,
            'scanned_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark this attendance as manual entry
     */
    public function markManualEntry($scannedByUserId, $notes = null)
    {
        $this->update([
            'method' => 'manual',
            'scanned_by_user_id' => $scannedByUserId,
            'scanned_at' => now(),
            'notes' => $notes,
        ]);

        return $this;
    }

    /**
     * Check if this is a duplicate scan (same registration scanned twice)
     */
    public static function isDuplicateScan($registrationId, $withinSeconds = 300)
    {
        return self::where('registration_id', $registrationId)
            ->where('scanned_at', '>=', now()->subSeconds($withinSeconds))
            ->exists();
    }

    /**
     * Get attendance count for event
     */
    public static function countForEvent($eventId)
    {
        return self::byEvent($eventId)->count();
    }

    /**
     * Get attendance rate for event
     */
    public static function attendanceRateForEvent($eventId)
    {
        $totalRegistrations = EventRegistration::byEvent($eventId)->count();
        if ($totalRegistrations === 0) {
            return 0;
        }

        $attendanceCount = self::countForEvent($eventId);
        return ($attendanceCount / $totalRegistrations) * 100;
    }
}
