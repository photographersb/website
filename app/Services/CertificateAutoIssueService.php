<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Event;
use App\Models\EventAttendanceLog;
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
        $template = $event->certificateTemplate ?: CertificateTemplate::where('type', 'event')->where('is_default', true)->first();
        
        if (!$template) {
            return ['success' => false, 'message' => 'No certificate template found'];
        }

        // Get all attended registrations without certificates
        $attendances = EventAttendanceLog::where('event_id', $event->id)
            ->with(['registration', 'user'])
            ->whereDoesntHave('registration.user.certificates', function ($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->get();

        $issued = 0;
        $errors = [];

        foreach ($attendances as $attendance) {
            try {
                $certificate = Certificate::create([
                    'template_id' => $template->id,
                    'event_id' => $event->id,
                    'issued_to_user_id' => $attendance->user_id,
                    'certificate_code' => self::generateCertificateCode($event->id),
                    'issued_at' => now(),
                    'title' => "Certificate of Attendance - {$event->title}",
                    'description' => "This is to certify that {$attendance->user->name} successfully attended {$event->title} held on {$event->start_datetime->format('M d, Y')}.",
                    'issued_by' => $event->organizer?->name ?? 'Photographer SB',
                    'status' => 'active',
                ]);

                $issued++;
            } catch (\Exception $e) {
                $errors[] = [
                    'user_id' => $attendance->user_id,
                    'error' => $e->getMessage(),
                ];
                \Log::error('Certificate auto-issue failed', [
                    'event_id' => $event->id,
                    'user_id' => $attendance->user_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => true,
            'issued' => $issued,
            'total_attendees' => $attendances->count(),
            'errors' => $errors,
        ];
    }

    /**
     * Auto-issue certificate for single attendee
     */
    public static function issueForAttendee(EventAttendanceLog $attendance)
    {
        $event = $attendance->event;

        // Check if event has certificates enabled
        if (!$event->certificates_enabled) {
            return ['success' => false, 'message' => 'Certificates not enabled'];
        }

        // Check if already issued
        $existing = Certificate::where('event_id', $event->id)
            ->where('issued_to_user_id', $attendance->user_id)
            ->first();

        if ($existing) {
            return ['success' => false, 'message' => 'Certificate already issued', 'certificate' => $existing];
        }

        // Get template
        $template = $event->certificateTemplate ?: CertificateTemplate::where('type', 'event')->where('is_default', true)->first();
        
        if (!$template) {
            return ['success' => false, 'message' => 'No certificate template found'];
        }

        try {
            $certificate = Certificate::create([
                'template_id' => $template->id,
                'event_id' => $event->id,
                'issued_to_user_id' => $attendance->user_id,
                'certificate_code' => self::generateCertificateCode($event->id),
                'issued_at' => now(),
                'title' => "Certificate of Attendance - {$event->title}",
                'description' => "This is to certify that {$attendance->user->name} successfully attended {$event->title} held on {$event->start_datetime->format('M d, Y')}.",
                'issued_by' => $event->organizer?->name ?? 'Photographer SB',
                'status' => 'active',
            ]);

            return ['success' => true, 'certificate' => $certificate];
        } catch (\Exception $e) {
            \Log::error('Certificate auto-issue failed for attendee', [
                'attendance_id' => $attendance->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
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
