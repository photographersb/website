<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\User;
use App\Jobs\GenerateCertificateAssetsJob;
use App\Services\CertificateIssuanceService;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class IssueCertificateForAttendanceJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Event $event,
        protected User $user,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(CertificateIssuanceService $issuanceService): void
    {
        // Get the default/active template for this event
        $template = $this->event->certificateTemplate ?: $this->event->competition?->activeShareFrameTemplate;

        if (!$template) {
            Log::warning('No certificate template found for event', [
                'event_id' => $this->event->id,
                'user_id' => $this->user->id,
            ]);
            return;
        }

        try {
            $certificate = $issuanceService->issueCertificate(
                $template,
                $this->event,
                null,
                $this->user,
                null,
                null,
                now()->toDateTimeString(),
                'Automatically issued by attendance job',
                null,
                null,
                false,
                true
            );

            GenerateCertificateAssetsJob::dispatch($certificate->id)->onQueue('certificates');

            Log::info('Certificate auto-issued for attendance', [
                'event_id' => $this->event->id,
                'user_id' => $this->user->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to auto-issue certificate', [
                'event_id' => $this->event->id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
