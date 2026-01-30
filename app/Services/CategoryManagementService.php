<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionCategory;
use App\Models\CompetitionSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryManagementService
{
    /**
     * Create a new category for a competition
     */
    public function createCategory(Competition $competition, array $categoryData)
    {
        try {
            $category = CompetitionCategory::create([
                'competition_id' => $competition->id,
                'name' => $categoryData['name'],
                'description' => $categoryData['description'] ?? null,
                'prize_amount' => $categoryData['prize_amount'] ?? null,
                'max_submissions_per_user' => $categoryData['max_submissions_per_user'] ?? 1,
                'is_active' => $categoryData['is_active'] ?? true
            ]);

            Log::info('Competition category created', [
                'category_id' => $category->id,
                'competition_id' => $competition->id,
                'name' => $category->name
            ]);

            return [
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ];
        } catch (\Exception $e) {
            Log::error('Error creating category', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update a category
     */
    public function updateCategory(CompetitionCategory $category, array $categoryData)
    {
        try {
            $category->update([
                'name' => $categoryData['name'] ?? $category->name,
                'description' => $categoryData['description'] ?? $category->description,
                'prize_amount' => $categoryData['prize_amount'] ?? $category->prize_amount,
                'max_submissions_per_user' => $categoryData['max_submissions_per_user'] ?? $category->max_submissions_per_user,
                'is_active' => $categoryData['is_active'] ?? $category->is_active
            ]);

            Log::info('Category updated', [
                'category_id' => $category->id,
                'name' => $category->name
            ]);

            return [
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category->fresh()
            ];
        } catch (\Exception $e) {
            Log::error('Error updating category', [
                'category_id' => $category->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a category
     */
    public function deleteCategory(CompetitionCategory $category)
    {
        try {
            $submissionCount = $category->submissions()->count();

            if ($submissionCount > 0) {
                return [
                    'success' => false,
                    'message' => "Cannot delete category with {$submissionCount} submissions. Please reassign or delete submissions first."
                ];
            }

            $categoryName = $category->name;
            $category->delete();

            Log::info('Category deleted', [
                'category_id' => $category->id,
                'name' => $categoryName
            ]);

            return [
                'success' => true,
                'message' => 'Category deleted successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Error deleting category', [
                'category_id' => $category->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all categories for a competition
     */
    public function getCategories(Competition $competition, bool $activeOnly = false)
    {
        $query = CompetitionCategory::where('competition_id', $competition->id)
            ->withCount('submissions');

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        $categories = $query->orderBy('name')->get();

        return [
            'success' => true,
            'data' => $categories,
            'total' => $categories->count()
        ];
    }

    /**
     * Get category details with statistics
     */
    public function getCategoryDetails(CompetitionCategory $category)
    {
        $category->load(['competition', 'submissions']);

        $stats = [
            'total_submissions' => $category->submissions()->count(),
            'approved_submissions' => $category->submissions()->where('status', 'approved')->count(),
            'pending_submissions' => $category->submissions()->where('status', 'pending')->count(),
            'rejected_submissions' => $category->submissions()->where('status', 'rejected')->count(),
            'winners' => $category->submissions()->where('is_winner', true)->count()
        ];

        return [
            'success' => true,
            'data' => [
                'category' => $category,
                'statistics' => $stats
            ]
        ];
    }

    /**
     * Bulk create categories for a competition
     */
    public function bulkCreateCategories(Competition $competition, array $categories)
    {
        DB::beginTransaction();

        try {
            $created = [];
            $failed = [];

            foreach ($categories as $categoryData) {
                if (!isset($categoryData['name'])) {
                    $failed[] = [
                        'data' => $categoryData,
                        'error' => 'Category name is required'
                    ];
                    continue;
                }

                $category = CompetitionCategory::create([
                    'competition_id' => $competition->id,
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'] ?? null,
                    'prize_amount' => $categoryData['prize_amount'] ?? null,
                    'max_submissions_per_user' => $categoryData['max_submissions_per_user'] ?? 1,
                    'is_active' => $categoryData['is_active'] ?? true
                ]);

                $created[] = $category;
            }

            DB::commit();

            Log::info('Bulk category creation', [
                'competition_id' => $competition->id,
                'created_count' => count($created),
                'failed_count' => count($failed)
            ]);

            return [
                'success' => true,
                'message' => "Created " . count($created) . " categories",
                'data' => [
                    'created' => $created,
                    'failed' => $failed,
                    'total' => count($categories)
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error in bulk category creation', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create categories: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Toggle category active status
     */
    public function toggleActiveStatus(CompetitionCategory $category)
    {
        try {
            $category->update(['is_active' => !$category->is_active]);

            Log::info('Category status toggled', [
                'category_id' => $category->id,
                'is_active' => $category->is_active
            ]);

            return [
                'success' => true,
                'message' => 'Category status updated',
                'data' => [
                    'category_id' => $category->id,
                    'is_active' => $category->is_active
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error toggling category status', [
                'category_id' => $category->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update category status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get category leaderboard
     */
    public function getCategoryLeaderboard(CompetitionCategory $category, int $limit = 20)
    {
        $submissions = CompetitionSubmission::where('category_id', $category->id)
            ->where('status', 'approved')
            ->with(['photographer'])
            ->orderBy('final_score', 'desc')
            ->orderBy('public_votes', 'desc')
            ->limit($limit)
            ->get();

        $leaderboard = $submissions->map(function ($submission, $index) {
            return [
                'rank' => $index + 1,
                'submission_id' => $submission->id,
                'photographer_id' => $submission->photographer_id,
                'photographer_name' => $submission->photographer->name,
                'photo_title' => $submission->title,
                'final_score' => $submission->final_score,
                'public_votes' => $submission->public_votes,
                'judge_score' => $submission->judge_score,
                'is_winner' => $submission->is_winner,
                'award_type' => $submission->award_type
            ];
        });

        return [
            'success' => true,
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description
                ],
                'leaderboard' => $leaderboard,
                'total_submissions' => $category->submissions()->where('status', 'approved')->count()
            ]
        ];
    }

    /**
     * Get winners by category for a competition
     */
    public function getWinnersByCategory(Competition $competition)
    {
        $categories = CompetitionCategory::where('competition_id', $competition->id)
            ->with(['submissions' => function ($query) {
                $query->where('is_winner', true)
                    ->with('photographer')
                    ->orderBy('rank');
            }])
            ->get();

        $result = $categories->map(function ($category) {
            return [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'prize_amount' => $category->prize_amount,
                'winners' => $category->submissions->map(function ($submission) {
                    return [
                        'submission_id' => $submission->id,
                        'photographer_name' => $submission->photographer->name,
                        'photo_title' => $submission->title,
                        'rank' => $submission->rank,
                        'award_type' => $submission->award_type,
                        'final_score' => $submission->final_score
                    ];
                })
            ];
        });

        return [
            'success' => true,
            'data' => $result
        ];
    }

    /**
     * Get category statistics for dashboard
     */
    public function getCategoryStatistics(Competition $competition)
    {
        $categories = CompetitionCategory::where('competition_id', $competition->id)
            ->withCount([
                'submissions',
                'submissions as approved_count' => function ($query) {
                    $query->where('status', 'approved');
                },
                'submissions as winner_count' => function ($query) {
                    $query->where('is_winner', true);
                }
            ])
            ->get();

        $stats = [
            'total_categories' => $categories->count(),
            'active_categories' => $categories->where('is_active', true)->count(),
            'total_submissions' => $categories->sum('submissions_count'),
            'categories' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'is_active' => $category->is_active,
                    'prize_amount' => $category->prize_amount,
                    'total_submissions' => $category->submissions_count,
                    'approved_submissions' => $category->approved_count,
                    'winners' => $category->winner_count
                ];
            })
        ];

        return [
            'success' => true,
            'data' => $stats
        ];
    }
}
