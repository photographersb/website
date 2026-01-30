<?php

namespace App\Http\Controllers\Api;

use App\Models\Photographer;
use App\Models\Category;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PhotographerController extends Controller
{
    /**
     * Get all photographers with filters
     */
    public function index(Request $request)
    {
        $query = Photographer::where('is_verified', true)
            ->with(['user', 'trustScore', 'categories', 'photos']);

        // Filter by city
        if ($request->has('city')) {
            $city = City::where('slug', $request->city)->first();
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
        match ($sort) {
            'rating' => $query->orderBy('average_rating', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('is_featured', 'desc')->orderBy('average_rating', 'desc'),
        };

        $photographers = $query->paginate(30);

        return response()->json([
            'status' => 'success',
            'data' => $photographers->items(),
            'meta' => [
                'total' => $photographers->total(),
                'per_page' => $photographers->perPage(),
                'current_page' => $photographers->currentPage(),
                'last_page' => $photographers->lastPage(),
            ],
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

        // Eagerly load relationships to avoid N+1
        $photographer->load([
            'user',
            'trustScore',
            'categories',
            'albums' => function ($q) {
                $q->where('is_public', true)->orderBy('display_order');
            },
            'packages' => function ($q) {
                $q->where('is_active', true)->orderBy('display_order');
            },
            'reviews' => function ($q) {
                $q->where('status', 'published')->latest('published_at')->limit(5);
            },
            'reviews.booking', // Eager load booking for reviews
            'reviews.reviewer', // Eager load reviewer (client) for reviews
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $photographer,
        ]);
    }

    /**
     * Search photographers
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'status' => 'error',
                'message' => 'Search query must be at least 2 characters',
            ], 422);
        }

        $photographers = Photographer::where('is_verified', true)
            ->whereHas('user', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->select('id', 'user_id', 'slug', 'average_rating', 'is_featured')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $photographers,
        ]);
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
            return response()->json([
                'status' => 'error',
                'message' => 'Photographer profile not found',
            ], 404);
        }

        try {
            // Delete old avatar if exists
            if ($photographer->profile_picture && \Storage::disk('public')->exists($photographer->profile_picture)) {
                \Storage::disk('public')->delete($photographer->profile_picture);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');

            // Update photographer record
            $photographer->update([
                'profile_picture' => $path,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile picture updated successfully',
                'data' => [
                    'profile_picture' => \Storage::url($path),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Avatar upload failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload avatar. Please try again.',
            ], 500);
        }
    }

    /**
     * Get photographer dashboard stats and data
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Photographer profile not found',
            ], 404);
        }

        // Get dashboard statistics
        $stats = [
            'total_bookings' => $photographer->bookings()->count(),
            'pending_bookings' => $photographer->bookings()->where('status', 'pending')->count(),
            'average_rating' => round($photographer->reviews()->avg('rating') ?? 0, 1),
            'total_revenue' => $photographer->transactions()
                ->where('status', 'completed')
                ->where('type', 'credit')
                ->sum('amount'),
        ];

        // Get recent bookings
        $bookings = $photographer->bookings()
            ->with(['client', 'service'])
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

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => $stats,
                'photographer' => $photographer,
                'user' => $user,
                'bookings' => $bookings,
                'reviews' => $reviews,
                'albums' => $albums,
                'packages' => $packages,
            ],
        ]);
    }

    /**
     * Update photographer profile information
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Photographer profile not found',
            ], 404);
        }

        $validated = $request->validate([
            'business_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'starting_price' => 'nullable|numeric|min:0',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        try {
            $photographer->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => $photographer->fresh(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update profile. Please try again.',
            ], 500);
        }
    }
}
