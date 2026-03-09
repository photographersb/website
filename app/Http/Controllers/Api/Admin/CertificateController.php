<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Competition;
use App\Models\Event;
use App\Models\User;
use App\Services\CertificateGenerator;
use App\Services\CertificateIssuanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    protected array $issuerRoles = ['admin', 'super_admin'];

    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 20);
        $search = trim((string) $request->get('search', ''));
        $status = $request->get('status');
        $type = $request->get('type');

        $certificates = Certificate::with(['event', 'competition', 'template', 'user', 'issuedToUser'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($type, fn ($query) => $query->whereHas('template', fn ($templateQuery) => $templateQuery->where('type', $type)))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('certificate_code', 'like', "%{$search}%")
                        ->orWhere('recipient_name', 'like', "%{$search}%")
                        ->orWhere('issued_to_name', 'like', "%{$search}%")
                        ->orWhereHas('event', fn ($eventQuery) => $eventQuery->where('title', 'like', "%{$search}%"))
                        ->orWhereHas('competition', fn ($competitionQuery) => $competitionQuery->where('title', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate($perPage);

        $mapped = $certificates->getCollection()->map(function (Certificate $certificate) {
            $filePath = $certificate->file_path;
            $hasFile = !empty($filePath);

            return [
                'id' => $certificate->id,
                'certificate_code' => $certificate->certificate_code,
                'recipient_name' => $certificate->getParticipantName(),
                'recipient_email' => $certificate->issued_to_email,
                'event_title' => $certificate->event?->title,
                'competition_title' => $certificate->competition?->title ?? $certificate->competition?->name,
                'template_type' => $certificate->template?->type,
                'template_title' => $certificate->template?->title,
                'status' => $certificate->status,
                'issued_at' => $certificate->issued_at ?? $certificate->issue_date,
                'verification_url' => route('certificate.verify', $certificate->certificate_code),
                'certificate_url' => $hasFile ? Storage::url($filePath) : null,
                'has_file' => $hasFile,
                'source_title' => $certificate->event?->title
                    ?? $certificate->competition?->title
                    ?? $certificate->competition?->name
                    ?? '—',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $mapped,
            'meta' => [
                'total' => $certificates->total(),
                'per_page' => $certificates->perPage(),
                'current_page' => $certificates->currentPage(),
                'last_page' => $certificates->lastPage(),
            ],
        ]);
    }

    public function options(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $users = User::query()
            ->select('id', 'name', 'full_name', 'email')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $events = Event::query()->select('id', 'title')->orderByDesc('id')->limit(50)->get();
        $competitions = Competition::query()->select('id', 'title')->orderByDesc('id')->limit(50)->get();
        $templates = CertificateTemplate::query()->select('id', 'title', 'type', 'is_default')->orderBy('title')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'users' => $users,
                'events' => $events,
                'competitions' => $competitions,
                'templates' => $templates,
            ],
        ]);
    }

    public function issueManually(Request $request, CertificateIssuanceService $issuanceService)
    {
        $this->ensureIssuerRole();

        $validated = $request->validate([
            'template_id' => 'required|exists:certificate_templates,id',
            'user_id' => 'nullable|exists:users,id',
            'event_id' => 'nullable|exists:events,id',
            'competition_id' => 'nullable|exists:competitions,id',
            'source_type' => 'nullable|in:event,workshop,competition,award,participation',
            'recipient_name' => 'required_without:user_id|string|max:255',
            'recipient_email' => 'nullable|email|max:255',
            'issued_at' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $template = CertificateTemplate::findOrFail($validated['template_id']);
        $user = !empty($validated['user_id']) ? User::find($validated['user_id']) : null;
        $event = !empty($validated['event_id']) ? Event::find($validated['event_id']) : null;
        $competition = !empty($validated['competition_id']) ? Competition::find($validated['competition_id']) : null;

        $sourceType = $validated['source_type']
            ?? ($competition ? 'competition' : ($event ? (($event->event_type === 'workshop' || $event->type === 'workshop') ? 'workshop' : 'event') : null));

        if (!$event && !$competition && !in_array($sourceType, ['award', 'participation'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Select event/competition or choose source type award/participation.',
            ], 422);
        }

        $certificate = $issuanceService->issueCertificate(
            template: $template,
            event: $event,
            competition: $competition,
            user: $user,
            recipientName: $validated['recipient_name'] ?? null,
            participantEmail: $validated['recipient_email'] ?? null,
            issueDate: $validated['issued_at'] ?? null,
            notes: $validated['notes'] ?? null,
            sourceType: $sourceType,
            sourceId: $competition?->id ?? $event?->id,
            generateFile: true,
            autoGenerated: false,
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Certificate issued successfully.',
            'data' => [
                'id' => $certificate->id,
                'certificate_code' => $certificate->certificate_code,
                'certificate_url' => $certificate->file_path ? Storage::url($certificate->file_path) : null,
            ],
        ], 201);
    }

    public function autoIssue(Request $request, CertificateIssuanceService $issuanceService)
    {
        $this->ensureIssuerRole();

        $validated = $request->validate([
            'source_type' => 'required|in:event,competition',
            'source_id' => 'required|integer|min:1',
            'template_id' => 'required|exists:certificate_templates,id',
        ]);

        $template = CertificateTemplate::findOrFail($validated['template_id']);

        if ($validated['source_type'] === 'event') {
            $event = Event::findOrFail($validated['source_id']);
            $certificates = $issuanceService->autoIssueCertificatesForEvent($event, $template);
        } else {
            $competition = Competition::findOrFail($validated['source_id']);
            $certificates = $issuanceService->autoIssueCertificatesForCompetition($competition, $template);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Automatic issuance completed.',
            'data' => [
                'issued_count' => $certificates->count(),
            ],
        ]);
    }

    public function regenerate(Certificate $certificate, CertificateGenerator $generator)
    {
        $this->ensureIssuerRole();

        try {
            $generator->generateCertificate($certificate);

            return response()->json([
                'status' => 'success',
                'message' => 'Certificate regenerated successfully',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to regenerate certificate: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download(Certificate $certificate)
    {
        $this->ensureIssuerRole();

        if (!$certificate->file_path || !Storage::disk('public')->exists($certificate->file_path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate file not found.',
            ], 404);
        }

        $certificate->logs()->create([
            'user_id' => Auth::id(),
            'action_type' => 'downloaded',
            'entity_type' => 'certificate',
            'entity_id' => $certificate->id,
            'message' => 'Admin downloaded certificate',
        ]);

        return response()->download(
            Storage::disk('public')->path($certificate->file_path),
            'certificate-' . $certificate->certificate_code . '.pdf'
        );
    }

    protected function ensureIssuerRole(): void
    {
        $userRole = Auth::user()?->role;

        if (!in_array($userRole, $this->issuerRoles, true)) {
            abort(403, 'Only admin users can issue certificates.');
        }
    }
}
