<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateAutomationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateAutomationRuleController extends Controller
{
    public function index()
    {
        $rules = CertificateAutomationRule::with('template:id,title,type')
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $rules,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|in:event_attendance,workshop_completed,competition_winners_announced,participation_confirmed',
            'source_type' => 'required|in:event,workshop,competition,award,participation',
            'source_id' => 'nullable|integer|min:1',
            'template_id' => 'required|exists:certificate_templates,id',
            'is_active' => 'boolean',
            'config' => 'nullable|array',
        ]);

        $validated['created_by_user_id'] = Auth::id();
        $rule = CertificateAutomationRule::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $rule,
            'message' => 'Automation rule created successfully.',
        ], 201);
    }

    public function update(Request $request, CertificateAutomationRule $rule)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'trigger_type' => 'sometimes|required|in:event_attendance,workshop_completed,competition_winners_announced,participation_confirmed',
            'source_type' => 'sometimes|required|in:event,workshop,competition,award,participation',
            'source_id' => 'nullable|integer|min:1',
            'template_id' => 'sometimes|required|exists:certificate_templates,id',
            'is_active' => 'sometimes|boolean',
            'config' => 'nullable|array',
        ]);

        $rule->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $rule->fresh('template:id,title,type'),
            'message' => 'Automation rule updated successfully.',
        ]);
    }

    public function destroy(CertificateAutomationRule $rule)
    {
        $rule->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Automation rule deleted successfully.',
        ]);
    }
}
