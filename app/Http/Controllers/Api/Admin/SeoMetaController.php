<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\SeoMeta;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SeoMetaController extends Controller
{
    use ApiResponse;
    /**
     * Get all SEO meta records for admin browser
     */
    public function index(Request $request)
    {
        try {
            // Check admin role
            if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $meta = SeoMeta::with(['creator', 'updater'])
                ->orderBy('updated_at', 'desc')
                ->limit(100)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $meta
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load SEO meta records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get SEO meta for an entity
     */
    public function show(Request $request)
    {
        $validated = $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        $meta = SeoMeta::where('model_type', $validated['model_type'])
            ->where('model_id', $validated['model_id'])
            ->first();

        if (!$meta) {
            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'No SEO meta found. Auto-generation available.',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $meta,
        ]);
    }

    /**
     * Update or create SEO meta
     */
    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|url',
            'og_image_credit_name' => 'nullable|string|max:255',
            'og_image_credit_url' => 'nullable|url|max:255',
            'twitter_card' => 'nullable|in:summary,summary_large_image,app,player',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|url',
            'twitter_image_credit_name' => 'nullable|string|max:255',
            'twitter_image_credit_url' => 'nullable|url|max:255',
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'robots_snippet' => 'nullable|string|max:255',
            'schema_json' => 'nullable|json',
        ]);

        try {
            $meta = SeoMeta::updateOrCreate(
                [
                    'model_type' => $validated['model_type'],
                    'model_id' => $validated['model_id'],
                ],
                array_merge(
                    array_filter($validated, fn($value) => $value !== null),
                    [
                        'updated_by' => auth()->id(),
                        'is_auto_generated' => false,
                    ]
                )
            );

            Log::info('SEO meta updated', [
                'model_type' => $validated['model_type'],
                'model_id' => $validated['model_id'],
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'SEO meta updated successfully',
                'data' => $meta,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update SEO meta: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update SEO meta',
            ], 500);
        }
    }

    /**
     * Auto-generate SEO meta
     */
    public function generate(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        try {
            // Find the model
            $modelClass = 'App\\Models\\' . str($validated['model_type'])->studly();
            
            if (!class_exists($modelClass)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Model type not found',
                ], 404);
            }

            $model = $modelClass::findOrFail($validated['model_id']);

            // Check if model uses HasSeoMeta trait
            if (!method_exists($model, 'generateSeoMeta')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This model does not support SEO meta',
                ], 400);
            }

            $model->generateSeoMeta();
            $meta = $model->seoMeta;

            Log::info('SEO meta auto-generated', [
                'model_type' => $validated['model_type'],
                'model_id' => $validated['model_id'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'SEO meta auto-generated successfully',
                'data' => $meta,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to auto-generate SEO meta: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to auto-generate SEO meta',
            ], 500);
        }
    }

    /**
     * Preview SEO meta as it will appear in search results
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        $meta = SeoMeta::where('model_type', $validated['model_type'])
            ->where('model_id', $validated['model_id'])
            ->first();

        if (!$meta) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'title' => 'No SEO meta found',
                    'description' => 'Auto-generation available',
                    'url' => config('app.url'),
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'title' => $meta->meta_title ?? 'Untitled',
                'description' => substr($meta->meta_description ?? '', 0, 160) ?? 'No description',
                'url' => $meta->canonical_url ?? config('app.url'),
                'og_image' => $meta->og_image,
            ],
        ]);
    }

    /**
     * Delete SEO meta
     */
    public function destroy(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        SeoMeta::where('model_type', $validated['model_type'])
            ->where('model_id', $validated['model_id'])
            ->delete();

        Log::info('SEO meta deleted', [
            'model_type' => $validated['model_type'],
            'model_id' => $validated['model_id'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'SEO meta deleted successfully',
        ]);
    }
}
