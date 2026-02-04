<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Collection;
use Exception;

class CertificateIssuanceService
{
    protected $generator;

    public function __construct(CertificateGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Issue certificate to a user for an event
     */
    public function issueCertificate(
        CertificateTemplate $template,
        ?Event $event = null,
        ?User $user = null,
        ?string $participantName = null,
        ?string $participantEmail = null,
        bool $autoGenerate = false
    ): Certificate {
        // Validate input
        if ($user) {
            $participantName = $user->full_name ?? $user->name;
            $participantEmail = $user->email;
        } elseif (!$participantName) {
            throw new Exception('Either user or participant name must be provided');
        }

        // Check for duplicates
        $existing = Certificate::where([
            ['template_id', '=', $template->id],
            ['event_id', '=', $event?->id],
            ['issued_to_user_id', '=', $user?->id],
            ['issued_to_name', '=', $participantName],
            ['status', '=', 'issued'],
        ])->first();

        if ($existing) {
            return $existing;
        }

        // Create certificate
        $certificate = Certificate::create([
            'certificate_code' => $this->generator->generateCode(),
            'template_id' => $template->id,
            'event_id' => $event?->id,
            'issued_to_user_id' => $user?->id,
            'issued_to_name' => $participantName,
            'issued_to_email' => $participantEmail,
            'issue_date' => now()->toDateString(),
            'created_by_user_id' => auth()?->id() ?? 1, // Default to system user if not authenticated
            'status' => 'issued',
        ]);

        // Log issuance
        $action = $autoGenerate ? 'auto_issued' : 'manual_issued';
        $certificate->logs()->create([
            'action' => $action,
            'performed_by_user_id' => auth()?->id(),
            'meta' => [
                'template_name' => $template->name,
                'event_title' => $event?->title,
            ],
        ]);

        // Generate PDF and QR if enabled
        if ($autoGenerate) {
            try {
                $this->generator->generateCertificate($certificate);
            } catch (Exception $e) {
                \Log::warning('Certificate generation failed but issuance succeeded', [
                    'certificate_id' => $certificate->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $certificate;
    }

    /**
     * Issue certificates to multiple participants
     */
    public function issueCertificateBulk(
        CertificateTemplate $template,
        Event $event,
        array $participants,
        bool $autoGenerate = false
    ): Collection {
        $certificates = collect();

        foreach ($participants as $participant) {
            try {
                $cert = $this->issueCertificate(
                    $template,
                    $event,
                    $participant['user'] ?? null,
                    $participant['name'] ?? null,
                    $participant['email'] ?? null,
                    $autoGenerate
                );
                $certificates->push($cert);
            } catch (Exception $e) {
                \Log::error('Failed to issue certificate', [
                    'participant' => $participant,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $certificates;
    }

    /**
     * Auto-issue certificates for event attendees
     */
    public function autoIssueCertificatesForEvent(Event $event, CertificateTemplate $template): Collection
    {
        // Get active attendees from event_attendances
        $attendees = $event->attendances()
            ->where('is_present', true)
            ->with('user')
            ->get();

        $participants = $attendees->map(function ($attendance) {
            return [
                'user' => $attendance->user,
                'name' => $attendance->user->full_name ?? $attendance->user->name,
                'email' => $attendance->user->email,
            ];
        })->toArray();

        return $this->issueCertificateBulk($template, $event, $participants, autoGenerate: true);
    }

    /**
     * Revoke a certificate
     */
    public function revokeCertificate(Certificate $certificate, ?string $reason = null): Certificate
    {
        $certificate->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoked_by_user_id' => auth()?->id(),
        ]);

        $certificate->logs()->create([
            'action' => 'revoked',
            'performed_by_user_id' => auth()?->id(),
            'meta' => ['reason' => $reason],
        ]);

        return $certificate;
    }

    /**
     * Reissue a revoked certificate
     */
    public function reissueCertificate(Certificate $certificate): Certificate
    {
        if (!$certificate->isRevoked()) {
            throw new Exception('Cannot reissue a certificate that is not revoked');
        }

        $certificate->update([
            'status' => 'issued',
            'revoked_at' => null,
            'revoked_by_user_id' => null,
        ]);

        $certificate->logs()->create([
            'action' => 'reissued',
            'performed_by_user_id' => auth()?->id(),
        ]);

        return $certificate;
    }

    /**
     * Download or email certificate
     */
    public function downloadCertificate(Certificate $certificate)
    {
        if (!$certificate->pdf_path) {
            throw new Exception('Certificate PDF not generated yet');
        }

        // Log download
        $certificate->logs()->create([
            'action' => 'downloaded',
            'performed_by_user_id' => auth()?->id(),
        ]);

        return $certificate->pdf_path;
    }
}
