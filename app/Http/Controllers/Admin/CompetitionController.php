<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Services\WinnerCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
// use Inertia\Inertia; // Not using Inertia - using API approach with Vue Router

class CompetitionController extends Controller
{
    /**
     * Display a listing of competitions
     */
    public function index(Request $request)
    {
        $query = Competition::query()
            ->withCount(['submissions', 'votes']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('theme', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date filter
        if ($request->filled('date_filter')) {
            $today = now();
            switch ($request->date_filter) {
                case 'upcoming':
                    $query->where('submission_start_date', '>', $today);
                    break;
                case 'active':
                    $query->where('submission_start_date', '<=', $today)
                          ->where('announcement_date', '>=', $today);
                    break;
                case 'completed':
                    $query->where('announcement_date', '<', $today);
                    break;
            }
        }

        $competitions = $query->latest()
            ->paginate(15)
            ->withQueryString();

        // Note: Returning JSON instead of Inertia since we're using Vue Router + API
        return response()->json([
            'message' => 'This endpoint is deprecated. Please use /api/v1/admin/competitions',
            'redirect' => '/api/v1/admin/competitions'
        ]);
    }

    /**
     * Show the form for creating a new competition
     */
    public function create()
    {
        return response()->json(['message' => 'Use API endpoint']);
    }

    /**
     * Store a newly created competition
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:competitions,slug',
            'category_id' => 'nullable|exists:categories,id',
            'theme' => 'required|string',
            'description' => 'nullable|string',
            'submission_start_date' => 'required|date',
            'submission_end_date' => 'required|date|after:submission_start_date',
            'voting_start_date' => 'required|date|after_or_equal:submission_end_date',
            'voting_end_date' => 'required|date|after:voting_start_date',
            'announcement_date' => 'required|date|after:voting_end_date',
            'prize_pool' => 'nullable|numeric|min:0',
            'max_submissions_per_user' => 'nullable|integer|min:1|max:10',
            'rules' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'status' => 'required|in:draft,published,closed',
            'featured' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Competition::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $competition = Competition::create($validated);

        return redirect()
            ->route('admin.competitions.index')
            ->with('success', 'Competition created successfully!');
    }

    public function show(Competition $competition)
    {
        return response()->json(['message' => 'Use API endpoint']);
    }

    /**
     * Show the form for editing the competition
     */
    public function edit(Competition $competition)
    {
        return response()->json(['message' => 'Use API endpoint']);
    }

    /**
     * Update the specified competition
     */
    public function update(Request $request, Competition $competition)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:competitions,slug,' . $competition->id,
            'theme' => 'required|string',
            'description' => 'nullable|string',
            'submission_start_date' => 'required|date',
            'submission_end_date' => 'required|date|after:submission_start_date',
            'voting_start_date' => 'required|date|after_or_equal:submission_end_date',
            'voting_end_date' => 'required|date|after:voting_start_date',
            'announcement_date' => 'required|date|after:voting_end_date',
            'prize_pool' => 'nullable|numeric|min:0',
            'max_submissions_per_user' => 'nullable|integer|min:1|max:10',
            'rules' => 'nullable|string',
            'terms' => 'nullable|string',
            'status' => 'required|in:draft,published,closed',
            'featured' => 'boolean',
        ]);

        // Update slug if title changed
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure uniqueness (excluding current record)
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Competition::where('slug', $validated['slug'])
                              ->where('id', '!=', $competition->id)
                              ->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $competition->update($validated);

