<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateVerificationController extends Controller
{
    /**
     * Verify certificate public page.
     */
    public function verify(string $certificateCode)
    {
        $certificate = Certificate::with(['event', 'competition', 'template', 'user', 'issuedToUser'])
            ->where('certificate_code', $certificateCode)
            ->first();

        if (!$certificate) {
            return inertia('Certificates/Verify', [
                'certificate' => null,
                'valid' => false,
                'message' => 'Certificate not found',
            ]);
        }

        // Log verification
        $certificate->logs()->create([
            'action_type' => 'verified',
            'entity_type' => 'certificate',
            'entity_id' => $certificate->id,
            'message' => 'Public verification page viewed',
            'metadata' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        ]);

        $isValid = $certificate->isValid();

        return inertia('Certificates/Verify', [
            'certificate' => [
                'code' => $certificate->certificate_code,
                'certificateId' => $certificate->certificate_code,
                'participantName' => $certificate->getParticipantName(),
                'eventTitle' => $certificate->event?->title
                    ?? $certificate->competition?->title
                    ?? $certificate->competition?->name
                    ?? 'Completion',
                'issueDate' => ($certificate->issued_at ?? $certificate->issue_date)?->format('j F Y'),
                'expiryDate' => $certificate->valid_until?->format('j F Y'),
                'status' => $certificate->status,
                'templateName' => $certificate->template?->title,
                'platformLogo' => asset('images/logo.png'),
            ],
            'valid' => $isValid,
            'message' => $this->getVerificationMessage($certificate),
        ]);
    }

    /**
     * Get appropriate message for certificate status.
     */
    protected function getVerificationMessage(Certificate $certificate): string
    {
        if ($certificate->isRevoked()) {
            return 'This certificate has been revoked.';
        }

        if ($certificate->valid_until && $certificate->valid_until->isPast()) {
            return 'This certificate has expired.';
        }

        return 'This certificate is valid and has been verified.';
    }

    /**
     * Download QR code image.
     */
    public function downloadQR(string $certificateCode)
    {
        $certificate = Certificate::where('certificate_code', $certificateCode)->first();

        if (!$certificate || !$certificate->verification_qr_path) {
            return response()->json(['error' => 'QR code not found'], 404);
        }

        return response()->download(Storage::disk('public')->path($certificate->verification_qr_path));
    }
}
