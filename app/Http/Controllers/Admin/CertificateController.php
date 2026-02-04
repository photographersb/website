<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Event;
use App\Models\User;
use App\Services\CertificateIssuanceService;
use App\Services\CertificateGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function __construct(
        protected CertificateIssuanceService $issuanceService,
        protected CertificateGenerator $generator
    ) {}

    /**
     * Show all certificates.
     */
    public function index()
    {
        $certificates = Certificate::with(['template', 'event', 'issuedToUser'])
            ->latest()
            ->paginate(20);

        return inertia('Admin/Certificates/Index', [
            'certificates' => $certificates,
        ]);
    }

    /**
     * Show form for manual certificate issuance.
     */
    public function create()
    {
        return inertia('Admin/Certificates/Create', [
            'templates' => CertificateTemplate::active()->get(),
            'events' => Event::where('status', 'active')->get(),
        ]);
    }

    /**
     * Store manually issued certificates.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:certificate_templates,id',
            'event_id' => 'nullable|exists:events,id',
            'participants' => 'required|array|min:1',
            'participants.*.name' => 'required|string|max:255',
            'participants.*.email' => 'nullable|email',
            'participants.*.user_id' => 'nullable|exists:users,id',
            'auto_generate' => 'boolean',
        ]);

        $template = CertificateTemplate::findOrFail($validated['template_id']);
        $event = $validated['event_id'] ? Event::findOrFail($validated['event_id']) : null;
        $autoGenerate = $validated['auto_generate'] ?? false;

        $participants = [];
        foreach ($validated['participants'] as $participant) {
            $user = $participant['user_id'] ? User::find($participant['user_id']) : null;
            $participants[] = [
                'user' => $user,
                'name' => $participant['name'],
                'email' => $participant['email'] ?? null,
            ];
        }

        $certificates = $this->issuanceService->issueCertificateBulk(
            $template,
            $event,
            $participants,
            $autoGenerate
        );

        return back()->with('success', 'Certificates issued successfully! ' . $certificates->count() . ' certificate(s) created.');
    }

    /**
     * Show a single certificate.
     */
    public function show(Certificate $certificate)
    {
        $certificate->load(['template', 'event', 'issuedToUser', 'logs']);

        return inertia('Admin/Certificates/Show', [
            'certificate' => $certificate,
        ]);
    }

    /**
     * Auto-issue certificates for event attendees.
     */
    public function autoIssueForEvent(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'template_id' => 'required|exists:certificate_templates,id',
        ]);

        $event = Event::findOrFail($validated['event_id']);
        $template = CertificateTemplate::findOrFail($validated['template_id']);

        $certificates = $this->issuanceService->autoIssueCertificatesForEvent($event, $template);

        return back()->with('success', 'Certificates issued successfully! ' . $certificates->count() . ' certificate(s) auto-issued for attendees.');
    }

    /**
     * Generate PDF and QR for a certificate.
     */
    public function generate(Certificate $certificate)
    {
        try {
            $this->generator->generateCertificate($certificate);
            return back()->with('success', 'Certificate generated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }

    /**
     * Download certificate PDF.
     */
    public function download(Certificate $certificate)
    {
        if (!$certificate->pdf_path || !Storage::disk('public')->exists($certificate->pdf_path)) {
            return back()->with('error', 'Certificate PDF not found');
        }

        $certificate->logs()->create([
            'action' => 'downloaded',
            'performed_by_user_id' => auth()->id(),
        ]);

        return Storage::disk('public')->download($certificate->pdf_path, "{$certificate->certificate_code}.pdf");
    }

    /**
     * Revoke a certificate.
     */
    public function revoke(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $this->issuanceService->revokeCertificate($certificate, $validated['reason'] ?? null);

        return back()->with('success', 'Certificate revoked successfully!');
    }

    /**
     * Reissue a revoked certificate.
     */
    public function reissue(Certificate $certificate)
    {
        try {
            $this->issuanceService->reissueCertificate($certificate);
            return back()->with('success', 'Certificate reissued successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * List certificates by event.
     */
    public function byEvent(Event $event)
    {
        $certificates = $event->certificates()
            ->with(['template', 'issuedToUser'])
            ->paginate(20);

        return inertia('Admin/Events/Certificates', [
            'event' => $event,
            'certificates' => $certificates,
        ]);
    }

    /**
     * Bulk regenerate all certificates for event.
     */
    public function regenerateBulk(Request $request, Event $event)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:certificate_templates,id',
        ]);

        $template = CertificateTemplate::findOrFail($validated['template_id']);

        $count = 0;
        foreach ($event->certificates as $certificate) {
            try {
                $this->generator->generateCertificate($certificate);
                $count++;
            } catch (\Exception $e) {
                \Log::error('Bulk regeneration failed for certificate', [
                    'certificate_id' => $certificate->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return back()->with('success', "Successfully regenerated $count certificate(s)!");
    }
}