        return redirect()
            ->route('admin.competitions.index')
            ->with('success', 'Competition updated successfully!');
    }

    /**
     * Remove the specified competition
     */
    public function destroy(Competition $competition)
    {
        // Check if competition has submissions
        if ($competition->submissions()->exists()) {
            return back()->with('error', 'Cannot delete competition with existing submissions.');
        }

        $competition->delete();

        return redirect()
            ->route('admin.competitions.index')
            ->with('success', 'Competition deleted successfully!');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Competition $competition)
    {
        $competition->update([
            'featured' => !$competition->featured,
        ]);

        return back()->with('success', 'Featured status updated!');
    }

    /**
     * Publish competition
     */
    public function publish(Competition $competition)
    {
        $competition->update(['status' => 'published']);

        return back()->with('success', 'Competition published successfully!');
    }

    /**
     * Close competition
     */
    public function close(Competition $competition)
    {
        $competition->update(['status' => 'closed']);

        return back()->with('success', 'Competition closed successfully!');
    }

    /**
     * View competition submissions
     */
    public function submissions(Competition $competition)
    {
        return response()->json(['message' => 'Use API endpoint']);
    }

    /**
     * Approve submission
     */
    public function approveSubmission(CompetitionSubmission $submission)
    {
        $submission->update(['status' => 'approved']);

        return back()->with('success', 'Submission approved!');
    }

    /**
     * Reject submission
     */
    public function rejectSubmission(CompetitionSubmission $submission)
    {
        $submission->update(['status' => 'rejected']);

        return back()->with('success', 'Submission rejected!');
    }

    /**
     * Disqualify submission
     */
    public function disqualifySubmission(CompetitionSubmission $submission)
    {
        $submission->update(['status' => 'disqualified']);

        return back()->with('success', 'Submission disqualified!');
    }

    /**
     * Announce winner
     */
    public function announceWinner(Request $request, Competition $competition)
    {
        $validated = $request->validate([
            'winner_submission_id' => 'required|exists:competition_submissions,id',
        ]);

        $competition->update([
            'winner_submission_id' => $validated['winner_submission_id'],
            'status' => 'closed',
        ]);

        // You can add email notification to winner here
        // Notification::send($winnerUser, new CompetitionWinnerNotification($competition));

        return back()->with('success', 'Winner announced successfully!');
    }

    /**
     * Calculate winners for a competition
     */
    public function calculateWinners(Request $request, Competition $competition)
    {
        $validated = $request->validate([
            'vote_weight' => 'nullable|numeric|min:0|max:1',
            'judge_weight' => 'nullable|numeric|min:0|max:1',
            'number_of_winners' => 'nullable|integer|min:1|max:10',
            'honorable_mentions' => 'nullable|integer|min:0|max:20',
        ]);

        $winnerService = new WinnerCalculationService();
        $result = $winnerService->calculateWinners($competition, $validated);

        return response()->json($result);
    }

    /**
     * Announce winners (save to database)
     */
    public function announceWinners(Request $request, Competition $competition)
    {
        $validated = $request->validate([
            'vote_weight' => 'nullable|numeric|min:0|max:1',
            'judge_weight' => 'nullable|numeric|min:0|max:1',
            'number_of_winners' => 'nullable|integer|min:1|max:10',
            'honorable_mentions' => 'nullable|integer|min:0|max:20',
        ]);

        $winnerService = new WinnerCalculationService();
        $result = $winnerService->announceWinners($competition, $validated);

        return response()->json($result);
    }

    /**
     * Get winners for a competition
     */
    public function getWinners(Competition $competition)
    {
        $winnerService = new WinnerCalculationService();
        $result = $winnerService->getWinners($competition);

        return response()->json($result);
    }

    /**
     * Get leaderboard for a competition
     */
    public function getLeaderboard(Request $request, Competition $competition)
    {
        $limit = $request->input('limit', 20);
        
        $winnerService = new WinnerCalculationService();
        $leaderboard = $winnerService->getLeaderboard($competition, $limit);

        return response()->json([
            'leaderboard' => $leaderboard
        ]);
    }

    /**
     * Issue certificate for a competition submission (for manual issuance)
     */
    public function issueCertificate(Request $request, Competition $competition)
    {
        try {
            $validated = $request->validate([
                'submission_id' => 'required|exists:competition_submissions,id',
                'certificate_type' => 'required|in:participation,winner,finalist,merit',
                'position' => 'nullable|in:1st,2nd,3rd',
                'issue_date' => 'required|date',
                'admin_notes' => 'nullable|string|max:500',
                'send_email' => 'boolean',
            ]);

            // Get the submission
            $submission = CompetitionSubmission::findOrFail($validated['submission_id']);

            if ($submission->competition_id !== $competition->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Submission does not belong to this competition'
                ], 400);
            }

            // Load photographer and user relationship
            $submission->load(['photographer', 'photographer.user']);
            
            if (!$submission->photographer || !$submission->photographer->user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Photographer information not found'
                ], 400);
            }

            // Use CertificateIssuanceService to issue the certificate
            $issuanceService = app(\App\Services\CertificateIssuanceService::class);
            
            // Get or create certificate template
            $template = \App\Models\CertificateTemplate::where('type', $validated['certificate_type'])
                ->where('is_default', true)
                ->first();

            if (!$template) {
                // Create a basic template if none exists
                $template = \App\Models\CertificateTemplate::create([
                    'title' => ucfirst(str_replace('_', ' ', $validated['certificate_type'])) . ' Certificate',
                    'type' => $validated['certificate_type'],
                    'description' => 'Auto-generated certificate template',
                    'width' => 1000,
                    'height' => 700,
                    'background_color' => '#ffffff',
                    'accent_color' => '#8e0e3f',
                    'text_color' => '#000000',
                ]);
            }

            // Issue the certificate
            $certificate = $issuanceService->issueCertificate(
                template: $template,
                event: null, // No event for competition certificates
                user: $submission->photographer->user,
                participantName: $submission->photographer->user->name,
                participantEmail: $submission->photographer->user->email,
                autoGenerate: false
            );

            // Update submission with certificate reference
            $updateData = [
                'certificate_id' => $certificate->id,
            ];
            
            // Only update fields that exist in the model and are mass-assignable
            try {
                if (in_array('certificate_issued_at', $submission->getFillable())) {
                    $updateData['certificate_issued_at'] = $validated['issue_date'];
                }
                if (in_array('awarded_type', $submission->getFillable())) {
                    $updateData['awarded_type'] = $validated['certificate_type'];
                }
                if (in_array('award_position', $submission->getFillable())) {
                    $updateData['award_position'] = $validated['position'];
                }
            } catch (\Exception $e) {
                \Log::debug('Could not check fillable attributes', ['error' => $e->getMessage()]);
            }
            
            $submission->update($updateData);

            // Send email if requested
            if ($validated['send_email']) {
                try {
                    // Try to send email if Mail notification is configured
                    \Notification::send($submission->photographer->user, new \App\Notifications\CertificateIssuedNotification($certificate, $submission));
                } catch (\Exception $e) {
                    \Log::warning('Failed to send certificate notification', [
                        'certificate_id' => $certificate->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Certificate issued successfully',
                'data' => [
                    'certificate_id' => $certificate->id,
                    'certificate_code' => $certificate->certificate_code,
                    'submission_id' => $submission->id,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Certificate issuance error', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to issue certificate: ' . $e->getMessage()
            ], 500);
        }
    }
}
