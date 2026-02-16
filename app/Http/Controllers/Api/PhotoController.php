<?php

namespace App\Http\Controllers\Api;

use App\Models\Photo;
use App\Models\Album;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ImageProcessingService;
use App\Services\PhotoMetadataService;

class PhotoController extends Controller
{
    use ApiResponse;
    /**
     * Add photos to album
     */
    public function store(Request $request, $albumId)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        // Verify album ownership
        $album = Album::where('photographer_id', $photographer->id)->findOrFail($albumId);

        $validated = $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*.image_url' => 'required|url',
            'photos.*.thumbnail_url' => 'nullable|url',
            'photos.*.title' => 'nullable|string|max:255',
            'photos.*.description' => 'nullable|string|max:500',
            'photos.*.mime_type' => 'nullable|in:image/jpeg,image/png,image/webp',
            'photos.*.file_size' => 'nullable|integer|max:5242880',
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

        return $this->created($createdPhotos, count($createdPhotos) . ' photo(s) added successfully');
    }

    /**
     * Upload photo files to album
     */
    public function upload(Request $request, $albumId, PhotoMetadataService $metadataService, ImageProcessingService $imageService)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        // Verify album ownership
        $album = Album::where('photographer_id', $photographer->id)->findOrFail($albumId);

        $validated = $request->validate([
            'photos' => 'required|array|min:1|max:20',
            'photos.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240',
        ]);

        $createdPhotos = [];

        foreach ($validated['photos'] as $file) {
            // Extract EXIF metadata
            $metadata = $metadataService->extractMetadata($file);

            // Store main image
            $path = 'portfolio/' . $photographer->id . '/albums/' . $album->id;
            $result = $imageService->processAndSave($file, $path, [
                'max_width' => 2048,
                'max_height' => 2048,
                'quality' => 85,
                'format' => 'jpg'
            ]);

            if (!$result['success']) {
                return $this->error('Failed to upload image: ' . $result['error'], 500);
            }

            // Create thumbnail
            $thumbResult = $imageService->processAndSave($file, $path, [
                'max_width' => 800,
                'max_height' => 800,
                'quality' => 80,
                'format' => 'jpg'
            ]);

            $imageUrl = $result['url'];
            $thumbnailUrl = $thumbResult['success'] ? $thumbResult['url'] : $imageUrl;

            $photo = Photo::create([
                'uuid' => Str::uuid(),
                'album_id' => $album->id,
                'photographer_id' => $photographer->id,
                'image_url' => $imageUrl,
                'thumbnail_url' => $thumbnailUrl,
                'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'display_order' => Photo::where('album_id', $album->id)->max('display_order') + 1,
                'camera_make' => $metadata['camera_make'] ?? null,
                'camera_model' => $metadata['camera_model'] ?? null,
                'camera_settings' => $metadata['camera_settings'] ?? null,
                'location' => $metadata['location'] ?? null,
                'date_taken' => $metadata['date_taken'] ?? null,
            ]);

            $createdPhotos[] = $photo;
        }

        // Update album photo count
        $album->increment('photo_count', count($createdPhotos));

        // Set first photo as cover if album doesn't have one
        if (!$album->cover_photo_url && count($createdPhotos) > 0) {
            $album->update(['cover_photo_url' => $createdPhotos[0]->image_url]);
        }

        return $this->created($createdPhotos, count($createdPhotos) . ' photo(s) uploaded successfully');
    }

    /**
     * Get photos from Pexels API
     */
    public function searchPexels(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:80',
            'orientation' => 'nullable|in:landscape,portrait,square',
        ]);

        $query = $validated['query'];
        $perPage = $validated['per_page'] ?? 15;
        $orientation = $validated['orientation'] ?? 'landscape';

        try {
            $apiKey = config('services.pexels.key');
            if (!$apiKey) {
                return $this->error('Pexels API key is not configured.', 500);
            }

            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->get('https://api.pexels.com/v1/search', [
                'query' => $query,
                'per_page' => $perPage,
                'orientation' => $orientation,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $photos = array_map(function ($photo) {
                    return [
                        'id' => $photo['id'],
                        'image_url' => $photo['src']['large2x'],
                        'thumbnail_url' => $photo['src']['medium'],
                        'photographer' => $photo['photographer'],
                        'photographer_url' => $photo['photographer_url'] ?? null,
                        'url' => $photo['url'] ?? null,
                        'width' => $photo['width'],
                        'height' => $photo['height'],
                    ];
                }, $data['photos'] ?? []);

                return $this->success($photos, 'Photos retrieved from Pexels', 200, ['total' => $data['total_results'] ?? 0]);
            }

            $status = $response->status();
            $payload = $response->json();
            $errorDetails = $payload['error'] ?? $payload['message'] ?? Str::limit($response->body(), 200);

            Log::warning('Pexels API request failed', [
                'status' => $status,
                'error' => $errorDetails,
            ]);

            return $this->error('Failed to fetch photos from Pexels (' . $status . '): ' . $errorDetails, 500);

        } catch (\Exception $e) {
            return $this->error('Error connecting to Pexels: ' . $e->getMessage(), 500);
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
            return $this->notFound('Photographer profile not found');
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

        return $this->success([], 'Photo deleted successfully');
    }

    /**
     * Update photo
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $photo = Photo::where('photographer_id', $photographer->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $photo->update($validated);

        return $this->success($photo->fresh(), 'Photo updated successfully');
    }
}
