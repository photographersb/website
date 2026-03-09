<?php

namespace App\Http\Controllers\Api;

use App\Models\Photographer;
use App\Models\Category;
use App\Models\Location;
use App\Models\EventRsvp;
use App\Models\PhotographerStats;
use App\Models\Inquiry;
use App\Models\CompetitionSubmission;
use App\Models\Certificate;
use App\Models\SubmissionShareFrame;
use App\Models\Referral;
use App\Models\ReferralReward;
use App\Models\CommunityUserBadge;
use App\Models\LearningEnrollment;
use App\Models\LearningInstructorProfile;
use App\Rules\SocialMediaUrl;
use App\Http\Requests\StorePhotographerRequest;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PhotographerController extends Controller
{
    use ApiResponse;

    /**
     * Get all photographers with filters
     */
    public function index(Request $request)
    {
        // Enforce pagination limits to prevent DoS
        $perPage = min($request->get('per_page', 15), 100);
        $page = $request->get('page', 1);

        $query = Photographer::publicVisible()
            ->with(['user:id,name,username', 'city:id,name,slug', 'trustScore', 'categories:id,name,slug,icon'])
            ->select('photographers.*');

        // Search by name, username, location, or category
        if ($request->has('q') && strlen($request->q) >= 2) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                // Search in photographer name/username
                $q->whereHas('user', function ($subQ) use ($searchTerm) {
                    $subQ->where('name', 'LIKE', "%{$searchTerm}%")
                         ->orWhere('username', 'LIKE', "%{$searchTerm}%");
                })
                // Search in city name
                ->orWhereHas('city', function ($subQ) use ($searchTerm) {
                    $subQ->where('name', 'LIKE', "%{$searchTerm}%");
                })
                // Search in category name
                ->orWhereHas('categories', function ($subQ) use ($searchTerm) {
                    $subQ->where('name', 'LIKE', "%{$searchTerm}%");
                });
            });
        }

        // Filter by city
        if ($request->has('city')) {
            $city = Location::where('slug', $request->city)->first();
            if ($city) {
                $query->where('city_id', $city->id);
            }
        }

        // Filter by category
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->whereHas('categories', function ($q) use ($category) {
                    $q->where('categories.id', $category->id);
                });
            }
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating > 0) {
            $query->where('average_rating', '>=', $request->rating);
        }

        // Sort
        $sort = $request->get('sort', 'relevance');
        $featuredScope = $request->get('featured_scope', 'global');
        $now = now();

        if ($featuredScope === 'area' && isset($city) && $city) {
            $query->orderByRaw(
                "CASE WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) AND city_id = ? THEN 2 WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) THEN 1 ELSE 0 END DESC",
                [$now, $city->id, $now]
            );
        } elseif ($featuredScope === 'category' && isset($category) && $category) {
            $query->orderByRaw(
                "CASE WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) AND EXISTS (SELECT 1 FROM photographer_category pc WHERE pc.photographer_id = photographers.id AND pc.category_id = ?) THEN 2 WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) THEN 1 ELSE 0 END DESC",
                [$now, $category->id, $now]
            );
        } else {
            $query->orderByRaw(
                "CASE WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) THEN 1 ELSE 0 END DESC",
                [$now]
            );
        }

        match ($sort) {
            'rating' => $query->orderByRaw('CASE WHEN is_verified = 1 THEN 0 ELSE 1 END')
                             ->orderBy('average_rating', 'desc'),
            'newest' => $query->orderByRaw('CASE WHEN is_verified = 1 THEN 0 ELSE 1 END')
                             ->orderBy('created_at', 'desc'),
            default => $query->orderByRaw('CASE WHEN is_verified = 1 THEN 0 ELSE 1 END')
                            ->orderBy('average_rating', 'desc'),
        };

        $photographers = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginated($photographers, 'Photographers retrieved successfully', 200, [
            'links' => [
                'first' => $photographers->url(1),
                'last' => $photographers->url($photographers->lastPage()),
                'prev' => $photographers->previousPageUrl(),
                'next' => $photographers->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Get single photographer profile
     */
    public function show($photographerSlugOrId)
    {
        // Support both slug and ID
        $photographer = is_numeric($photographerSlugOrId)
            ? Photographer::findOrFail($photographerSlugOrId)
            : Photographer::where('slug', $photographerSlugOrId)->firstOrFail();

        // Track profile view (async, don't wait for it)
        \App\Services\AchievementService::trackProfileView($photographer->id);

        // Eagerly load relationships to avoid N+1
        $photographer->load([
            'user',
            'city',
            'trustScore',
            'categories',
            'albums' => function ($q) {
                $q->where('is_public', true)->orderBy('display_order');
            },
            'albums.photos' => function ($q) {
                $q->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
            },
            'packages' => function ($q) {
                $q->where('is_active', true)->orderBy('display_order');
            },
            'reviews' => function ($q) {
                $q->where('status', 'published')->latest('published_at')->limit(5);
            },
            'reviews.booking', // Eager load booking for reviews
            'reviews.reviewer', // Eager load reviewer (client) for reviews
            'awards' => function ($q) {
                $q->orderBy('year', 'desc')->orderBy('display_order');
            },
        ]);

        // Get competition wins
        $competitionWins = \App\Models\CompetitionSubmission::where('photographer_id', $photographer->id)
            ->where('is_winner', true)
            ->with('competition')
            ->orderByRaw('COALESCE(ranking, 999)')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'competition_title' => $submission->competition->title ?? 'Competition',
                    'position' => $submission->winner_position ?? $submission->ranking ?? 1,
                    'prize_amount' => $submission->prize_amount ?? 0,
                    'submission_title' => $submission->title,
                    'image_url' => $submission->image_url,
                    'created_at' => $submission->created_at,
                ];
            });

        // Get photographer stats and achievements
        $stats = \App\Models\PhotographerStats::where('photographer_id', $photographer->id)->first();
        $achievementSummary = null;
        if ($stats) {
            $unlockedAchievements = \App\Models\PhotographerAchievement::where('photographer_id', $photographer->id)
                ->where('is_unlocked', true)
                ->with('achievement')
                ->orderBy('unlocked_at', 'desc')
                ->limit(5)
                ->get();
            
            $achievementSummary = [
                'level' => $stats->level,
                'total_points' => $stats->total_points,
                'unlocked_count' => $unlockedAchievements->count(),
                'recent_achievements' => $unlockedAchievements->map(function ($pa) {
                    return [
                        'name' => $pa->achievement->name,
                        'icon' => $pa->achievement->icon,
                        'badge_color' => $pa->achievement->badge_color,
                        'points' => $pa->achievement->points,
                        'unlocked_at' => $pa->unlocked_at,
                    ];
                }),
            ];
        }

        $photographerData = $photographer->toArray();
        unset($photographerData['specializations']);
        $photographerData['competition_wins'] = $competitionWins;
        $photographerData['events_joined'] = Schema::hasTable('event_rsvps')
            ? EventRsvp::where('user_id', $photographer->user_id)
                ->where('rsvp_status', 'going')
                ->distinct('event_id')
                ->count('event_id')
            : 0;
        $photographerData['competitions_tried'] = Schema::hasTable('competition_submissions')
            ? \App\Models\CompetitionSubmission::where('photographer_id', $photographer->id)
                ->distinct('competition_id')
                ->count('competition_id')
            : 0;
        $photographerData['awards_won'] = $photographer->awards->count();

        $certifications = Certificate::query()
            ->where('status', 'issued')
            ->where(function ($query) use ($photographer) {
                $query->where('user_id', $photographer->user_id)
                    ->orWhere('issued_to_user_id', $photographer->user_id);
            })
            ->with(['event:id,title', 'competition:id,title', 'template:id,title'])
            ->latest('issued_at')
            ->limit(20)
            ->get()
            ->map(function (Certificate $certificate) {
                return [
                    'id' => $certificate->id,
                    'title' => $certificate->template?->title ?? 'Certificate',
                    'issuing_event' => $certificate->event?->title ?? $certificate->competition?->title,
                    'year' => ($certificate->issued_at ?? $certificate->issue_date)?->format('Y'),
                    'verification_badge' => $certificate->isValid(),
                    'verification_url' => route('certificate.verify', $certificate->certificate_code),
                    'preview_url' => $certificate->png_path ? Storage::url($certificate->png_path) : null,
                ];
            });

        $photographerData['certifications'] = $certifications;
        $photographerData['awards_won'] = $photographerData['awards_won'] + $certifications->count();
        
        // Add stats and achievement data
        $photographerData['stats'] = $stats;
        $photographerData['achievements'] = $achievementSummary;
        $photographerData['starting_price'] = $photographer->packages()->where('is_active', true)->min('base_price') ?? $photographer->starting_price ?? null;
        $photographerData['profile_views'] = $stats ? $stats->profile_views : 0;
        $photographerData['response_rate'] = $stats ? $stats->response_rate : null;
        $photographerData['average_response_time'] = $stats ? $stats->average_response_time : null;
        $photographerData['portfolio_completeness'] = $stats ? $stats->portfolio_completeness : 0;

            $growthBadges = ReferralReward::query()
                ->where('user_id', $photographer->user_id)
                ->where('status', 'achieved')
                ->orderBy('milestone')
                ->get(['milestone', 'badge_name', 'achieved_at']);

            $photographerData['growth_badges'] = $growthBadges;
            $photographerData['community_badges'] = CommunityUserBadge::query()
                ->where('user_id', $photographer->user_id)
                ->with('badge:id,name,code,icon')
                ->latest('awarded_at')
                ->get(['id', 'badge_id', 'awarded_reason', 'awarded_at']);
            $photographerData['learning_stats'] = [
                'active_courses' => LearningEnrollment::query()
                    ->where('user_id', $photographer->user_id)
                    ->where('status', 'enrolled')
                    ->count(),
                'completed_courses' => LearningEnrollment::query()
                    ->where('user_id', $photographer->user_id)
                    ->where('status', 'completed')
                    ->count(),
                'certificates_earned' => LearningEnrollment::query()
                    ->where('user_id', $photographer->user_id)
                    ->whereNotNull('certificate_id')
                    ->count(),
                'instructor_profile' => LearningInstructorProfile::query()
                    ->where('user_id', $photographer->user_id)
                    ->where('is_approved', true)
                    ->first(['id', 'user_id', 'bio', 'expertise', 'student_rating', 'courses_created', 'students_count']),
            ];
            $photographerData['referral_summary'] = [
                'successful_photographer_referrals' => Referral::query()
                    ->where('referrer_user_id', $photographer->user_id)
                    ->where('status', 'successful')
                    ->whereHas('referredUser', function ($query) {
                        $query->whereIn('role', ['photographer', 'studio_owner', 'studio_photographer']);
                    })
                    ->count(),
            ];

        return $this->success($photographerData, 'Photographer profile retrieved successfully');
    }

    /**
     * Get photographer profile by username
     */
    public function showByUsername($username)
    {
        // Find user by username
        $user = \App\Models\User::where('username', $username)->first();
        
        if (!$user || !$user->isPhotographer()) {
            return $this->error('Photographer not found', 404);
        }

        // Get the photographer and call show with the ID
        return $this->show($user->photographer->id);
    }

    /**
     * Search photographers with intelligent matching
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return $this->validationError(['q' => 'Search query must be at least 2 characters'], 'Search query must be at least 2 characters');
        }

        $photographers = Photographer::publicVisible()
            ->with(['city:id,name,slug', 'categories:id,name,slug,icon'])
            ->whereHas('user', function ($q) use ($query) {
                // Prioritize exact matches, then word boundaries, then contains
                $q->where(function($subQ) use ($query) {
                    // Exact match (highest priority)
                    $subQ->where('name', '=', $query)
                         ->orWhere('username', '=', $query);
                })
                ->orWhere(function($subQ) use ($query) {
                    // Starts with (second priority)
                    $subQ->where('name', 'LIKE', "{$query}%")
                         ->orWhere('username', 'LIKE', "{$query}%");
                })
                ->orWhere(function($subQ) use ($query) {
                    // Contains with word boundary (third priority)
                    $subQ->where('name', 'LIKE', "% {$query}%")
                         ->orWhere('name', 'LIKE', "%{$query} %")
                         ->orWhere('username', 'LIKE', "% {$query}%");
                })
                ->orWhere(function($subQ) use ($query) {
                    // Contains anywhere (lowest priority)
                    $subQ->where('name', 'LIKE', "%{$query}%")
                         ->orWhere('username', 'LIKE', "%{$query}%");
                });
            })
            ->select('id', 'user_id', 'city_id', 'slug', 'profile_picture', 'average_rating', 'is_featured')
            ->limit(10)
            ->get();

        return $this->success($photographers, 'Search results retrieved successfully');
    }

    /**
     * Update photographer profile avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,jpg,png|max:5120', // Max 5MB
        ]);

        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        try {
            // Delete old avatar if exists
            $rawPath = $photographer->getRawOriginal('profile_picture');
            if ($rawPath && !str_starts_with($rawPath, 'http')) {
                $trimmed = ltrim($rawPath, '/');
                if (str_starts_with($trimmed, 'storage/')) {
                    $trimmed = substr($trimmed, strlen('storage/'));
                }
                if ($trimmed && Storage::disk('public')->exists($trimmed)) {
                    Storage::disk('public')->delete($trimmed);
                }
            }

            // Store new avatar
            $path = $request->file('avatar')->store('profile_pictures/' . $photographer->id, 'public');

            // Update photographer record
            $photographer->update([
                'profile_picture' => $path,
            ]);

            return $this->success([
                'profile_picture' => Storage::url($path),
                'profile_picture_url' => Storage::url($path),
            ], 'Profile picture updated successfully');
        } catch (\Exception $e) {
            Log::error('Avatar upload failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return $this->error('Failed to upload avatar. Please try again.', 500);
        }
    }

    /**
     * Get photographer dashboard stats and data
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $photographer = $user->photographer;
        $userId = $user->id;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $profileStats = PhotographerStats::firstOrCreate(
            ['photographer_id' => $photographer->id],
            ['profile_views' => 0, 'profile_views_this_month' => 0, 'profile_share_clicks' => 0, 'profile_share_visits' => 0, 'total_points' => 0, 'level' => 1]
        );

        $this->ensureShareCode($photographer);

        // Get dashboard statistics
        $stats = [
            'total_bookings' => $photographer->bookings()->count(),
            'pending_bookings' => $photographer->bookings()->where('status', 'pending')->count(),
            'average_rating' => round($photographer->reviews()->avg('rating') ?? 0, 1),
            'total_revenue' => $photographer->transactions()
                ->where('status', 'completed')
                ->sum('net_amount') ?? 0,
            'profile_views' => $profileStats->profile_views ?? 0,
            'profile_clicks' => Inquiry::where('photographer_id', $photographer->id)->count(),
            'vote_count' => CompetitionSubmission::where('photographer_id', $userId)->sum('vote_count'),
            'share_count' => SubmissionShareFrame::whereHas('submission', function ($q) use ($userId) {
                $q->where('photographer_id', $userId);
            })->sum('generation_count'),
        ];

        // Get recent bookings
        $bookings = $photographer->bookings()
            ->with('client')
            ->latest()
            ->limit(5)
            ->get();

        // Get recent reviews
        $reviews = $photographer->reviews()
            ->with(['reviewer', 'booking'])
            ->where('status', 'published')
            ->latest()
            ->limit(5)
            ->get();

        // Get albums
        $albums = $photographer->albums()
            ->withCount('photos')
            ->orderBy('display_order')
            ->get();

        // Get packages
        $packages = $photographer->packages()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        // Get competition submissions
        $competitionSubmissions = \App\Models\CompetitionSubmission::where(function ($q) use ($userId) {
            $q->where('photographer_id', $userId)
                ->orWhere('user_id', $userId);
            })
            ->with(['competition:id,title,slug,status,submission_deadline,voting_end_at'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get event RSVPs (events user registered for)
        $eventRsvps = \App\Models\EventRsvp::where('user_id', $user->id)
            ->where('rsvp_status', 'going')
            ->with(['event', 'event.city'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return $this->success([
            'stats' => $stats,
            'photographer' => $photographer,
            'user' => $user,
            'bookings' => $bookings,
            'reviews' => $reviews,
            'albums' => $albums,
            'packages' => $packages,
            'competition_submissions' => $competitionSubmissions,
            'event_rsvps' => $eventRsvps,
        ], 'Dashboard data retrieved successfully');
    }

    /**
     * Track profile share actions from the photographer dashboard
     */
    public function logProfileShare(Request $request)
    {
        $photographer = $request->user()->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $validated = $request->validate([
            'action' => 'required|string|in:copy,open,whatsapp,facebook,messenger,telegram',
        ]);

        $this->ensureShareCode($photographer);

        $stats = PhotographerStats::firstOrCreate(
            ['photographer_id' => $photographer->id],
            ['profile_views' => 0, 'profile_views_this_month' => 0, 'profile_share_clicks' => 0, 'profile_share_visits' => 0, 'total_points' => 0, 'level' => 1]
        );

        $stats->increment('profile_share_clicks');

        return $this->success([
            'share_code' => $photographer->share_code,
            'profile_share_clicks' => $stats->profile_share_clicks,
        ], 'Profile share tracked successfully');
    }

    /**
     * Track visits that came from shared profile links
     */
    public function trackProfileShareVisit(Request $request)
    {
        $validated = $request->validate([
            'ref' => 'required|string|max:32',
        ]);

        $photographer = Photographer::where('share_code', $validated['ref'])->first();
        if (!$photographer) {
            return $this->success([], 'Share code not found');
        }

        $stats = PhotographerStats::firstOrCreate(
            ['photographer_id' => $photographer->id],
            ['profile_views' => 0, 'profile_views_this_month' => 0, 'profile_share_clicks' => 0, 'profile_share_visits' => 0, 'total_points' => 0, 'level' => 1]
        );

        $stats->increment('profile_share_visits');

        return $this->success([], 'Profile share visit tracked successfully');
    }

    private function ensureShareCode(Photographer $photographer): void
    {
        if ($photographer->share_code) {
            return;
        }

        do {
            $shareCode = Str::lower(Str::random(10));
        } while (Photographer::where('share_code', $shareCode)->exists());

        $photographer->share_code = $shareCode;
        $photographer->save();
    }

    /**
     * Update photographer profile information
     */
    public function updateProfile(StorePhotographerRequest $request)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        try {
            $validated = $request->validated();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors(), 'Validation failed');
        }

        try {
            // Handle username update (stored in users table)
            if (isset($validated['username']) && $validated['username'] !== $user->username) {
                // Check if username is available
                $existingUser = \App\Models\User::where('username', $validated['username'])
                    ->where('id', '!=', $user->id)
                    ->first();
                
                if ($existingUser) {
                    return $this->validationError(['username' => ['This username is already taken']], 'Username unavailable');
                }
                
                $user->update(['username' => $validated['username']]);
            }
            unset($validated['username']);
            
            // Extract category_ids before updating photographer
            $categoryIds = $validated['category_ids'] ?? null;
            unset($validated['category_ids']);
            
            // Convert empty string to null for city_id
            if (isset($validated['city_id']) && $validated['city_id'] === '') {
                $validated['city_id'] = null;
            }
            
            // Remove empty social media URLs
            foreach (['facebook_url', 'instagram_url', 'twitter_url', 'linkedin_url', 'youtube_url', 'website_url'] as $field) {
                if (isset($validated[$field]) && empty($validated[$field])) {
                    $validated[$field] = null;
                }
            }
            
            $photographer->update($validated);
            
            // Sync categories if provided
            if ($categoryIds !== null) {
                $photographer->categories()->sync($categoryIds);
            }

            // Track profile update achievement
            \App\Services\AchievementService::trackProfileUpdate($photographer->id);

            return $this->success($photographer->fresh()->load('categories', 'city'), 'Profile updated successfully');
        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $user->id,
                'photographer_id' => $photographer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->error('Failed to update profile: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get photographer's awards
     */
    public function getAwards(Request $request)
    {
        $photographer = $request->user()->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $awards = $photographer->awards()
            ->orderBy('year', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($awards, 'Awards retrieved successfully');
    }

    /**
     * Store a new award
     */
    public function storeAward(Request $request)
    {
        $photographer = $request->user()->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'year' => 'required|integer|min:1950|max:' . (date('Y') + 1),
            'type' => 'required|in:award,achievement,recognition,certification',
            'description' => 'nullable|string|max:1000',
            'certificate_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        ]);

        // Handle certificate upload
        if ($request->hasFile('certificate_file')) {
            $path = $request->file('certificate_file')->store('certificates', 'public');
            $validated['certificate_url'] = '/storage/' . $path;
        }

        $award = $photographer->awards()->create($validated);

        return $this->created($award, 'Award added successfully');
    }

    /**
     * Update an award
     */
    public function updateAward(Request $request, $id)
    {
        $photographer = $request->user()->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $award = $photographer->awards()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'year' => 'sometimes|required|integer|min:1950|max:' . (date('Y') + 1),
            'type' => 'sometimes|required|in:award,achievement,recognition,certification',
            'description' => 'nullable|string|max:1000',
            'certificate_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Handle certificate upload
        if ($request->hasFile('certificate_file')) {
            // Delete old certificate if exists
            if ($award->certificate_url) {
                $oldPath = str_replace('/storage/', '', $award->certificate_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('certificate_file')->store('certificates', 'public');
            $validated['certificate_url'] = '/storage/' . $path;
        }

        $award->update($validated);

        return $this->success($award->fresh(), 'Award updated successfully');
    }

    /**
     * Delete an award
     */
    public function deleteAward(Request $request, $id)
    {
        $photographer = $request->user()->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $award = $photographer->awards()->findOrFail($id);

        // Delete certificate file if exists
        if ($award->certificate_url) {
            $path = str_replace('/storage/', '', $award->certificate_url);
            Storage::disk('public')->delete($path);
        }

        $award->delete();

        return $this->success([], 'Award deleted successfully');
    }

    /**
     * Get my competition submissions
     */
    public function getMySubmissions(Request $request)
    {
        $photographer = $request->user()->photographer;
        $userId = $request->user()->id;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $perPage = (int) $request->get('per_page', 15);
        $perPage = max(1, min($perPage, 1000));

        $submissions = \App\Models\CompetitionSubmission::where(function ($q) use ($userId) {
                $q->where('photographer_id', $userId)
                    ->orWhere('user_id', $userId);
            })
            ->with([
                'competition:id,title,slug,status,submission_deadline,voting_start_at,voting_end_at,judging_end_at',
                'votes'
            ])
            ->withCount('votes')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->paginated($submissions, 'Competition submissions retrieved successfully');
    }

    /**
     * Get my event RSVPs
     */
    public function getMyEventRsvps(Request $request)
    {
        $user = $request->user();

        $rsvps = \App\Models\EventRsvp::where('user_id', $user->id)
            ->with(['event', 'event.city'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginated($rsvps, 'Event RSVPs retrieved successfully');
    }

    /**
     * Get notifications
     */
    public function getNotifications(Request $request)
    {
        $photographer = $request->user()->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $perPage = $request->get('per_page', 20);
        $unreadOnly = $request->get('unread_only', false);

        $query = $photographer->notifications();

        if ($unreadOnly) {
            $query->unread();
        }

        $notifications = $query->paginate($perPage);

        return $this->paginated($notifications, 'Notifications retrieved successfully', 200, [
            'unread_count' => $photographer->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(Request $request, $id)
    {
        $photographer = $request->user()->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $notification = \App\Models\PhotographerNotification::where('id', $id)
            ->where('photographer_id', $photographer->id)
            ->first();

        if (!$notification) {
            return $this->notFound('Notification not found');
        }

        $notification->markAsRead();

        return $this->success($notification, 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead(Request $request)
    {
        $photographer = $request->user()->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $photographer->unreadNotifications()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $this->success([], 'All notifications marked as read');
    }

    /**
     * Delete notification
     */
    public function deleteNotification(Request $request, $id)
    {
        $photographer = $request->user()->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $notification = \App\Models\PhotographerNotification::where('id', $id)
            ->where('photographer_id', $photographer->id)
            ->first();

        if (!$notification) {
            return $this->notFound('Notification not found');
        }

        $notification->delete();

        return $this->success([], 'Notification deleted');
    }

    /**
     * Get unread notification count
     */
    public function getUnreadNotificationCount(Request $request)
    {
        $photographer = $request->user()->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $count = $photographer->unreadNotifications()->count();

        return $this->success([
            'unread_count' => $count,
        ], 'Unread notification count retrieved');
    }

    /**
     * Get photographer's achievements and stats
     */
    public function getAchievements(Request $request)
    {
        $photographer = $request->user()->photographer;

        if (!$photographer) {
            return $this->notFound('Photographer profile not found');
        }

        $achievementData = \App\Services\AchievementService::getAchievementStats($photographer->id);

        return $this->success($achievementData, 'Achievements retrieved successfully');
    }
}
