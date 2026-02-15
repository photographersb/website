<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photographer;
use Illuminate\Http\Request;

class AdminPhotographerController extends Controller
{
    /**
     * Get all photographers for admin event management
     * Returns photographers with their user info for dropdown/selection
     */
    public function index(Request $request)
    {
        try {
            // Start with basic query - don't use with() initially to avoid relationship issues
            $query = Photographer::query();

            // Filter by status if provided
            if ($request->has('status') && $request->input('status') === 'active') {
                $query->where('is_verified', 1);
            }

            // Search by name or email if provided
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Order and limit
            $query->orderBy('is_verified', 'desc')
                ->orderBy('id', 'desc')
                ->limit(500);

            // Get the photographers
            $photographers = $query->get();

            // For minimal response (useful for dropdowns) - load user relationship
            if ($request->input('minimal') == 1) {
                $data = $photographers->map(function ($photographer) {
                    // Lazy load the user if not already loaded
                    if (!$photographer->relationLoaded('user')) {
                        $photographer->load('user');
                    }
                    
                    return [
                        'id' => $photographer->id,
                        'user' => $photographer->user ? [
                            'id' => $photographer->user->id,
                            'name' => $photographer->user->name,
                            'email' => $photographer->user->email,
                        ] : null,
                    ];
                })->filter(fn($p) => $p['user'] !== null);

                return response()->json([
                    'status' => 'success',
                    'data' => $data->values(),
                    'message' => 'Photographers retrieved successfully'
                ]);
            }

            // Full response with pagination
            return response()->json([
                'status' => 'success',
                'data' => $photographers,
                'message' => 'Photographers retrieved successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('PhotographerController@index error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve photographers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific photographer
     */
    public function show($id)
    {
        $photographer = Photographer::with(['user:id,name,email,username'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $photographer,
            'message' => 'Photographer retrieved successfully'
        ]);
    }
}
