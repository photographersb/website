<?php

namespace App\Http\Controllers\Api;

use App\Models\Album;
use App\Http\Requests\AlbumStoreRequest;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    use ApiResponse;
    /**
     * Get photographer's albums
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $albums = Album::where('photographer_id', $photographer->id)
            ->withCount('photos')
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($albums, 'Albums retrieved successfully');
    }

    /**
     * Create new album
     */
    public function store(AlbumStoreRequest $request)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $validated = $request->validated();

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

        // Track achievement
        \App\Services\AchievementService::trackAlbumCreated($photographer->id);

        return $this->created($album, 'Album created successfully');
    }

    /**
     * Get single album
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $album = Album::where('photographer_id', $photographer->id)
            ->with('photos')
            ->findOrFail($id);

        return $this->success($album, 'Album retrieved successfully');
    }

    /**
     * Update album
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
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

        return $this->success($album->fresh(), 'Album updated successfully');
    }

    /**
     * Delete album
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $album = Album::where('photographer_id', $photographer->id)
            ->findOrFail($id);

        // Delete all photos in the album
        $album->photos()->delete();

        // Delete the album
        $album->delete();

        return $this->success([], 'Album deleted successfully');
    }
}
