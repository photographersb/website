<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateTemplateController extends Controller
{
    /**
     * Show all certificate templates.
     */
    public function index()
    {
        $templates = CertificateTemplate::with('createdBy')
            ->latest()
            ->paginate(20);

        return inertia('Admin/Certificates/Templates/Index', [
            'templates' => $templates,
        ]);
    }

    /**
     * Show form to create new template.
     */
    public function create()
    {
        return inertia('Admin/Certificates/Templates/Create', [
            'template' => new CertificateTemplate(),
        ]);
    }

    /**
     * Store new certificate template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'primary_color' => 'required|string|max:7',
            'font_family' => 'required|string|max:100',
            'background_image' => 'nullable|image|max:5120', // 5MB
            'settings' => 'nullable|array',
        ]);

        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('certificate-templates', 'public');
            $validated['background_image_path'] = $path;
        }

        $validated['settings'] = $request->input('settings', []);
        $validated['created_by_user_id'] = auth()->id();

        $template = CertificateTemplate::create($validated);

        return redirect()
            ->route('admin.certificates.templates.edit', $template)
            ->with('success', 'Certificate template created successfully!');
    }

    /**
     * Show form to edit template.
     */
    public function edit(CertificateTemplate $template)
    {
        return inertia('Admin/Certificates/Templates/Edit', [
            'template' => $template->load('createdBy'),
        ]);
    }

    /**
     * Update certificate template.
     */
    public function update(Request $request, CertificateTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'primary_color' => 'required|string|max:7',
            'font_family' => 'required|string|max:100',
            'background_image' => 'nullable|image|max:5120',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('background_image')) {
            if ($template->background_image_path) {
                Storage::disk('public')->delete($template->background_image_path);
            }
            $path = $request->file('background_image')->store('certificate-templates', 'public');
            $validated['background_image_path'] = $path;
        }

        $validated['settings'] = $request->input('settings', []);

        $template->update($validated);

        return redirect()
            ->route('admin.certificates.templates.edit', $template)
            ->with('success', 'Certificate template updated successfully!');
    }

    /**
     * Delete certificate template.
     */
    public function destroy(CertificateTemplate $template)
    {
        if ($template->background_image_path) {
            Storage::disk('public')->delete($template->background_image_path);
        }

        $template->delete();

        return redirect()
            ->route('admin.certificates.templates.index')
            ->with('success', 'Certificate template deleted successfully!');
    }
}
