<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{
    public function index()
    {
        $templates = CertificateTemplate::query()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $templates,
            'message' => 'Certificate templates retrieved successfully',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateTemplatePayload($request);

        if (($validated['is_default'] ?? false) === true) {
            CertificateTemplate::query()
                ->where('type', $validated['type'])
                ->update(['is_default' => false]);
        }

        $template = CertificateTemplate::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $template,
            'message' => 'Certificate template created successfully',
        ], 201);
    }

    public function show($id)
    {
        $template = CertificateTemplate::find($id);

        if (!$template) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate template not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $template,
            'message' => 'Certificate template retrieved successfully',
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = CertificateTemplate::find($id);

        if (!$template) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate template not found',
            ], 404);
        }

        $validated = $this->validateTemplatePayload($request);

        if (($validated['is_default'] ?? false) === true) {
            CertificateTemplate::query()
                ->where('type', $validated['type'])
                ->where('id', '!=', $template->id)
                ->update(['is_default' => false]);
        }

        $template->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Certificate template updated successfully',
        ]);
    }

    public function destroy($id)
    {
        $template = CertificateTemplate::find($id);

        if (!$template) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate template not found',
            ], 404);
        }

        $count = CertificateTemplate::query()
            ->where('type', $template->type)
            ->count();

        if ($count <= 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete the last template for this certificate type',
            ], 400);
        }

        $template->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Certificate template deleted successfully',
        ]);
    }

    public function getDefault($type)
    {
        $template = CertificateTemplate::query()
            ->where('type', $type)
            ->where('is_default', true)
            ->first();

        if (!$template) {
            $template = CertificateTemplate::query()
                ->where('type', $type)
                ->first();
        }

        if (!$template) {
            return response()->json([
                'status' => 'error',
                'message' => 'No certificate template found for type: ' . $type,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $template,
            'message' => 'Default certificate template retrieved successfully',
        ]);
    }

    protected function validateTemplatePayload(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:event,competition,award,participation,finalist,winner,merit',
            'width' => 'required|numeric|min:100|max:1000',
            'height' => 'required|numeric|min:100|max:1000',
            'background_image' => 'nullable|string|max:2048',
            'background_color' => 'required|regex:/^#[0-9A-F]{6}$/i',
            'accent_color' => 'required|regex:/^#[0-9A-F]{6}$/i',
            'text_color' => 'required|regex:/^#[0-9A-F]{6}$/i',
            'font_family' => 'nullable|string|max:100',
            'font_size' => 'nullable|integer|min:10|max:120',
            'title_font' => 'required|in:serif,sans-serif,monospace',
            'is_default' => 'boolean',
            'template_content' => 'nullable|string',
        ]);
    }
}
