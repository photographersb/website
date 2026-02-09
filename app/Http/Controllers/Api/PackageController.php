<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\ImageProcessingService;

class PackageController extends Controller
{
    use ApiResponse;
    /**
     * Get photographer's packages
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $packages = Package::where('photographer_id', $photographer->id)
            ->orderBy('display_order')
            ->orderBy('price', 'asc')
            ->get();

        return $this->success($packages, 'Packages retrieved successfully');
    }

    /**
     * Create new package
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'edited_photos' => 'required|integer|min:0',
            'raw_photos' => 'nullable|integer|min:0',
            'delivery_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'cover_image' => 'nullable|url',
            'sample_images' => 'nullable|array',
            'sample_images.*' => 'url',
        ]);

        $package = Package::create([
            'photographer_id' => $photographer->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'base_price' => $validated['price'], // Map price to base_price for DB compatibility
            'price' => $validated['price'],
            'duration_unit' => 'hours',
            'duration_value' => $validated['duration_hours'],
            'duration_hours' => $validated['duration_hours'],
            'edited_photos' => $validated['edited_photos'],
            'raw_photos' => $validated['raw_photos'] ?? 0,
            'delivery_days' => $validated['delivery_days'],
            'is_active' => $validated['is_active'] ?? true,
            'cover_image' => $validated['cover_image'] ?? null,
            'sample_images' => $validated['sample_images'] ?? null,
        ]);

        // Track achievement
        \App\Services\AchievementService::trackPackageCreated($photographer->id);

        return $this->created($package, 'Package created successfully');
    }

    /**
     * Get single package
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $package = Package::where('photographer_id', $photographer->id)
            ->findOrFail($id);

        return $this->success($package, 'Package retrieved successfully');
    }

    /**
     * Update package
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $package = Package::where('photographer_id', $photographer->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:2000',
            'price' => 'sometimes|required|numeric|min:0',
            'duration_hours' => 'sometimes|required|integer|min:1',
            'edited_photos' => 'sometimes|required|integer|min:0',
            'raw_photos' => 'nullable|integer|min:0',
            'delivery_days' => 'sometimes|required|integer|min:1',
            'is_active' => 'boolean',
            'cover_image' => 'nullable|url',
            'sample_images' => 'nullable|array',
            'sample_images.*' => 'url',
        ]);

        // Map price to base_price for DB compatibility
        if (isset($validated['price'])) {
            $validated['base_price'] = $validated['price'];
        }
        if (isset($validated['duration_hours'])) {
            $validated['duration_value'] = $validated['duration_hours'];
        }

        $package->update($validated);

        return $this->success($package->fresh(), 'Package updated successfully');
    }

    /**
     * Upload package images (cover + samples)
     */
    public function uploadImages(Request $request, $id, ImageProcessingService $imageService)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $package = Package::where('photographer_id', $photographer->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
            'sample_images' => 'nullable|array|max:10',
            'sample_images.*' => 'image|mimes:jpeg,jpg,png,webp|max:10240',
        ]);

        $path = 'packages/' . $photographer->id . '/' . $package->id;
        $updated = [];

        if ($request->hasFile('cover_image')) {
            $coverFile = $request->file('cover_image');
            $coverResult = $imageService->processAndSave($coverFile, $path, [
                'max_width' => 2048,
                'max_height' => 2048,
                'quality' => 85,
                'format' => 'jpg'
            ]);

            if (!$coverResult['success']) {
                return $this->error('Failed to upload cover image: ' . $coverResult['error'], 500);
            }

            $updated['cover_image'] = $coverResult['url'];
        }

        if ($request->hasFile('sample_images')) {
            $existingSamples = $package->sample_images ?? [];
            $newSamples = [];

            foreach ($request->file('sample_images') as $file) {
                $sampleResult = $imageService->processAndSave($file, $path, [
                    'max_width' => 2048,
                    'max_height' => 2048,
                    'quality' => 85,
                    'format' => 'jpg'
                ]);

                if (!$sampleResult['success']) {
                    return $this->error('Failed to upload sample image: ' . $sampleResult['error'], 500);
                }

                $newSamples[] = $sampleResult['url'];
            }

            $updated['sample_images'] = array_values(array_unique(array_merge($existingSamples, $newSamples)));
        }

        if (!empty($updated)) {
            $package->update($updated);
        }

        return $this->success($package->fresh(), 'Package images uploaded successfully');
    }

    /**
     * Delete package
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $package = Package::where('photographer_id', $photographer->id)
            ->findOrFail($id);

        $package->delete();

        return $this->success([], 'Package deleted successfully');
    }
}
