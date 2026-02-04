<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\CertificateTemplate;
use Illuminate\Support\Str;

class CertificateAutoIssueService
{
    /**
     * Auto-issue certificates for event attendees
     */
    public static function issueForEvent(Event $event)
    {
        // Check if event has certificates enabled
        if (!$event->certificates_enabled) {
            return ['success' => false, 'message' => 'Certificates not enabled for this event'];
        }

        // Get certificate template
        $template = $event->certificateTemplate ?: CertificateTemplate::where('type', 'participation')->where('is_default', true)->first();
        
        if (!$template) {
            return ['success' => false, 'message' => 'No certificate template found'];
        }

        // Get all attended registrations without certificates
        $attendedRegistrations = EventRegistration::where('event_id', $event->id)
            ->whereNotNull('attended_at')
            ->with('user')
            ->whereDoesntHave('user.certificates', function ($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->get();

        $issued = 0;
        $errors = [];

        foreach ($attendedRegistrations as $registration) {
            try {
                $certificate = Certificate::create([
                    'certificate_code' => self::generateCertificateCode($event->id),
                    'template_id' => $template->id,
                    'event_id' => $event->id,
                    'issued_to_user_id' => $registration->user_id,
                    'issued_to_name' => $registration->user->name,
                    'issued_to_email' => $registration->user->email,
                    'issue_date' => now(),
                    'status' => 'issued',
                    'created_by_user_id' => 1, // System
                ]);

                $issued++;
            } catch (\Exception $e) {
                $errors[] = [
                    'user_id' => $registration->user_id,
                    'error' => $e->getMessage(),
                ];
                \Log::error('Certificate auto-issue failed', [
                    'event_id' => $event->id,
                    'user_id' => $registration->user_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => true,
            'issued' => $issued,
            'total_attendees' => $attendedRegistrations->count(),
            'errors' => $errors,
        ];
    }

    /**
     * Auto-issue certificate for single attendee (EventRegistration)
     */
    public static function issueForAttendee($registration)
    {
        // Support both EventRegistration model and stdClass for testing
        if (is_object($registration) && !($registration instanceof EventRegistration)) {
            // Convert stdClass to compatible object for testing
            $event = $registration->event;
            $userId = $registration->user_id;
            $userName = $registration->user->name ?? 'Unknown';
            $userEmail = $registration->user->email ?? null;
        } else {
            $event = $registration->event;
            $userId = $registration->user_id;
            $userName = $registration->user->name;
            $userEmail = $registration->user->email;
        }

        // Check if event has certificates enabled
        if (!$event->certificates_enabled) {
            return ['success' => false, 'message' => 'Certificates not enabled'];
        }

        // Check if already issued
        $existing = Certificate::where('event_id', $event->id)
            ->where('issued_to_user_id', $userId)
            ->first();

        if ($existing) {
            return ['success' => false, 'message' => 'Certificate already issued', 'certificate' => $existing];
        }

        // Get template
        $template = $event->certificateTemplate ?: CertificateTemplate::where('type', 'participation')->where('is_default', true)->first();
        
        if (!$template) {
            return ['success' => false, 'message' => 'No certificate template found'];
        }

        try {
            $certificate = Certificate::create([
                'certificate_code' => self::generateCertificateCode($event->id),
                'template_id' => $template->id,
                'event_id' => $event->id,
                'issued_to_user_id' => $userId,
                'issued_to_name' => $userName,
                'issued_to_email' => $userEmail,
                'issue_date' => now(),
                'status' => 'issued',
                'created_by_user_id' => 1, // System
            ]);

            return ['success' => true, 'certificate' => $certificate];
        } catch (\Exception $e) {
            \Log::error('Certificate auto-issue failed for attendee', [
                'event_id' => $event->id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Generate unique certificate code
     */
    private static function generateCertificateCode($eventId)
    {
        $prefix = 'CERT-' . strtoupper(substr(md5($eventId), 0, 4));
        $unique = strtoupper(Str::random(8));
        $code = "{$prefix}-{$unique}";

        // Ensure uniqueness
        while (Certificate::where('certificate_code', $code)->exists()) {
            $unique = strtoupper(Str::random(8));
            $code = "{$prefix}-{$unique}";
        }

        return $code;
    }

    /**
     * Schedule auto-issue for event (can be called via job/cron)
     */
    public static function scheduleAutoIssue(Event $event)
    {
        // Check if event has ended
        if ($event->end_datetime && now() > $event->end_datetime) {
            return self::issueForEvent($event);
        }

        return ['success' => false, 'message' => 'Event has not ended yet'];
    }
}
