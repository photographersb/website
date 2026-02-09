<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MentorController extends Controller
{
    public function index(Request $request)
    {
        // Minimal list for dropdowns (e.g., event forms)
        if ($request->get('minimal') === 'true' || $request->get('minimal') === '1') {
            $query = Mentor::query()->select('id', 'name', 'email');

            if ($request->filled('status') && $request->status === 'active') {
                $query->where('is_active', true);
            }

            $mentors = $query->orderBy('name')->get();

            return response()->json([
                'status' => 'success',
                'data' => $mentors,
            ]);
        }

        // Calculate stats before applying pagination
        $statsQuery = Mentor::query();
        
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
        ];

        $query = Mentor::query()->with('creator');

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

        $mentors = $query->ordered()
            ->paginate($request->get('per_page', 15))
            ->withQueryString();

        return response()->json([
            'status' => 'success',
            'data' => $mentors,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:mentors,slug',
            'title' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:5120', // 5MB
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        // Handle image upload
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('mentors', 'public');
        }

        $validated['created_by'] = auth()->id();

        $mentor = Mentor::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Mentor created successfully',
            'data' => $mentor,
        ], 201);
    }

    public function show($id)
    {
        $mentor = Mentor::findOrFail($id);
        $mentor->load('user', 'creator', 'competitions');

        return response()->json([
            'status' => 'success',
            'data' => $mentor,
        ]);
    }

    public function update(Request $request, $id)
    {
        $mentor = Mentor::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:mentors,slug,' . $mentor->id,
            'title' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:5120',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        // Handle image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image
            if ($mentor->profile_image) {
                Storage::disk('public')->delete($mentor->profile_image);
            }
            $validated['profile_image'] = $request->file('profile_image')->store('mentors', 'public');
        }

        $mentor->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Mentor updated successfully',
            'data' => $mentor->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $mentor = Mentor::findOrFail($id);
        // Delete profile image
        if ($mentor->profile_image) {
            Storage::disk('public')->delete($mentor->profile_image);
        }

        $mentor->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Mentor deleted successfully',
        ]);
    }

    public function toggleStatus($id)
    {
        $mentor = Mentor::findOrFail($id);
        $mentor->update(['is_active' => !$mentor->is_active]);

        return response()->json([
            'status' => 'success',
            'message' => 'Mentor status updated',
            'data' => ['is_active' => $mentor->is_active],
        ]);
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'mentors' => 'required|array',
            'mentors.*.id' => 'required|exists:mentors,id',
            'mentors.*.sort_order' => 'required|integer',
        ]);

        foreach ($validated['mentors'] as $item) {
            Mentor::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Mentor order updated',
        ]);
    }
}
