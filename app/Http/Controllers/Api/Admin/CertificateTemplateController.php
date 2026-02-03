<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CertificateTemplateController extends Controller
{
    /**
     * Display all certificate templates
     */
    public function index()
    {
        $templates = DB::table('certificate_templates')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($template) {
                return [
                    'id' => $template->id,
                    'title' => $template->title,
                    'description' => $template->description,
                    'type' => $template->type,
                    'width' => $template->width,
                    'height' => $template->height,
                    'background_color' => $template->background_color,
                    'accent_color' => $template->accent_color,
                    'text_color' => $template->text_color,
                    'title_font' => $template->title_font,
                    'is_default' => (bool) $template->is_default,
                    'template_content' => $template->template_content,
                    'created_at' => $template->created_at,
                    'updated_at' => $template->updated_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $templates,
            'message' => 'Certificate templates retrieved successfully'
        ]);
    }

    /**
     * Store a newly created certificate template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:participation,finalist,winner,merit',
            'width' => 'required|numeric|min:100|max:1000',
            'height' => 'required|numeric|min:100|max:1000',
            'background_color' => 'required|regex:/^#[0-9A-F]{6}$/i',
            'accent_color' => 'required|regex:/^#[0-9A-F]{6}$/i',
            'text_color' => 'required|regex:/^#[0-9A-F]{6}$/i',
            'title_font' => 'required|in:serif,sans-serif,monospace',
            'is_default' => 'boolean',
            'template_content' => 'nullable|string',
        ]);

        // If setting as default, remove default from others of same type
        if ($validated['is_default'] ?? false) {
            DB::table('certificate_templates')
                ->where('type', $validated['type'])
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $template = DB::table('certificate_templates')->insertGetId([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'width' => $validated['width'],
            'height' => $validated['height'],
            'background_color' => $validated['background_color'],
            'accent_color' => $validated['accent_color'],
            'text_color' => $validated['text_color'],
            'title_font' => $validated['title_font'],
            'is_default' => $validated['is_default'] ?? false,
            'template_content' => $validated['template_content'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => ['id' => $template],
            'message' => 'Certificate template created successfully'
        ], 201);
    }

    /**
     * Display the specified certificate template
     */
    public function show($id)
    {
        $template = DB::table('certificate_templates')->find($id);

        if (!$template) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate template not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $template,
            'message' => 'Certificate template retrieved successfully'
        ]);
    }

    /**
     * Update the specified certificate template
     */
    public function update(Request $request, $id)
    {
        $template = DB::table('certificate_templates')->find($id);

        if (!$template) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate template not found'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:participation,finalist,winner,merit',
            'width' => 'required|numeric|min:100|max:1000',
            'height' => 'required|numeric|min:100|max:1000',
            'background_color' => 'required|regex:/^#[0-9A-F]{6}$/i',
            'accent_color' => 'required|regex:/^#[0-9A-F]{6}$/i',
            'text_color' => 'required|regex:/^#[0-9A-F]{6}$/i',
            'title_font' => 'required|in:serif,sans-serif,monospace',
            'is_default' => 'boolean',
            'template_content' => 'nullable|string',
        ]);

        // If setting as default, remove default from others of same type
        if ($validated['is_default'] ?? false) {
            DB::table('certificate_templates')
                ->where('type', $validated['type'])
                ->where('id', '!=', $id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        DB::table('certificate_templates')->where('id', $id)->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'width' => $validated['width'],
            'height' => $validated['height'],
            'background_color' => $validated['background_color'],
            'accent_color' => $validated['accent_color'],
            'text_color' => $validated['text_color'],
            'title_font' => $validated['title_font'],
            'is_default' => $validated['is_default'] ?? false,
            'template_content' => $validated['template_content'] ?? null,
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Certificate template updated successfully'
        ]);
    }

    /**
     * Delete the specified certificate template
     */
    public function destroy($id)
    {
        $template = DB::table('certificate_templates')->find($id);

        if (!$template) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate template not found'
            ], 404);
        }

        // Don't delete if it's the only template for that type
        $count = DB::table('certificate_templates')
            ->where('type', $template->type)
            ->count();

        if ($count <= 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete the last template for this certificate type'
            ], 400);
        }

        DB::table('certificate_templates')->where('id', $id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Certificate template deleted successfully'
        ]);
    }

    /**
     * Get default template for a specific type
     */
    public function getDefault($type)
    {
        $template = DB::table('certificate_templates')
            ->where('type', $type)
            ->where('is_default', true)
            ->first();

        if (!$template) {
            // Fall back to first template of that type
            $template = DB::table('certificate_templates')
                ->where('type', $type)
                ->first();
        }

        if (!$template) {
            return response()->json([
                'status' => 'error',
                'message' => 'No certificate template found for type: ' . $type
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $template,
            'message' => 'Default certificate template retrieved successfully'
        ]);
    }
}
