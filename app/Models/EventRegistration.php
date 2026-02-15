<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'registration_code',
        'ticket_qr_path',
        'ticket_id',
        'qty',
        'quantity',
        'total_amount',
        'qr_token',
        'status',
        'lock_token',
        'locked_at',
        'payment_expires_at',
        'attended_at',
        'checked_in_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'locked_at' => 'datetime',
        'payment_expires_at' => 'datetime',
        'attended_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(EventTicket::class);
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function payment()
    {
        return $this->hasOne(EventPayment::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePendingPayment($query)
    {
        return $query->where('status', 'pending_payment');
    }

    public function scopeAttended($query)
    {
        return $query->where('status', 'attended');
    }

    public function scopeByEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registration) {
            if (!$registration->qr_token) {
                $registration->qr_token = Str::random(32);
            }
            if (!$registration->registration_code) {
                $registration->registration_code = 'REG-' . strtoupper(Str::random(8));
            }
        });
    }

    // Methods
    public function generateQrToken()
    {
        $this->qr_token = Str::random(32);
        return $this;
    }

    public function markAsAttended($checkedInBy = null)
    {
        $this->update([
            'status' => 'attended',
            'attended_at' => now(),
            'checked_in_by' => $checkedInBy,
        ]);
    }

    public function isAttended()
    {
        return $this->status === 'attended';
    }

    public function canCheckIn()
    {
        return $this->status === 'confirmed' && !$this->isAttended();
    }

    public function markAsConfirmed()
    {
        $this->update(['status' => 'confirmed']);
    }

    public function markAsCancelled()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function getQrCodeUrl()
    {
        return route('events.check-in.scan', ['token' => $this->qr_token]);
    }
}
