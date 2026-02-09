<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedPhotographer;
use App\Models\Photographer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FeaturedPhotographerController extends Controller
{
    /**
     * Get all featured photographers with statistics
     */
    public function index(Request $request)
    {
        $query = FeaturedPhotographer::with(['photographer.user:id,name,email']);

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('photographer.user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            } elseif ($status === 'expired') {
                $query->where(function ($q) {
                    $q->where('end_date', '<', now())
                      ->orWhere('active', false);
                });
            } elseif ($status === 'pending') {
                $query->where('active', false);
            }
        }

        // Filter by package
        if ($request->has('package')) {
            $query->where('package_tier', $request->input('package'));
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        $featured = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $stats = [
            'total' => FeaturedPhotographer::count(),
            'active' => FeaturedPhotographer::active()->count(),
            'premium' => FeaturedPhotographer::where('package_tier', 'Enterprise')->count(),
            'revenue' => $this->calculateTotalRevenue(),
        ];

        return response()->json([
            'data' => $featured->items(),
            'stats' => $stats,
            'pagination' => [
                'total' => $featured->total(),
                'per_page' => $featured->perPage(),
                'current_page' => $featured->currentPage(),
                'last_page' => $featured->lastPage(),
            ]
        ]);
    }

    /**
     * Store a new featured photographer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'photographer_id' => 'required|exists:photographers,id',
            'package_tier' => 'required|in:Starter,Professional,Enterprise',
            'category' => 'nullable|string',
            'location' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'active' => 'boolean',
        ]);

        try {
            $featured = FeaturedPhotographer::create([
                ...$validated,
                'approved_by' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Featured photographer created successfully',
                'data' => $featured->load('photographer')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating featured photographer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update featured photographer
     */
    public function update(Request $request, FeaturedPhotographer $featuredPhotographer)
    {
        $validated = $request->validate([
            'package_tier' => 'in:Starter,Professional,Enterprise',
            'category' => 'nullable|string',
            'location' => 'nullable|string',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'active' => 'boolean',
        ]);

        try {
            $featuredPhotographer->update($validated);

            return response()->json([
                'message' => 'Featured photographer updated successfully',
                'data' => $featuredPhotographer->load('photographer')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating featured photographer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete featured photographer
     */
    public function destroy(FeaturedPhotographer $featuredPhotographer)
    {
        try {
            $featuredPhotographer->delete();

            return response()->json([
                'message' => 'Featured photographer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting featured photographer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status
     */
    public function toggle(FeaturedPhotographer $featuredPhotographer)
    {
        try {
            $featuredPhotographer->update([
                'active' => !$featuredPhotographer->active
            ]);

            return response()->json([
                'message' => 'Status updated successfully',
                'data' => $featuredPhotographer->load('photographer')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search photographers for dropdown
     */
    public function searchPhotographers(Request $request)
    {
        $search = trim($request->input('q', ''));

        $query = Photographer::with(['user:id,name,email'])
            ->select('id', 'user_id', 'profile_picture');

        if ($search !== '') {
            $query->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $photographers = $query
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(function ($photographer) {
                return [
                    'id' => $photographer->id,
                    'name' => $photographer->user?->name ?? 'Unknown',
                    'email' => $photographer->user?->email ?? '',
                    'profile_image' => $photographer->profile_picture,
                ];
            });

        return response()->json($photographers);
    }

    /**
     * Calculate total revenue
     */
    private function calculateTotalRevenue()
    {
        $prices = [
            'Starter' => 999,
            'Professional' => 2499,
            'Enterprise' => 5999,
        ];

        $revenue = 0;
        foreach ($prices as $tier => $price) {
            $count = FeaturedPhotographer::where('package_tier', $tier)->active()->count();
            $revenue += $count * $price;
        }

        return $revenue;
    }
}
