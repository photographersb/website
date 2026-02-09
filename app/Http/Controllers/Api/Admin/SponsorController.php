<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::orderBy('display_order')->get();
        return response()->json(['data' => $sponsors]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string',
            'logo_credit_name' => 'nullable|string|max:255',
            'logo_credit_url' => 'nullable|url|max:255',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'display_order' => 'nullable|integer',
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
            'is_featured' => 'nullable|boolean',
        ]);

        // Convert empty date strings to null (prevent empty strings from being stored)
        $validated['start_date'] = empty($validated['start_date']) ? null : $validated['start_date'];
        $validated['end_date'] = empty($validated['end_date']) ? null : $validated['end_date'];

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
            'logo_credit_name' => 'nullable|string|max:255',
            'logo_credit_url' => 'nullable|url|max:255',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:active,inactive',
            'display_order' => 'nullable|integer',
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
            'is_featured' => 'nullable|boolean',
        ]);

        // Convert empty date strings to null (prevent empty strings from being stored)
        if (isset($validated['start_date'])) {
            $validated['start_date'] = empty($validated['start_date']) ? null : $validated['start_date'];
        }
        if (isset($validated['end_date'])) {
            $validated['end_date'] = empty($validated['end_date']) ? null : $validated['end_date'];
        }

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
        try {
            $sponsor = Sponsor::findOrFail($id);
            
            // Delete logo file if exists
            if ($sponsor->logo && strpos($sponsor->logo, 'storage/') !== false) {
                $path = str_replace('storage/', '', parse_url($sponsor->logo, PHP_URL_PATH));
                Storage::disk('public')->delete($path);
            }
            
            $sponsor->delete();
            return response()->json(['message' => 'Sponsor deleted successfully']);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete sponsor: This sponsor may be linked to competitions or other data. Please remove those associations first.',
                ], 409);
            }
            throw $e;
        }
    }
    
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('sponsors', $filename, 'public');
            
            return response()->json([
                'url' => Storage::url($path),
                'path' => $path
            ]);
        }
        
        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
