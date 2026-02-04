<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificate extends Model
{
    protected $fillable = [
        'certificate_code',
        'template_id',
        'event_id',
        'competition_id',
        'issued_to_user_id',
        'issued_to_name',
        'issued_to_email',
        'issue_date',
        'valid_until',
        'verification_qr_path',
        'pdf_path',
        'status',
        'revoked_at',
        'revoked_by_user_id',
        'created_by_user_id',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_until' => 'date',
        'revoked_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    public function issuedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_to_user_id');
    }

    public function revokedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by_user_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CertificateIssueLog::class, 'certificate_id');
    }

    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    public function isValid(): bool
    {
        if ($this->isRevoked()) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        return true;
    }

    public function getParticipantName(): string
    {
        if ($this->issuedToUser) {
            return $this->issuedToUser->full_name ?? $this->issuedToUser->name;
        }

        return $this->issued_to_name;
    }

    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopeRevoked($query)
    {
        return $query->where('status', 'revoked');
    }

    public function scopeByEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeByCompetition($query, $competitionId)
    {
        return $query->where('competition_id', $competitionId);
    }
}
