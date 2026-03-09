<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Category;
use App\Models\CommunityDiscussion;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\Event;
use App\Models\Location;
use App\Models\Photo;
use App\Models\Photographer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DiscoverController extends Controller
{
    use ApiResponse;

    public function hub(Request $request)
    {
        $limit = min((int) $request->input('limit', 12), 24);
        $user = $request->user('sanctum') ?? $request->user();
        $userId = $user?->id ?? 0;

        $cacheKey = 'discover:hub:' . md5(json_encode([
            'limit' => $limit,
            'user_id' => $userId,
        ]));

        $payload = Cache::remember($cacheKey, 300, function () use ($request, $limit, $user) {
            $photographers = $this->fetchTrendingPhotographers($request, $limit, false);
            $photos = $this->fetchTrendingPhotos($request, $limit);
            $competitions = $this->fetchTrendingCompetitions($request, $limit, false);
            $events = $this->fetchTrendingEvents($request, $limit, true, false);
            $categories = $this->fetchPopularCategories($limit);
            $locations = $this->fetchPopularLocations($limit);
            $featuredCommunityPosts = Schema::hasTable('community_discussions')
                ? CommunityDiscussion::query()
                    ->with('user:id,name,username')
                    ->where('status', 'active')
                    ->where('is_featured', true)
                    ->latest('last_activity_at')
                    ->limit(6)
                    ->get(['id', 'user_id', 'title', 'category', 'tags', 'likes_count', 'comments_count', 'shares_count', 'last_activity_at', 'created_at'])
                : collect();

            $personalized = null;
            if ($user) {
                $personalized = $this->fetchPersonalizedSuggestions($user, $limit);
            }

            return [
                'trending_photographers' => $photographers,
                'trending_photos' => $photos,
                'trending_competitions' => $competitions,
                'upcoming_events' => $events,
                'popular_categories' => $categories,
                'popular_locations' => $locations,
                'featured_community_posts' => $featuredCommunityPosts,
                'personalized' => $personalized,
                'generated_at' => now()->toIso8601String(),
            ];
        });

        return $this->success($payload, 'Discovery hub data retrieved successfully');
    }

    public function photographers(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 15), 50);

        $query = $this->buildPhotographerTrendingQuery();

        if ($request->filled('city')) {
            $citySlug = (string) $request->input('city');
            $query->whereHas('city', fn ($cityQuery) => $cityQuery->where('slug', $citySlug));
        }

        if ($request->filled('category')) {
            $categorySlug = (string) $request->input('category');
            $query->whereHas('categories', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            if (mb_strlen($term) >= 2) {
                $query->where(function ($searchQuery) use ($term) {
                    $searchQuery
                        ->whereHas('user', fn ($userQuery) => $userQuery
                            ->where('name', 'like', "%{$term}%")
                            ->orWhere('username', 'like', "%{$term}%"))
                        ->orWhereHas('city', fn ($cityQuery) => $cityQuery->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('categories', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$term}%"));
                });
            }
        }

        $items = $query->paginate($perPage);

        return $this->paginated($items, 'Discovery photographers retrieved successfully');
    }

    public function photos(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 24), 60);
        $page = max((int) $request->input('page', 1), 1);
        $source = (string) $request->input('source', 'all');

        $cacheKey = 'discover:photos:' . md5(json_encode([
            'per_page' => $perPage,
            'page' => $page,
            'source' => $source,
        ]));

        $result = Cache::remember($cacheKey, 240, function () use ($request, $perPage, $page, $source) {
            $items = $this->fetchTrendingPhotos($request, 120, $source);
            $paginator = $this->paginateCollection($items, $perPage, $page, $request);

            return [
                'items' => $paginator->items(),
                'pagination' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                ],
            ];
        });

        return $this->success($result['items'], 'Discovery photos retrieved successfully', 200, [
            'pagination' => $result['pagination'],
        ]);
    }

    public function competitions(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 12), 40);

        $query = $this->buildCompetitionTrendingQuery();

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            if (mb_strlen($term) >= 2) {
                $query->where(function ($searchQuery) use ($term) {
                    $searchQuery
                        ->where('title', 'like', "%{$term}%")
                        ->orWhere('theme', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            }
        }

        if ($request->filled('category')) {
            $categorySlug = (string) $request->input('category');
            $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
        }

        $items = $query->paginate($perPage);

        return $this->paginated($items, 'Discovery competitions retrieved successfully');
    }

    public function events(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 12), 40);

        $query = $this->buildEventTrendingQuery(true);

        if ($request->filled('city')) {
            $citySlug = (string) $request->input('city');
            $query->whereHas('city', fn ($cityQuery) => $cityQuery->where('slug', $citySlug));
        }

        if ($request->filled('category')) {
            $categorySlug = (string) $request->input('category');
            $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            if (mb_strlen($term) >= 2) {
                $query->where(function ($searchQuery) use ($term) {
                    $searchQuery
                        ->where('title', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhere('venue_name', 'like', "%{$term}%");
                });
            }
        }

        $items = $query->paginate($perPage);

        return $this->paginated($items, 'Discovery events retrieved successfully');
    }

    public function search(Request $request)
    {
        $term = trim((string) $request->input('q', ''));
        if (mb_strlen($term) < 2) {
            return $this->success([
                'photographers' => [],
                'photos' => [],
                'competitions' => [],
                'events' => [],
                'categories' => [],
                'cities' => [],
            ], 'Search term is too short');
        }

        $citySlug = $request->input('city');
        $categorySlug = $request->input('category');
        $type = $request->input('type', 'all');

        $result = [
            'photographers' => [],
            'photos' => [],
            'competitions' => [],
            'events' => [],
            'categories' => [],
            'cities' => [],
        ];

        if (in_array($type, ['all', 'photographers'], true)) {
            $query = $this->buildPhotographerTrendingQuery();
            $query->where(function ($searchQuery) use ($term) {
                $searchQuery
                    ->whereHas('user', fn ($userQuery) => $userQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('username', 'like', "%{$term}%"))
                    ->orWhereHas('city', fn ($cityQuery) => $cityQuery->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('categories', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$term}%"));
            });

            if ($citySlug) {
                $query->whereHas('city', fn ($cityQuery) => $cityQuery->where('slug', $citySlug));
            }

            if ($categorySlug) {
                $query->whereHas('categories', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
            }

            $result['photographers'] = $query->limit(8)->get();
        }

        if (in_array($type, ['all', 'photos'], true)) {
            $result['photos'] = $this->fetchTrendingPhotos($request, 30)
                ->filter(function (array $item) use ($term) {
                    $haystack = strtolower(implode(' ', [
                        $item['title'] ?? '',
                        $item['photographer_name'] ?? '',
                        $item['category'] ?? '',
                    ]));

                    return str_contains($haystack, strtolower($term));
                })
                ->values()
                ->take(12);
        }

        if (in_array($type, ['all', 'competitions'], true)) {
            $competitionQuery = $this->buildCompetitionTrendingQuery();
            $competitionQuery->where(function ($searchQuery) use ($term) {
                $searchQuery
                    ->where('title', 'like', "%{$term}%")
                    ->orWhere('theme', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
            if ($categorySlug) {
                $competitionQuery->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
            }

            $result['competitions'] = $competitionQuery->limit(8)->get();
        }

        if (in_array($type, ['all', 'events'], true)) {
            $eventQuery = $this->buildEventTrendingQuery(false);
            $eventQuery->where(function ($searchQuery) use ($term) {
                $searchQuery
                    ->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('venue_name', 'like', "%{$term}%");
            });
            if ($citySlug) {
                $eventQuery->whereHas('city', fn ($cityQuery) => $cityQuery->where('slug', $citySlug));
            }
            if ($categorySlug) {
                $eventQuery->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
            }

            $result['events'] = $eventQuery->limit(8)->get();
        }

        if (in_array($type, ['all', 'categories'], true)) {
            $result['categories'] = Category::query()
                ->where('is_active', true)
                ->where('name', 'like', "%{$term}%")
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name', 'slug', 'icon']);
        }

        if (in_array($type, ['all', 'cities'], true)) {
            $result['cities'] = Location::query()
                ->where('is_active', true)
                ->where('name', 'like', "%{$term}%")
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name', 'slug', 'type']);
        }

        return $this->success($result, 'Discovery search results retrieved successfully');
    }

    public function personalized(Request $request)
    {
        $user = $request->user('sanctum') ?? $request->user();
        if (!$user) {
            return $this->success([
                'photographers_in_city' => [],
                'events_near_you' => [],
                'competitions_for_you' => [],
                'context' => null,
            ], 'User is not authenticated');
        }

        $limit = min((int) $request->input('limit', 8), 20);
        $payload = $this->fetchPersonalizedSuggestions($user, $limit);

        return $this->success($payload, 'Personalized discovery suggestions retrieved successfully');
    }

    private function fetchTrendingPhotographers(Request $request, int $limit, bool $useCache = true): Collection
    {
        $cacheKey = 'discover:photographers:' . md5(json_encode([
            'limit' => $limit,
            'city' => $request->input('city'),
            'category' => $request->input('category'),
        ]));

        $resolver = function () use ($limit, $request) {
            $query = $this->buildPhotographerTrendingQuery();

            if ($request->filled('city')) {
                $citySlug = (string) $request->input('city');
                $query->whereHas('city', fn ($cityQuery) => $cityQuery->where('slug', $citySlug));
            }

            if ($request->filled('category')) {
                $categorySlug = (string) $request->input('category');
                $query->whereHas('categories', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
            }

            return $query->limit($limit)->get();
        };

        return $useCache ? Cache::remember($cacheKey, 300, $resolver) : $resolver();
    }

    private function buildPhotographerTrendingQuery()
    {
        $since30 = now()->subDays(30);
        $since45 = now()->subDays(45);

        return Photographer::query()
            ->publicVisible()
            ->with([
                'user:id,name,username',
                'city:id,name,slug',
                'categories:id,name,slug,icon',
                'trustScore:id,photographer_id,trust_badge,overall_score',
            ])
            ->select([
                'photographers.id',
                'photographers.user_id',
                'photographers.slug',
                'photographers.profile_picture',
                'photographers.city_id',
                'photographers.average_rating',
                'photographers.rating_count',
                'photographers.total_bookings',
                'photographers.is_verified',
                'photographers.updated_at',
            ])
            ->selectRaw('(
                COALESCE((SELECT COUNT(*) FROM photos WHERE photos.photographer_id = photographers.id AND photos.created_at >= ?), 0) * 5 +
                COALESCE((SELECT COUNT(*) FROM competition_submissions cs WHERE cs.photographer_id = photographers.user_id AND cs.created_at >= ?), 0) * 6 +
                COALESCE((SELECT COUNT(*) FROM reviews WHERE reviews.photographer_id = photographers.id AND reviews.published_at >= ?), 0) * 4 +
                COALESCE((SELECT COUNT(*) FROM bookings WHERE bookings.photographer_id = photographers.id AND bookings.created_at >= ?), 0) * 3 +
                CASE WHEN photographers.updated_at >= ? THEN 6 ELSE 0 END +
                CASE WHEN photographers.is_verified = 1 THEN 8 ELSE 0 END
            ) as discovery_score', [$since30, $since30, $since45, $since30, $since30])
            ->orderByDesc('discovery_score')
            ->orderByDesc('photographers.updated_at');
    }

    private function fetchTrendingPhotos(Request $request, int $limit, string $source = 'all'): Collection
    {
        $cacheKey = 'discover:photos:mixed:' . md5(json_encode([
            'limit' => $limit,
            'source' => $source,
        ]));

        return Cache::remember($cacheKey, 240, function () use ($limit, $source) {
            $items = collect();

            if (in_array($source, ['all', 'competition_submissions'], true)) {
                $competitionPhotos = CompetitionSubmission::query()
                    ->approved()
                    ->with(['competition:id,title,slug,theme,category_id', 'competition.category:id,name', 'photographer:id,name'])
                    ->select([
                        'id',
                        'competition_id',
                        'photographer_id',
                        'title',
                        'image_url',
                        'thumbnail_url',
                        'vote_count',
                        'view_count',
                        'created_at',
                    ])
                    ->orderByDesc('vote_count')
                    ->orderByDesc('created_at')
                    ->limit($limit)
                    ->get()
                    ->map(function (CompetitionSubmission $item) {
                        $score = ($item->vote_count * 5) + ($item->view_count * 0.25) + ($item->created_at?->gt(now()->subDays(7)) ? 12 : 0);

                        return [
                            'id' => "submission-{$item->id}",
                            'source' => 'competition_submission',
                            'source_id' => $item->id,
                            'image_url' => $item->image_url,
                            'thumbnail_url' => $item->thumbnail_url ?: $item->image_url,
                            'title' => $item->title,
                            'photographer_name' => $item->photographer?->name ?? 'Anonymous',
                            'category' => $item->competition?->category?->name ?? ($item->competition?->theme ?? 'Competition'),
                            'likes_or_votes' => (int) ($item->vote_count ?? 0),
                            'score' => $score,
                            'detail_url' => $item->competition?->slug
                                ? "/competitions/{$item->competition->slug}/submissions/{$item->id}"
                                : null,
                            'created_at' => $item->created_at?->toIso8601String(),
                        ];
                    });

                $items = $items->concat($competitionPhotos);
            }

            if (in_array($source, ['all', 'portfolio_uploads', 'featured'], true)) {
                $portfolioPhotos = Photo::query()
                    ->with(['photographer:id,user_id,slug', 'photographer.user:id,name'])
                    ->select([
                        'id',
                        'photographer_id',
                        'title',
                        'image_url',
                        'thumbnail_url',
                        'view_count',
                        'is_featured',
                        'created_at',
                    ])
                    ->where(function ($query) use ($source) {
                        if ($source === 'featured') {
                            $query->where('is_featured', true);
                            return;
                        }

                        $query->where('is_featured', true)
                            ->orWhere('created_at', '>=', now()->subDays(45));
                    })
                    ->orderByDesc('is_featured')
                    ->orderByDesc('view_count')
                    ->orderByDesc('created_at')
                    ->limit($limit)
                    ->get()
                    ->map(function (Photo $item) {
                        $featuredBoost = $item->is_featured ? 20 : 0;
                        $score = ($item->view_count * 0.4) + $featuredBoost + ($item->created_at?->gt(now()->subDays(10)) ? 8 : 0);

                        return [
                            'id' => "portfolio-{$item->id}",
                            'source' => $item->is_featured ? 'featured_photo' : 'portfolio_upload',
                            'source_id' => $item->id,
                            'image_url' => $item->image_url,
                            'thumbnail_url' => $item->thumbnail_url ?: $item->image_url,
                            'title' => $item->title,
                            'photographer_name' => $item->photographer?->user?->name ?? 'Photographer',
                            'category' => 'Portfolio',
                            'likes_or_votes' => (int) ($item->view_count ?? 0),
                            'score' => $score,
                            'detail_url' => $item->photographer?->slug ? "/@{$item->photographer->slug}" : null,
                            'created_at' => $item->created_at?->toIso8601String(),
                        ];
                    });

                $items = $items->concat($portfolioPhotos);
            }

            return $items
                ->sortByDesc('score')
                ->unique('id')
                ->take($limit)
                ->values();
        });
    }

    private function fetchTrendingCompetitions(Request $request, int $limit, bool $useCache = true): Collection
    {
        $cacheKey = 'discover:competitions:' . md5(json_encode([
            'limit' => $limit,
            'category' => $request->input('category'),
        ]));

        $resolver = function () use ($request, $limit) {
            $query = $this->buildCompetitionTrendingQuery();

            if ($request->filled('category')) {
                $categorySlug = (string) $request->input('category');
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
            }

            return $query->limit($limit)->get();
        };

        return $useCache ? Cache::remember($cacheKey, 300, $resolver) : $resolver();
    }

    private function buildCompetitionTrendingQuery()
    {
        $since14 = now()->subDays(14);

        return Competition::query()
            ->published()
            ->with(['category:id,name,slug'])
            ->withCount([
                'submissions as submissions_count',
                'votes as votes_count',
                'sponsorRecords as sponsors_count' => fn ($q) => $q->where('competition_sponsors.is_active', true),
            ])
            ->select([
                'id',
                'slug',
                'title',
                'theme',
                'cover_image',
                'banner_image',
                'submission_deadline',
                'total_prize_pool',
                'is_featured',
                'category_id',
                'created_at',
            ])
            ->selectRaw('COALESCE((SELECT COUNT(*) FROM competition_submissions cs WHERE cs.competition_id = competitions.id AND cs.created_at >= ?), 0) as recent_submissions_count', [$since14])
            ->selectRaw('(
                COALESCE((SELECT COUNT(*) FROM competition_submissions cs WHERE cs.competition_id = competitions.id), 0) * 3 +
                COALESCE((SELECT COUNT(*) FROM competition_submissions cs WHERE cs.competition_id = competitions.id AND cs.created_at >= ?), 0) * 4 +
                COALESCE((SELECT COUNT(*) FROM competition_votes cv WHERE cv.competition_id = competitions.id), 0) * 2 +
                COALESCE((SELECT COUNT(*) FROM competition_sponsors cps WHERE cps.competition_id = competitions.id AND cps.is_active = 1), 0) * 5 +
                CASE WHEN competitions.is_featured = 1 THEN 10 ELSE 0 END
            ) as discovery_score', [$since14])
            ->orderByDesc('discovery_score')
            ->orderByDesc('created_at');
    }

    private function fetchTrendingEvents(Request $request, int $limit, bool $upcomingOnly = true, bool $useCache = true): Collection
    {
        $cacheKey = 'discover:events:' . md5(json_encode([
            'limit' => $limit,
            'upcoming' => $upcomingOnly,
            'city' => $request->input('city'),
            'category' => $request->input('category'),
        ]));

        $resolver = function () use ($request, $limit, $upcomingOnly) {
            $query = $this->buildEventTrendingQuery($upcomingOnly);

            if ($request->filled('city')) {
                $citySlug = (string) $request->input('city');
                $query->whereHas('city', fn ($cityQuery) => $cityQuery->where('slug', $citySlug));
            }

            if ($request->filled('category') && Schema::hasColumn('events', 'category_id')) {
                $categorySlug = (string) $request->input('category');
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
            }

            return $query->limit($limit)->get();
        };

        return $useCache ? Cache::remember($cacheKey, 300, $resolver) : $resolver();
    }

    private function buildEventTrendingQuery(bool $upcomingOnly = true)
    {
        $since14 = now()->subDays(14);
        $hasEventCategory = Schema::hasColumn('events', 'category_id');

        $selectColumns = [
            'id',
            'slug',
            'title',
            'event_date',
            'city_id',
            'hero_image_url',
            'banner_image',
            'is_featured',
            'created_at',
        ];

        if ($hasEventCategory) {
            $selectColumns[] = 'category_id';
        }

        $query = Event::query()
            ->published()
            ->with(['city:id,name,slug'])
            ->when($hasEventCategory, function ($query) {
                $query->with('category:id,name,slug');
            })
            ->withCount(['registrations as registration_count'])
            ->select($selectColumns)
            ->selectRaw('COALESCE((SELECT COUNT(*) FROM event_rsvps er WHERE er.event_id = events.id AND er.created_at >= ?), 0) as recent_registration_count', [$since14])
            ->selectRaw('(
                COALESCE((SELECT COUNT(*) FROM event_rsvps er WHERE er.event_id = events.id), 0) * 4 +
                COALESCE((SELECT COUNT(*) FROM event_rsvps er WHERE er.event_id = events.id AND er.created_at >= ?), 0) * 5 +
                CASE WHEN events.is_featured = 1 THEN 10 ELSE 0 END
            ) as discovery_score', [$since14]);

        if ($upcomingOnly) {
            $query->whereDate('event_date', '>=', now()->toDateString());
        }

        return $query
            ->orderByDesc('discovery_score')
            ->orderBy('event_date');
    }

    private function fetchPopularCategories(int $limit): Collection
    {
        return Cache::remember("discover:categories:{$limit}", 600, function () use ($limit) {
            return Category::query()
                ->where('is_active', true)
                ->select(['id', 'name', 'slug', 'icon', 'description'])
                ->withCount('photographers')
                ->orderByDesc('photographers_count')
                ->orderBy('name')
                ->limit($limit)
                ->get()
                ->map(function (Category $category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'icon' => $category->icon,
                        'description' => $category->description,
                        'photographers_count' => (int) $category->photographers_count,
                    ];
                });
        });
    }

    private function fetchPopularLocations(int $limit): Collection
    {
        return Cache::remember("discover:locations:{$limit}", 600, function () use ($limit) {
            return Location::query()
                ->where('is_active', true)
                ->select(['id', 'name', 'slug', 'type'])
                ->withCount(['photographers', 'events'])
                ->orderByRaw('(photographers_count * 4 + events_count * 3) DESC')
                ->orderBy('name')
                ->limit($limit)
                ->get()
                ->map(function (Location $location) {
                    return [
                        'id' => $location->id,
                        'name' => $location->name,
                        'slug' => $location->slug,
                        'type' => $location->type,
                        'photographers_count' => (int) $location->photographers_count,
                        'events_count' => (int) $location->events_count,
                    ];
                });
        });
    }

    private function fetchPersonalizedSuggestions($user, int $limit): array
    {
        $photographerProfile = $user->photographer()->with('categories:id,name,slug')->first();
        $cityId = $photographerProfile?->city_id;
        $categorySlugs = $photographerProfile
            ? $photographerProfile->categories->pluck('slug')->filter()->values()->all()
            : [];

        $context = [
            'city_id' => $cityId,
            'city' => $photographerProfile?->city?->name,
            'categories' => $categorySlugs,
        ];

        $photographersQuery = $this->buildPhotographerTrendingQuery();
        if ($cityId) {
            $photographersQuery->where('city_id', $cityId);
        }
        if (!empty($categorySlugs)) {
            $photographersQuery->whereHas('categories', fn ($categoryQuery) => $categoryQuery->whereIn('slug', $categorySlugs));
        }

        $eventsQuery = $this->buildEventTrendingQuery(true);
        if ($cityId) {
            $eventsQuery->where('city_id', $cityId);
        }

        $competitionsQuery = $this->buildCompetitionTrendingQuery();
        if (!empty($categorySlugs)) {
            $competitionsQuery->whereHas('category', fn ($categoryQuery) => $categoryQuery->whereIn('slug', $categorySlugs));
        }

        return [
            'photographers_in_city' => $photographersQuery->limit($limit)->get(),
            'events_near_you' => $eventsQuery->limit($limit)->get(),
            'competitions_for_you' => $competitionsQuery->limit($limit)->get(),
            'context' => $context,
        ];
    }

    private function paginateCollection(Collection $items, int $perPage, int $page, Request $request): LengthAwarePaginator
    {
        $total = $items->count();
        $offset = ($page - 1) * $perPage;
        $sliced = $items->slice($offset, $perPage)->values();

        return new LengthAwarePaginator(
            $sliced,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
