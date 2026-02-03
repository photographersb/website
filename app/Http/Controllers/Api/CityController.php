<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Http\Requests\StoreCityRequest;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CityController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $cities = City::orderBy('name')->get();
        return $this->success($cities, 'Cities retrieved successfully');
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $query = City::withCount('photographers')
            ->orderBy('display_order')
            ->orderBy('name');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('division', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%");
            });
        }

        // Check if requesting for simple dropdown (minimal data) - for event forms
        if ($request->get('minimal') === 'true' || $request->get('minimal') === '1') {
            $cities = $query->select('id', 'name', 'slug', 'state', 'division')->get();
            return $this->success($cities, 'Cities retrieved successfully');
        }

        // Pagination for admin list view
        $perPage = $request->get('per_page', 15);
        $cities = $query->paginate($perPage);

        return $this->paginated($cities, 'Cities retrieved successfully');
    }

    public function store(StoreCityRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['name']);

        $city = City::create($validated);

        return $this->created($city, 'City created successfully');
    }

    public function show($id): JsonResponse
    {
        $city = City::findOrFail($id);

        return $this->success($city, 'City retrieved successfully');
    }

    public function update(StoreCityRequest $request, $id): JsonResponse
    {
        $city = City::findOrFail($id);

        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['name']);

        $city->update($validated);

        return $this->success($city->fresh(), 'City updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $city = City::findOrFail($id);
        
        // Check if city has photographers
        if ($city->photographers()->count() > 0) {
            return $this->error('Cannot delete city with associated photographers', 422);
        }

        $city->delete();

        return $this->success([], 'City deleted successfully');
    }
}
