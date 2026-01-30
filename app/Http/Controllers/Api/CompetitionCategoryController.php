<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionCategory;
use App\Services\CategoryManagementService;
use Illuminate\Http\Request;

class CompetitionCategoryController extends Controller
{
    /**
     * Get all categories for a competition
     */
    public function index(Request $request, Competition $competition, CategoryManagementService $categoryService)
    {
        $activeOnly = $request->boolean('active_only', false);
        $result = $categoryService->getCategories($competition, $activeOnly);

        return response()->json([
            'status' => 'success',
            'data' => $result['data'],
            'total' => $result['total']
        ]);
    }

    /**
     * Get category details
     */
    public function show(CompetitionCategory $category, CategoryManagementService $categoryService)
    {
        $result = $categoryService->getCategoryDetails($category);

        return response()->json([
            'status' => 'success',
            'data' => $result['data']
        ]);
    }

    /**
     * Create a new category
     */
    public function store(Request $request, Competition $competition, CategoryManagementService $categoryService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prize_amount' => 'nullable|numeric|min:0',
            'max_submissions_per_user' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean'
        ]);

        $result = $categoryService->createCategory($competition, $request->all());

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null
        ], $result['success'] ? 201 : 400);
    }

    /**
     * Update a category
     */
    public function update(Request $request, CompetitionCategory $category, CategoryManagementService $categoryService)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'prize_amount' => 'nullable|numeric|min:0',
            'max_submissions_per_user' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean'
        ]);

        $result = $categoryService->updateCategory($category, $request->all());

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Delete a category
     */
    public function destroy(CompetitionCategory $category, CategoryManagementService $categoryService)
    {
        $result = $categoryService->deleteCategory($category);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message']
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Bulk create categories
     */
    public function bulkCreate(Request $request, Competition $competition, CategoryManagementService $categoryService)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.name' => 'required|string|max:255',
            'categories.*.description' => 'nullable|string',
            'categories.*.prize_amount' => 'nullable|numeric|min:0',
            'categories.*.max_submissions_per_user' => 'nullable|integer|min:1',
            'categories.*.is_active' => 'nullable|boolean'
        ]);

        $result = $categoryService->bulkCreateCategories($competition, $request->categories);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null
        ], $result['success'] ? 201 : 400);
    }

    /**
     * Toggle category active status
     */
    public function toggleActive(CompetitionCategory $category, CategoryManagementService $categoryService)
    {
        $result = $categoryService->toggleActiveStatus($category);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Get category leaderboard
     */
    public function leaderboard(Request $request, CompetitionCategory $category, CategoryManagementService $categoryService)
    {
        $limit = $request->input('limit', 20);
        $result = $categoryService->getCategoryLeaderboard($category, $limit);

        return response()->json([
            'status' => 'success',
            'data' => $result['data']
        ]);
    }

    /**
     * Get winners by category for a competition
     */
    public function winnersByCategory(Competition $competition, CategoryManagementService $categoryService)
    {
        $result = $categoryService->getWinnersByCategory($competition);

        return response()->json([
            'status' => 'success',
            'data' => $result['data']
        ]);
    }

    /**
     * Get category statistics
     */
    public function statistics(Competition $competition, CategoryManagementService $categoryService)
    {
        $result = $categoryService->getCategoryStatistics($competition);

        return response()->json([
            'status' => 'success',
            'data' => $result['data']
        ]);
    }
}
