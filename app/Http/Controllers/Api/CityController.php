<?php

namespace App\Http\Controllers\Api;

use App\Models\Location;
use App\Http\Requests\StoreCityRequest;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CityController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $locations = Cache::remember('locations_public_list', 3600, function () {
            return Location::withCount('photographers')
                ->where('is_active', true)
                ->whereIn('type', ['district', 'upazila'])
                ->orderByDesc('photographers_count')
                ->orderBy('name')
                ->get();
        });
        return $this->success($locations, 'Locations retrieved successfully');
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $query = Location::withCount('photographers')
            ->with('parent')
            ->orderBy('sort_order')
            ->orderBy('name');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhereHas('parent', function ($parentQ) use ($search) {
                      $parentQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        // Check if requesting for simple dropdown (minimal data) - for event forms
        if ($request->get('minimal') === 'true' || $request->get('minimal') === '1') {
            $locations = $query->select('id', 'name', 'slug', 'type', 'parent_id')->get();
            return $this->success($locations, 'Locations retrieved successfully');
        }

        // Pagination for admin list view
        $perPage = $request->get('per_page', 15);
        $locations = $query->paginate($perPage);

        return $this->paginated($locations, 'Locations retrieved successfully');
    }

    public function store(StoreCityRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['name']);

        $location = Location::create($validated);

        // Clear city caches
        Cache::forget('locations_public_list');
        Cache::forget('locations_admin_list');

        return $this->created($location, 'Location created successfully');
    }

    public function show($id): JsonResponse
    {
        $location = Location::with('parent')->findOrFail($id);

        return $this->success($location, 'Location retrieved successfully');
    }

    public function update(StoreCityRequest $request, $id): JsonResponse
    {
        $location = Location::findOrFail($id);

        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['name']);

        $location->update($validated);

        // Clear city caches
        Cache::forget('locations_public_list');
        Cache::forget('locations_admin_list');

        return $this->success($location->fresh(), 'Location updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $location = Location::findOrFail($id);
        
        // Check if city has photographers
        if ($location->photographers()->count() > 0) {
            return $this->error('Cannot delete location with associated photographers', 422);
        }

        if ($location->children()->count() > 0) {
            return $this->error('Cannot delete location with child locations', 422);
        }

        if ($location->events()->count() > 0) {
            return $this->error('Cannot delete location with associated events', 422);
        }

        if ($location->competitionSubmissions()->count() > 0) {
            return $this->error('Cannot delete location with associated competition submissions', 422);
        }

        $location->delete();

        // Clear city caches
        Cache::forget('locations_public_list');
        Cache::forget('locations_admin_list');

        return $this->success([], 'Location deleted successfully');
    }
}
