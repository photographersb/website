<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    use ApiResponse;
    public function index(): JsonResponse
    {
        $categories = Cache::remember('categories_public_list', 3600, function () {
            return Category::where('is_active', true)
                ->withCount(['photographers' => function ($query) {
                    $query->where('is_verified', true);
                }])
                ->orderByDesc('photographers_count')
                ->orderBy('display_order')
                ->orderBy('name')
                ->get();
        });

        return $this->success($categories, 'Categories retrieved successfully');
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $query = Category::withCount('photographers')
            ->orderBy('display_order')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 20);
        $categories = $query->paginate($perPage);

        return $this->success($categories, 'Categories retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = Category::create($validated);

        // Clear category caches
        Cache::forget('categories_public_list');
        Cache::forget('categories_admin_list');

        return $this->created($category, 'Category created successfully');
    }

    public function show($id): JsonResponse
    {
        $category = Category::findOrFail($id);

        return $this->success($category, 'Category retrieved successfully');
    }

    public function update(Request $request, $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        // Clear category caches
        Cache::forget('categories_public_list');
        Cache::forget('categories_admin_list');

        return $this->success($category->fresh(), 'Category updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $category = Category::findOrFail($id);
        
        // Check if category has photographers
        if ($category->photographers()->count() > 0) {
            return $this->validationError([], 'Cannot delete category with associated photographers');
        }

        $category->delete();

        // Clear category caches
        Cache::forget('categories_public_list');
        Cache::forget('categories_admin_list');

        return $this->success([], 'Category deleted successfully');
    }
}
