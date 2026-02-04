<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionShareFrameTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CompetitionShareFrameTemplateController extends Controller
{
    /**
     * Show the form for creating/editing a share frame template for a competition.
     */
    public function edit(Competition $competition)
    {
        $template = $competition->activeShareFrameTemplate ?: new CompetitionShareFrameTemplate([
            'competition_id' => $competition->id,
            'background_color' => '#1a1a1a',
            'text_color' => '#ffffff',
            'accent_color' => '#3b82f6',
            'primary_font' => 'Inter',
            'secondary_font' => 'Inter',
            'cta_message' => 'Scan to vote for my photo!',
            'watermark_enabled' => true,
            'watermark_text' => 'Photographar',
            'watermark_opacity' => 30,
            'qr_code_enabled' => true,
            'qr_code_size' => 250,
            'padding' => 40,
            'fit_strategy' => 'contain',
            'is_active' => true,
        ]);

        return inertia('Admin/Competitions/ShareFrameTemplate', [
            'competition' => $competition->load('shareFrameTemplates'),
            'template' => $template,
        ]);
    }

    /**
     * Store or update a share frame template for a competition.
     */
    public function update(Request $request, Competition $competition)
    {
        $validated = $request->validate([
            'background_color' => 'required|string|max:7',
            'text_color' => 'required|string|max:7',
            'accent_color' => 'required|string|max:7',
            'primary_font' => 'required|string|max:100',
            'secondary_font' => 'required|string|max:100',
            'cta_message' => 'required|string|max:255',
            'watermark_enabled' => 'boolean',
            'watermark_text' => 'nullable|string|max:100',
            'watermark_opacity' => 'required|integer|min:0|max:100',
            'qr_code_enabled' => 'boolean',
            'qr_code_size' => 'required|integer|min:100|max:400',
            'padding' => 'required|integer|min:0|max:200',
            'fit_strategy' => ['required', Rule::in(['contain', 'cover'])],
            'background_image' => 'nullable|image|max:5120', // 5MB max
            'is_active' => 'boolean',
        ]);

        // Deactivate all other templates for this competition if this one is active
        if ($validated['is_active'] ?? true) {
            $competition->shareFrameTemplates()->update(['is_active' => false]);
        }

        // Handle background image upload
        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('share-frame-backgrounds', 'public');
            $validated['background_image'] = $path;
        }

        // Update or create template
        $template = $competition->activeShareFrameTemplate;
        
        if ($template) {
            $template->update($validated);
        } else {
            $template = $competition->shareFrameTemplates()->create($validated);
        }

        return redirect()
            ->route('admin.competitions.share-frame-template.edit', $competition)
            ->with('success', 'Share frame template saved successfully!');
    }

    /**
     * Preview the share frame template with a sample image.
     */
    public function preview(Request $request, Competition $competition)
    {
        $request->validate([
            'preview_image' => 'required|image|max:10240', // 10MB max for preview
        ]);

        $template = $competition->activeShareFrameTemplate;
        
        if (!$template) {
            return response()->json(['error' => 'No active template found'], 404);
        }

        // Use first submission from this competition for preview
        $submission = $competition->submissions()->first();
        
        if (!$submission) {
            return response()->json([
                'error' => 'No submissions available for preview. Upload a test submission first.'
            ], 404);
        }

        try {
            $generator = app(\App\Services\ShareFrameGenerator::class);
            
            // Generate all formats (or regenerate if exists)
            $shareFrame = $generator->generateAllFormats($submission, $template);

            return response()->json([
                'preview_url' => $shareFrame->post_frame_url, // Show post format as preview
                'story_url' => $shareFrame->story_frame_url,
                'portrait_url' => $shareFrame->portrait_frame_url,
                'landscape_url' => $shareFrame->landscape_frame_url,
            ]);
        } catch (\Exception $e) {
            \Log::error('Preview generation failed', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'error' => 'Failed to generate preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the share frame template for a competition.
     */
    public function destroy(Competition $competition)
    {
        $template = $competition->activeShareFrameTemplate;
        
        if ($template) {
            // Delete background image if exists
            if ($template->background_image) {
                Storage::disk('public')->delete($template->background_image);
            }
            
            $template->delete();
        }

        return redirect()
            ->route('admin.competitions.edit', $competition)
            ->with('success', 'Share frame template deleted successfully!');
    }
}
