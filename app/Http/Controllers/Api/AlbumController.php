<?php

namespace App\Http\Controllers\Api;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    /**
     * Get photographer's albums
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Photographer profile not found',
            ], 404);
        }

        $albums = Album::where('photographer_id', $photographer->id)
            ->withCount('photos')
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $albums,
        ]);
    }

    /**
     * Create new album
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Photographer profile not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        // Generate slug from name
        $slug = \Illuminate\Support\Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        
        // Ensure slug is unique for this photographer
        while (Album::where('photographer_id', $photographer->id)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $album = Album::create([
            'photographer_id' => $photographer->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_public' => $validated['is_public'] ?? true,
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Album created successfully',
            'data' => $album,
        ], 201);
    }

    /**
     * Get single album
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Photographer profile not found',
            ], 404);
        }

        $album = Album::where('photographer_id', $photographer->id)
            ->with('photos')
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $album,
        ]);
    }

    /**
     * Update album
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Photographer profile not found',
            ], 404);
        }

        $album = Album::where('photographer_id', $photographer->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        // If name is being updated, regenerate slug
        if (isset($validated['name'])) {
            $slug = \Illuminate\Support\Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;
            
            while (Album::where('photographer_id', $photographer->id)
                ->where('slug', $slug)
                ->where('id', '!=', $id)
                ->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        $album->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Album updated successfully',
            'data' => $album->fresh(),
        ]);
    }

    /**
     * Delete album
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Photographer profile not found',
            ], 404);
        }

        $album = Album::where('photographer_id', $photographer->id)
            ->findOrFail($id);

        // Delete all photos in the album
        $album->photos()->delete();

        // Delete the album
        $album->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Album deleted successfully',
        ]);
    }
}
