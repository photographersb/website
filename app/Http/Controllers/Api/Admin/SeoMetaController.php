<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\SeoMeta;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class SeoMetaController extends Controller
{
    use ApiResponse;
    private const DEFAULT_SCAN_TYPES = ['Photographer', 'Competition', 'Event', 'Album'];
    private const MODEL_TYPE_MAP = [
        'Photographer' => \App\Models\Photographer::class,
        'User' => \App\Models\User::class,
        'Competition' => \App\Models\Competition::class,
        'Event' => \App\Models\Event::class,
        'Album' => \App\Models\Album::class,
    ];
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

        $meta = SeoMeta::whereIn('model_type', $this->getModelTypeCandidates($validated['model_type']))
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
            $modelType = $this->normalizeModelType($validated['model_type']);
            $meta = SeoMeta::whereIn('model_type', $this->getModelTypeCandidates($validated['model_type']))
                ->where('model_id', $validated['model_id'])
                ->first();

            $payload = array_merge(
                array_filter($validated, fn($value) => $value !== null),
                [
                    'model_type' => $modelType,
                    'updated_by' => auth()->id(),
                    'is_auto_generated' => false,
                ]
            );

            if ($meta) {
                $meta->update($payload);
            } else {
                $meta = SeoMeta::create($payload);
            }

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
            $modelClass = $this->resolveModelClass($validated['model_type']);
            
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

        $meta = SeoMeta::whereIn('model_type', $this->getModelTypeCandidates($validated['model_type']))
            ->where('model_id', $validated['model_id'])
            ->first();

        $modelClass = $this->resolveModelClass($validated['model_type']);
        $model = class_exists($modelClass)
            ? $modelClass::find($validated['model_id'])
            : null;

        $fallback = $model ? $this->buildPreviewFromModel($model, $validated['model_type']) : [
            'title' => 'Unknown entity',
            'description' => 'Preview unavailable',
            'url' => config('app.url'),
            'og_image' => null,
        ];

        $data = [
            'title' => $meta?->meta_title ?: $fallback['title'],
            'description' => $meta?->meta_description
                ? $this->limitDescription($meta->meta_description)
                : $fallback['description'],
            'url' => $meta?->canonical_url ?: $fallback['url'],
            'og_image' => $meta?->og_image ?: ($fallback['og_image'] ?? null),
        ];

        if (isset($fallback['note'])) {
            $data['note'] = $fallback['note'];
        }
        if (isset($fallback['requires_auth'])) {
            $data['requires_auth'] = $fallback['requires_auth'];
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Bulk auto-generate SEO meta for all entities of specified types
     */
    public function bulkGenerate(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $types = $request->input('model_types');
        $types = is_array($types) && count($types) > 0 ? $types : self::DEFAULT_SCAN_TYPES;
        $generated = [];
        $skipped = [];

        foreach ($types as $type) {
            $modelClass = $this->resolveModelClass($type);
            if (!class_exists($modelClass) || !is_subclass_of($modelClass, Model::class)) {
                continue;
            }

            // Get all models of this type that don't have SEO meta yet
            $modelIds = SeoMeta::whereIn('model_type', $this->getModelTypeCandidates($type))
                ->pluck('model_id')
                ->all();

            $query = $modelClass::query();
            if (count($modelIds) > 0) {
                $query->whereNotIn('id', $modelIds);
            }
            $modelsWithoutMeta = $query->get();

            foreach ($modelsWithoutMeta as $model) {
                try {
                    if (method_exists($model, 'generateSeoMeta')) {
                        $model->generateSeoMeta();
                        $generated[] = [
                            'model_type' => $type,
                            'model_id' => $model->id,
                            'success' => true,
                        ];
                    } else {
                        $skipped[] = [
                            'model_type' => $type,
                            'model_id' => $model->id,
                            'reason' => 'Model does not support SEO meta',
                        ];
                    }
                } catch (\Exception $e) {
                    $skipped[] = [
                        'model_type' => $type,
                        'model_id' => $model->id,
                        'reason' => $e->getMessage(),
                    ];
                }
            }
        }

        Log::info('SEO meta bulk auto-generated', [
            'generated' => count($generated),
            'skipped' => count($skipped),
            'types' => $types,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Bulk auto-generation completed',
            'data' => [
                'generated_count' => count($generated),
                'skipped_count' => count($skipped),
                'generated' => array_slice($generated, 0, 10),
                'skipped' => array_slice($skipped, 0, 10),
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Scan SEO coverage for connected entities
     */
    public function scan(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $types = $request->input('model_types');
        $types = is_array($types) && count($types) > 0 ? $types : self::DEFAULT_SCAN_TYPES;
        $results = [];

        foreach ($types as $type) {
            $modelClass = $this->resolveModelClass($type);
            if (!class_exists($modelClass) || !is_subclass_of($modelClass, Model::class)) {
                continue;
            }

            $total = $modelClass::count();
            $metaQuery = SeoMeta::whereIn('model_type', $this->getModelTypeCandidates($type));
            $metaCount = (clone $metaQuery)->count();

            $missingTitle = (clone $metaQuery)
                ->where(function ($q) {
                    $q->whereNull('meta_title')->orWhere('meta_title', '');
                })
                ->count();
            $missingDescription = (clone $metaQuery)
                ->where(function ($q) {
                    $q->whereNull('meta_description')->orWhere('meta_description', '');
                })
                ->count();
            $missingCanonical = (clone $metaQuery)
                ->where(function ($q) {
                    $q->whereNull('canonical_url')->orWhere('canonical_url', '');
                })
                ->count();
            $missingOgImage = (clone $metaQuery)
                ->where(function ($q) {
                    $q->whereNull('og_image')->orWhere('og_image', '');
                })
                ->count();

            $metaIds = (clone $metaQuery)->limit(5000)->pluck('model_id')->all();
            $missingModels = $total > 0
                ? $modelClass::whereNotIn('id', $metaIds)->limit(3)->get()
                : collect();

            $samples = $missingModels->map(function ($model) use ($type) {
                $preview = $this->buildPreviewFromModel($model, $type);
                return array_merge($preview, ['model_id' => $model->id]);
            })->values();

            $results[] = [
                'model_type' => $type,
                'total' => $total,
                'meta_records' => $metaCount,
                'missing_meta' => max($total - $metaCount, 0),
                'missing_title' => $missingTitle,
                'missing_description' => $missingDescription,
                'missing_canonical' => $missingCanonical,
                'missing_og_image' => $missingOgImage,
                'missing_samples' => $samples,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'generated_at' => now()->toDateTimeString(),
                'items' => $results,
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

        SeoMeta::whereIn('model_type', $this->getModelTypeCandidates($validated['model_type']))
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

    private function normalizeModelType(string $value): string
    {
        $normalized = self::MODEL_TYPE_MAP[$value] ?? null;
        if ($normalized && class_exists($normalized)) {
            return $normalized;
        }

        if (class_exists($value) && is_subclass_of($value, Model::class)) {
            return $value;
        }

        $classGuess = 'App\\Models\\' . Str::studly($value);
        return class_exists($classGuess) ? $classGuess : $value;
    }

    private function resolveModelClass(string $value): string
    {
        return $this->normalizeModelType($value);
    }

    private function getModelTypeCandidates(string $value): array
    {
        $candidates = [$value, $this->normalizeModelType($value)];
        $classGuess = 'App\\Models\\' . Str::studly($value);
        if (class_exists($classGuess)) {
            $candidates[] = $classGuess;
        }

        return array_values(array_unique($candidates));
    }

    private function buildPreviewFromModel($model, string $modelType): array
    {
        $baseUrl = config('app.url');

        if ($model instanceof \App\Models\User) {
            $photographer = $model->photographer;
            $name = $model->name ?? 'Photographer';
            $city = $photographer?->city?->name ?? 'Bangladesh';
            $username = $model->username;
            $title = trim("{$name} | Photographer SB");
            $description = "Hire {$name}, a photographer in {$city}. View portfolio, packages, and reviews on Photographer SB.";
            $url = $username ? url("/@{$username}") : $baseUrl;
            $image = $model->profile_photo_url ?: ($photographer?->profile_picture ?: asset('images/PhotographerSB-OG.jpg'));

            return [
                'title' => $title,
                'description' => $this->limitDescription($description),
                'url' => $url,
                'og_image' => $image,
            ];
        }

        if ($model instanceof \App\Models\Photographer) {
            $user = $model->user;
            $name = $user?->name ?? $model->business_name ?? 'Photographer';
            $city = $model->city?->name ?? 'Bangladesh';
            $username = $user?->username;
            $title = trim("{$name} | Photographer SB");
            $description = "Hire {$name}, a photographer in {$city}. Browse portfolio, packages, and reviews on Photographer SB.";
            $url = $username ? url("/@{$username}") : $baseUrl;
            $image = $model->profile_picture ?: ($user?->profile_photo_url ?: asset('images/PhotographerSB-OG.jpg'));

            return [
                'title' => $title,
                'description' => $this->limitDescription($description),
                'url' => $url,
                'og_image' => $image,
            ];
        }

        if ($model instanceof \App\Models\Competition) {
            $title = trim("{$model->title} | Photography Competition | Photographer SB");
            $description = $model->description ? strip_tags($model->description) : 'Join the competition on Photographer SB.';
            $url = $model->slug ? url("/competitions/{$model->slug}") : $baseUrl;
            $image = $model->cover_image ?: ($model->banner_image ?: ($model->hero_image ?: asset('images/PhotographerSB-OG.jpg')));

            return [
                'title' => $title,
                'description' => $this->limitDescription($description),
                'url' => $url,
                'og_image' => $image,
            ];
        }

        if ($model instanceof \App\Models\Event) {
            $title = trim("{$model->title} | Photography Event | Photographer SB");
            $description = $model->description ? strip_tags($model->description) : 'Discover events on Photographer SB.';
            $url = $model->slug ? url("/events/{$model->slug}") : $baseUrl;
            $image = $model->hero_image_url ?: ($model->banner_image ?: ($model->og_image ?: asset('images/PhotographerSB-OG.jpg')));

            return [
                'title' => $title,
                'description' => $this->limitDescription($description),
                'url' => $url,
                'og_image' => $image,
            ];
        }

        if ($model instanceof \App\Models\Album) {
            $photographer = $model->photographer;
            $photographerName = $photographer?->user?->name ?? $photographer?->business_name ?? 'Photographer';
            $title = trim("{$model->name} | Client Gallery | Photographer SB");
            $description = $model->description ?: "Private gallery shared by {$photographerName}.";
            $url = url("/client/galleries/{$model->id}");
            $image = $model->cover_photo_url ?: ($photographer?->profile_picture ?: asset('images/PhotographerSB-OG.jpg'));

            return [
                'title' => $title,
                'description' => $this->limitDescription($description),
                'url' => $url,
                'og_image' => $image,
                'note' => 'Private gallery (client login required).',
                'requires_auth' => true,
            ];
        }

        $label = Str::headline(class_basename($modelType));
        return [
            'title' => "{$label} | Photographer SB",
            'description' => 'Preview not available for this entity type.',
            'url' => $baseUrl,
            'og_image' => null,
        ];
    }

    private function limitDescription(string $text): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', $text));
        if ($clean === '') {
            return 'No description available.';
        }

        return Str::limit($clean, 160, '');
    }
}
