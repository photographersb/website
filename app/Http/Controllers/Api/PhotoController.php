<?php

namespace App\Http\Controllers\Api;

use App\Models\Photo;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class PhotoController extends Controller
{
    /**
     * Add photos to album
     */
    public function store(Request $request, $albumId)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Photographer profile not found',
            ], 404);
        }

        // Verify album ownership
        $album = Album::where('photographer_id', $photographer->id)->findOrFail($albumId);

        $validated = $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*.image_url' => 'required|url',
            'photos.*.thumbnail_url' => 'nullable|url',
            'photos.*.title' => 'nullable|string|max:255',
            'photos.*.description' => 'nullable|string|max:500',
        ]);

        $createdPhotos = [];

        foreach ($validated['photos'] as $photoData) {
            $photo = Photo::create([
                'uuid' => Str::uuid(),
                'album_id' => $album->id,
                'photographer_id' => $photographer->id,
                'image_url' => $photoData['image_url'],
                'thumbnail_url' => $photoData['thumbnail_url'] ?? $photoData['image_url'],
                'title' => $photoData['title'] ?? null,
                'description' => $photoData['description'] ?? null,
                'display_order' => Photo::where('album_id', $album->id)->max('display_order') + 1,
            ]);

            $createdPhotos[] = $photo;
        }

        // Update album photo count
        $album->increment('photo_count', count($createdPhotos));

        // Set first photo as cover if album doesn't have one
        if (!$album->cover_photo_url && count($createdPhotos) > 0) {
            $album->update(['cover_photo_url' => $createdPhotos[0]->image_url]);
        }

        return response()->json([
            'status' => 'success',
            'message' => count($createdPhotos) . ' photo(s) added successfully',
            'data' => $createdPhotos,
        ], 201);
    }

    /**
     * Get photos from Pexels API
     */
    public function searchPexels(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:80',
        ]);

        $query = $validated['query'];
        $perPage = $validated['per_page'] ?? 15;

        try {
            // Use Pexels API (you should add PEXELS_API_KEY to .env)
            $apiKey = env('PEXELS_API_KEY', 'demo');
            
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->get('https://api.pexels.com/v1/search', [
                'query' => $query,
                'per_page' => $perPage,
                'orientation' => 'landscape',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $photos = array_map(function ($photo) {
                    return [
                        'id' => $photo['id'],
                        'image_url' => $photo['src']['large2x'],
                        'thumbnail_url' => $photo['src']['medium'],
                        'photographer' => $photo['photographer'],
                        'width' => $photo['width'],
                        'height' => $photo['height'],
                    ];
                }, $data['photos'] ?? []);

                return response()->json([
                    'status' => 'success',
                    'data' => $photos,
                    'total' => $data['total_results'] ?? 0,
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch photos from Pexels',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error connecting to Pexels: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete photo
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

        $photo = Photo::where('photographer_id', $photographer->id)->findOrFail($id);
        $album = $photo->album;

        $photo->delete();

        // Update album photo count
        $album->decrement('photo_count');

        // If deleted photo was cover, set new cover
        if ($album->cover_photo_url === $photo->image_url) {
            $newCover = Photo::where('album_id', $album->id)->first();
            $album->update(['cover_photo_url' => $newCover ? $newCover->image_url : null]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Photo deleted successfully',
        ]);
    }

    /**
     * Update photo
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

        $photo = Photo::where('photographer_id', $photographer->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $photo->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Photo updated successfully',
            'data' => $photo->fresh(),
        ]);
    }
}
