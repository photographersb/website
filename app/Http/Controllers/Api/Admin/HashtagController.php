<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Hashtag;
use App\Models\PhotoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HashtagController extends Controller
{
    public function index(Request $request)
    {
        $query = Hashtag::with('category');
        
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->is_featured) {
            $query->where('is_featured', true);
        }
        
        $hashtags = $query->orderBy('usage_count', 'desc')->get();
        return response()->json($hashtags);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:photo_categories,id',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Hashtag::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $hashtag = Hashtag::create($validated);
        return response()->json($hashtag->load('category'), 201);
    }

    public function show($id)
    {
        $hashtag = Hashtag::with('category')->findOrFail($id);
        return response()->json($hashtag);
    }

    public function update(Request $request, $id)
    {
        $hashtag = Hashtag::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'nullable|exists:photo_categories,id',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        if (isset($validated['name']) && $validated['name'] !== $hashtag->name) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Hashtag::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $hashtag->update($validated);
        return response()->json($hashtag->load('category'));
    }

    public function destroy($id)
    {
        $hashtag = Hashtag::findOrFail($id);
        $hashtag->delete();
        return response()->json(['message' => 'Hashtag deleted successfully']);
    }

    public function featured()
    {
        $hashtags = Hashtag::with('category')
            ->where('is_featured', true)
            ->orderBy('usage_count', 'desc')
            ->get();
        return response()->json($hashtags);
    }
}
