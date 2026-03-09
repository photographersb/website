<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'source_type',
        'source_id',
        'user_id',
        'recipient_name',
        'issued_at',
        'certificate_path',
        'png_path',
        'share_image_paths',
        'share_message',
        'issued_by_user_id',
        'notes',
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
        'issued_at' => 'datetime',
        'issue_date' => 'date',
        'valid_until' => 'datetime',
        'revoked_at' => 'datetime',
        'share_image_paths' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $certificate) {
            if ($certificate->user_id && !$certificate->issued_to_user_id) {
                $certificate->issued_to_user_id = $certificate->user_id;
            }

            if ($certificate->issued_to_user_id && !$certificate->user_id) {
                $certificate->user_id = $certificate->issued_to_user_id;
            }

            if ($certificate->recipient_name && !$certificate->issued_to_name) {
                $certificate->issued_to_name = $certificate->recipient_name;
            }

            if ($certificate->issued_to_name && !$certificate->recipient_name) {
                $certificate->recipient_name = $certificate->issued_to_name;
            }

            if ($certificate->issued_at && !$certificate->issue_date) {
                $certificate->issue_date = $certificate->issued_at;
            }

            if ($certificate->issue_date && !$certificate->issued_at) {
                $certificate->issued_at = $certificate->issue_date;
            }

            if ($certificate->certificate_path && !$certificate->pdf_path) {
                $certificate->pdf_path = $certificate->certificate_path;
            }

            if ($certificate->pdf_path && !$certificate->certificate_path) {
                $certificate->certificate_path = $certificate->pdf_path;
            }
        });
    }

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
        return $this->hasMany(CertificateLog::class, 'certificate_id');
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
        if ($this->user) {
            return $this->user->full_name ?? $this->user->name;
        }

        if ($this->issuedToUser) {
            return $this->issuedToUser->full_name ?? $this->issuedToUser->name;
        }

        return $this->recipient_name ?? $this->issued_to_name ?? 'Unknown recipient';
    }

    protected function filePath(): Attribute
    {
        return Attribute::get(fn () => $this->certificate_path ?? $this->pdf_path);
    }

    protected function imagePath(): Attribute
    {
        return Attribute::get(fn () => $this->png_path);
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
