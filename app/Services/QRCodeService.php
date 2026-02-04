<?php

namespace App\Services;

use App\Models\CompetitionSubmission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    /**
     * Generate QR code for a submission
     */
    public function generateForSubmission(CompetitionSubmission $submission): string
    {
        // Generate short URL if not exists
        if (!$submission->short_url) {
            $submission->short_url = $this->generateShortUrl($submission);
            $submission->save();
        }

        // Generate vote URL
        $voteUrl = $this->getVoteUrl($submission);

        // Generate QR code
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($voteUrl);

        // Save QR code
        $path = "qr-codes/submission_{$submission->id}_{$submission->share_token}.png";
        Storage::put($path, $qrCode);

        return $path;
    }

    /**
     * Generate short URL for submission
     */
    protected function generateShortUrl(CompetitionSubmission $submission): string
    {
        // Generate unique short code
        do {
            $shortCode = Str::random(8);
            $exists = CompetitionSubmission::where('short_url', $shortCode)->exists();
        } while ($exists);

        // Also generate share token for security
        if (!$submission->share_token) {
            $submission->share_token = Str::random(32);
        }

        return $shortCode;
    }

    /**
     * Get vote URL for submission
     */
    public function getVoteUrl(CompetitionSubmission $submission): string
    {
        // Use short URL if available
        if ($submission->short_url) {
            return route('submission.vote.short', ['code' => $submission->short_url]);
        }

        // Fallback to competition page with submission parameter
        return route('competition.show', [
            'competition' => $submission->competition->slug,
            'submission' => $submission->id
        ]);
    }

    /**
     * Get full vote URL with domain
     */
    public function getFullVoteUrl(CompetitionSubmission $submission): string
    {
        return config('app.url') . $this->getVoteUrl($submission);
    }
}
