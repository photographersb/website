<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\Photographer;
use App\Http\Requests\AwardStoreRequest;
use App\Http\Requests\AwardUpdateRequest;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotographerAwardController extends Controller
{
    use ApiResponse;
    /**
     * Get all awards for a photographer
     */
    public function index($photographerId = null)
    {
        // If no ID provided, get authenticated photographer's awards
        if (!$photographerId) {
            $user = auth()->user();
            if ($user && $user->role === 'photographer' && $user->photographer) {
                $photographerId = $user->photographer->id;
            } else {
                return $this->notFound('Photographer profile not found');
            }
        }

        $awards = Award::where('photographer_id', $photographerId)
            ->orderBy('display_order')
            ->orderBy('year', 'desc')
            ->get();

        return $this->success($awards, 'Awards retrieved successfully');
    }

    /**
     * Store a new award (authenticated photographer)
     */
    public function store(AwardStoreRequest $request)
    {
        $photographer = auth()->user()->photographer;
        
        if (!$photographer) {
            return $this->notFound('Photographer profile not found.');
        }

        $validated = $request->validated();

        // Handle certificate upload
        if ($request->hasFile('certificate_file')) {
            $file = $request->file('certificate_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('certificates', $filename, 'public');
            $validated['certificate_url'] = Storage::url($path);
        }

        $validated['photographer_id'] = $photographer->id;
        
        // Set display_order if not provided
        if (!isset($validated['display_order'])) {
            $validated['display_order'] = Award::where('photographer_id', $photographer->id)->max('display_order') + 1;
        }

        $award = Award::create($validated);

        return $this->created($award, 'Award added successfully');
    }

    /**
     * Update an award
     */
    public function update(AwardUpdateRequest $request, $id)
    {
        $photographer = auth()->user()->photographer;
        
        if (!$photographer) {
            return $this->notFound('Photographer profile not found.');
        }

        $award = Award::where('id', $id)
            ->where('photographer_id', $photographer->id)
            ->first();

        if (!$award) {
            return $this->notFound('Award not found or unauthorized.');
        }

        $validated = $request->validated();

        // Handle certificate upload
        if ($request->hasFile('certificate_file')) {
            // Delete old certificate if exists
            if ($award->certificate_url) {
                $oldPath = str_replace('/storage/', '', $award->certificate_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $file = $request->file('certificate_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('certificates', $filename, 'public');
            $validated['certificate_url'] = Storage::url($path);
        }

        $award->update($validated);

        return $this->success($award->fresh(), 'Award updated successfully');
    }

    /**
     * Delete an award
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        if (!$user || $user->role !== 'photographer') {
            return $this->unauthorized('Unauthorized. Photographer access required.');
        }

        $photographer = Photographer::where('user_id', $user->id)->first();
        
        if (!$photographer) {
            return $this->notFound('Photographer profile not found.');
        }

        $award = Award::where('id', $id)
            ->where('photographer_id', $photographer->id)
            ->first();

        if (!$award) {
            return $this->notFound('Award not found or unauthorized.');
        }

        // Delete certificate file if exists
        if ($award->certificate_url) {
            $path = str_replace('/storage/', '', $award->certificate_url);
            Storage::disk('public')->delete($path);
        }

        $award->delete();

        return $this->success([], 'Award deleted successfully');
    }

    /**
     * Reorder awards
     */
    public function reorder(Request $request)
    {
        $photographer = auth()->user()->photographer;
        
        if (!$photographer) {
            return $this->notFound('Photographer profile not found.');
        }

        $validated = $request->validate([
            'awards' => 'required|array',
            'awards.*.id' => 'required|exists:photographer_awards,id',
            'awards.*.display_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['awards'] as $awardData) {
            Award::where('id', $awardData['id'])
                ->where('photographer_id', $photographer->id)
                ->update(['display_order' => $awardData['display_order']]);
        }

        return $this->success([], 'Awards reordered successfully');
    }
}
