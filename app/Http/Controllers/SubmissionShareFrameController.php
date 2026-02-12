<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Services\ShareFrameGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionShareFrameController extends Controller
{
    public function __construct(
        protected ShareFrameGenerator $shareFrameGenerator
    ) {}

    /**
     * Show the share frame generation page for a submission.
     */
    public function show(Competition $competition, CompetitionSubmission $submission)
    {
        // Ensure submission belongs to competition
        abort_if($submission->competition_id !== $competition->id, 404);

        $template = $competition->activeShareFrameTemplate;
        
        if (!$template) {
            return redirect()
                ->route('competitions.submissions.show', [$competition, $submission])
                ->with('error', 'Share frames are not available for this competition.');
        }

        // Load existing share frame if available
        $shareFrame = $submission->shareFrame;

        return inertia('Competitions/ShareFrame', [
            'competition' => $competition,
            'submission' => $submission->load('user'),
            'shareFrame' => $shareFrame,
            'voteUrl' => $shareFrame ? route('vote.redirect', $submission->short_url) : null,
        ]);
    }

    /**
     * Generate share frames for a submission.
     */
    public function generate(Competition $competition, CompetitionSubmission $submission)
    {
        // Ensure submission belongs to competition
        abort_if($submission->competition_id !== $competition->id, 404);

        $template = $competition->activeShareFrameTemplate;
        
        if (!$template) {
            return back()->with('error', 'Share frames are not available for this competition.');
        }

        try {
            // Generate all frame formats
            $shareFrame = $this->shareFrameGenerator->generateAllFormats($submission, $template);

            return redirect()
                ->route('competitions.submissions.share-frame.show', [$competition, $submission])
                ->with('success', 'Share frames generated successfully!');
        } catch (\Exception $e) {
            \Log::error('Share frame generation failed', [
                'submission_id' => $submission->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to generate share frames. Please try again.');
        }
    }

    /**
     * Download a specific frame format.
     */
    public function download(Competition $competition, CompetitionSubmission $submission, string $format)
    {
        // Ensure submission belongs to competition
        abort_if($submission->competition_id !== $competition->id, 404);

        // Validate format
        $validFormats = ['story', 'post', 'portrait', 'landscape'];
        abort_if(!in_array($format, $validFormats), 404);

        $shareFrame = $submission->shareFrame;
        
        if (!$shareFrame || !$shareFrame->hasFrames()) {
            return back()->with('error', 'No share frames available. Please generate them first.');
        }

        $pathKey = $format . '_frame_path';
        $framePath = $shareFrame->$pathKey;

        if (!$framePath || !Storage::disk('public')->exists($framePath)) {
            return back()->with('error', 'Frame not found.');
        }

        $filename = sprintf(
            '%s_%s_%s.jpg',
            str_replace(' ', '_', $competition->name),
            $submission->photographer_name,
            $format
        );

        return Storage::disk('public')->download($framePath, $filename);
    }

    /**
     * Redirect short URL to voting page.
     */
    public function voteRedirect(string $shortUrl)
    {
        $submission = CompetitionSubmission::with('competition')
            ->where('short_url', $shortUrl)
            ->firstOrFail();

        $voteUrl = route('competitions.submissions.show', [
            $submission->competition_id,
            $submission->id,
        ]);

        $imageUrl = $this->resolveSubmissionImage($submission);

        return response()->view('vote-share', [
            'submission' => $submission,
            'competition' => $submission->competition,
            'voteUrl' => $voteUrl,
            'imageUrl' => $imageUrl,
        ]);
    }

    private function resolveSubmissionImage(CompetitionSubmission $submission): string
    {
        $image = $submission->image_url ?: $submission->thumbnail_url;

        if (!$image && $submission->image_path) {
            $image = Storage::disk('public')->url($submission->image_path);
        }

        if (!$image) {
            return asset('images/PhotographerSB-OG.jpg');
        }

        if (str_starts_with($image, 'http')) {
            return $image;
        }

        if (str_starts_with($image, '/')) {
            return url($image);
        }

        return Storage::disk('public')->url($image);
    }

    /**
     * Regenerate share frames (if user wants to update after template changes).
     */
    public function regenerate(Competition $competition, CompetitionSubmission $submission)
    {
        // Ensure submission belongs to competition
        abort_if($submission->competition_id !== $competition->id, 404);

        $template = $competition->activeShareFrameTemplate;
        
        if (!$template) {
            return back()->with('error', 'Share frames are not available for this competition.');
        }

        try {
            // Delete old frames
            $shareFrame = $submission->shareFrame;
            if ($shareFrame) {
                $this->deleteOldFrames($shareFrame);
            }

            // Generate new frames
            $shareFrame = $this->shareFrameGenerator->generateAllFormats($submission, $template);

            return redirect()
                ->route('competitions.submissions.share-frame.show', [$competition, $submission])
                ->with('success', 'Share frames regenerated successfully!');
        } catch (\Exception $e) {
            \Log::error('Share frame regeneration failed', [
                'submission_id' => $submission->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to regenerate share frames. Please try again.');
        }
    }

    /**
     * Delete old frame files from storage.
     */
    protected function deleteOldFrames($shareFrame)
    {
        $formats = ['story', 'post', 'portrait', 'landscape'];
        
        foreach ($formats as $format) {
            $pathKey = $format . '_frame_path';
            if ($shareFrame->$pathKey) {
                Storage::disk('public')->delete($shareFrame->$pathKey);
            }
        }

        if ($shareFrame->qr_code_path) {
            Storage::disk('public')->delete($shareFrame->qr_code_path);
        }
    }
}
