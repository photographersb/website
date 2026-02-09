<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hashtag;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class HashtagManagementController extends Controller
{
    /**
     * Get all hashtags with pagination and filters
     */
    public function index(Request $request)
    {
        $query = Hashtag::with('category');
        
        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', filter_var($request->input('is_featured'), FILTER_VALIDATE_BOOLEAN));
        }
        
        // Sort options: trending, recent, popular
        $sortBy = $request->input('sort_by', 'trending');
        
        if ($sortBy === 'recent') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('usage_count', 'desc');
        }
        
        $hashtags = $query->paginate($request->input('per_page', 20));
        
        return response()->json([
            'data' => $hashtags->items(),
            'pagination' => [
                'total' => $hashtags->total(),
                'per_page' => $hashtags->perPage(),
                'current_page' => $hashtags->currentPage(),
                'last_page' => $hashtags->lastPage(),
            ]
        ]);
    }
    
    /**
     * Get a single hashtag
     */
    public function show(Hashtag $hashtag)
    {
        return response()->json(['data' => $hashtag]);
    }
    
    /**
     * Create a new hashtag (manual creation)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hashtags,name',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:photo_categories,id',
            'is_featured' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Hashtag::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $hashtag = Hashtag::create($validated);

        return response()->json(['data' => $hashtag->load('category')], 201);
    }
    
    /**
     * Update hashtag
     */
    public function update(Request $request, Hashtag $hashtag)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:photo_categories,id',
            'is_featured' => 'nullable|boolean',
        ]);

        if (isset($validated['name']) && $validated['name'] !== $hashtag->name) {
            $validated['slug'] = Str::slug($validated['name']);
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Hashtag::where('slug', $validated['slug'])->where('id', '!=', $hashtag->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $hashtag->update($validated);

        return response()->json(['data' => $hashtag->fresh('category')]);
    }
    
    /**
     * Delete hashtag
     */
    public function destroy(Hashtag $hashtag)
    {
        $hashtag->delete();
        
        return response()->json(null, 204);
    }
}
