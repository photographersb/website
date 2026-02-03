<?php

namespace App\Http\Controllers\Api;

use App\Models\Album;
use App\Models\Photo;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    use ApiResponse;
    /**
     * Get photographer albums
     */
    public function getAlbums(Request $request)
    {
        $photographer = \App\Models\Photographer::find($request->get('photographer_id'));

        if (!$photographer) {
            return $this->notFound('Photographer not found');
        }

        $albums = $photographer->albums()
            ->where('is_public', true)
            ->orderBy('display_order')
            ->paginate(20);

        return $this->paginated($albums, 'Albums retrieved successfully');
    }

    /**
     * Create album
     */
    public function createAlbum(Request $request)
    {
        $this->authorize('isPhotographer');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|exists:categories,id',
            'cover_photo_url' => 'required|url',
            'is_public' => 'boolean',
        ]);

        $album = Album::create([
            ...$validated,
            'photographer_id' => auth()->user()->photographer->id,
            'slug' => str()->slug($validated['name']),
        ]);

        return $this->created($album, 'Album created');
    }

    /**
     * Upload photos to album
     */
    public function uploadPhotos(Album $album, Request $request)
    {
        $this->authorize('update', $album);

        $validated = $request->validate([
            'photos' => 'required|array|max:50',
            'photos.*.image_url' => 'required|url',
            'photos.*.title' => 'nullable|string|max:255',
            'photos.*.camera_make' => 'nullable|string',
            'photos.*.camera_model' => 'nullable|string',
        ]);

        foreach ($validated['photos'] as $photoData) {
            Photo::create([
                'album_id' => $album->id,
                'photographer_id' => $album->photographer_id,
                ...$photoData,
            ]);
        }

        $album->increment('photo_count', count($validated['photos']));

        return $this->success($album->photos, count($validated['photos']) . ' photos uploaded');
    }

    /**
     * Delete album
     */
    public function deleteAlbum(Album $album)
    {
        $this->authorize('delete', $album);

        $album->photos()->delete();
        $album->delete();

        return $this->success([], 'Album deleted');
    }
}
