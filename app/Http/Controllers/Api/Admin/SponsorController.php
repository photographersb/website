<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::orderBy('display_order')->get();
        return response()->json($sponsors);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'display_order' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Sponsor::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $sponsor = Sponsor::create($validated);
        return response()->json($sponsor, 201);
    }

    public function show($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        return response()->json($sponsor);
    }

    public function update(Request $request, $id)
    {
        $sponsor = Sponsor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'logo' => 'nullable|string',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:active,inactive',
            'display_order' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if (isset($validated['name']) && $validated['name'] !== $sponsor->name) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Sponsor::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $sponsor->update($validated);
        return response()->json($sponsor);
    }

    public function destroy($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        $sponsor->delete();
        return response()->json(['message' => 'Sponsor deleted successfully']);
    }
}
