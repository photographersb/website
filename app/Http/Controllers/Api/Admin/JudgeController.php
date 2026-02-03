<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Judge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JudgeController extends Controller
{
    public function index(Request $request)
    {
        // Calculate stats before applying pagination
        $statsQuery = Judge::query();
        
        // Apply search filter to stats if present
        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%");
            });
        }
        
        $stats = [
            'total' => $statsQuery->count(),
            'active' => (clone $statsQuery)->where('is_active', true)->count(),
            'inactive' => (clone $statsQuery)->where('is_active', false)->count(),
            'linked' => (clone $statsQuery)->whereNotNull('user_id')->count(),
        ];

        $query = Judge::query()->with('user');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $judges = $query->ordered()
            ->paginate($request->get('per_page', 15))
            ->withQueryString();

        return response()->json([
            'status' => 'success',
            'data' => $judges,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:judges,slug',
            'title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:5120',
            'email' => 'nullable|email|max:255',
            'organization' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        // Handle image upload
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('judges', 'public');
        }

        $judge = Judge::create($validated);

        // If linked to user, update user role to include judge
        if ($judge->user_id) {
            $user = User::find($judge->user_id);
            if ($user && $user->role !== 'judge') {
                $user->update(['role' => 'judge']);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Judge created successfully',
            'data' => $judge,
        ], 201);
    }

    public function show($id)
    {
        $judge = Judge::findOrFail($id);
        $judge->load('user', 'competitions', 'scores');

        return response()->json([
            'status' => 'success',
            'data' => $judge,
        ]);
    }

    public function update(Request $request, $id)
    {
        $judge = Judge::findOrFail($id);
        
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:judges,slug,' . $judge->id,
            'title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:5120',
            'email' => 'nullable|email|max:255',
            'organization' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        // Handle image upload
        if ($request->hasFile('profile_image')) {
            if ($judge->profile_image) {
                Storage::disk('public')->delete($judge->profile_image);
            }
            $validated['profile_image'] = $request->file('profile_image')->store('judges', 'public');
        }

        $judge->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Judge updated successfully',
            'data' => $judge->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $judge = Judge::findOrFail($id);
        if ($judge->profile_image) {
            Storage::disk('public')->delete($judge->profile_image);
        }

        $judge->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Judge deleted successfully',
        ]);
    }

    public function toggleStatus($id)
    {
        $judge = Judge::findOrFail($id);
        $judge->update(['is_active' => !$judge->is_active]);

        return response()->json([
            'status' => 'success',
            'message' => 'Judge status updated',
            'data' => ['is_active' => $judge->is_active],
        ]);
    }
}
