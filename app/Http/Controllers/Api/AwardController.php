<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Award;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAwardRequest;
use App\Http\Requests\UpdateAwardRequest;
use Illuminate\Support\Facades\Storage;

class AwardController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of awards for the authenticated photographer.
     */
    public function index()
    {
        try {
            $awards = Award::where('photographer_id', auth()->id())
                ->orderBy('display_order')
                ->get();

            return $this->success($awards, 'Awards retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve awards: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created award.
     */
    public function store(StoreAwardRequest $request)
    {
        try {
            $data = $request->validated();
            $data['photographer_id'] = auth()->id();

            // Handle certificate upload if present
            if ($request->hasFile('certificate')) {
                $file = $request->file('certificate');
                $path = $file->store('certificates', 'public');
                $data['certificate_path'] = $path;
            }

            // Set display order to last
            $maxOrder = Award::where('photographer_id', auth()->id())->max('display_order') ?? 0;
            $data['display_order'] = $maxOrder + 1;

            $award = Award::create($data);

            return $this->created($award, 'Award created successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to create award: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified award.
     */
    public function show(Award $award)
    {
        try {
            // Ensure the award belongs to the authenticated user
            if ($award->photographer_id !== auth()->id()) {
                return $this->unauthorized('You are not authorized to view this award');
            }

            return $this->success($award, 'Award retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve award: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified award.
     */
    public function update(UpdateAwardRequest $request, Award $award)
    {
        try {
            // Ensure the award belongs to the authenticated user
            if ($award->photographer_id !== auth()->id()) {
                return $this->unauthorized('You are not authorized to update this award');
            }

            $data = $request->validated();

            // Handle certificate upload if present
            if ($request->hasFile('certificate')) {
                // Delete old certificate if exists
                if ($award->certificate_path) {
                    Storage::disk('public')->delete($award->certificate_path);
                }

                $file = $request->file('certificate');
                $path = $file->store('certificates', 'public');
                $data['certificate_path'] = $path;
            }

            $award->update($data);

            return $this->success($award, 'Award updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update award: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified award.
     */
    public function destroy(Award $award)
    {
        try {
            // Ensure the award belongs to the authenticated user
            if ($award->photographer_id !== auth()->id()) {
                return $this->unauthorized('You are not authorized to delete this award');
            }

            // Delete certificate file if exists
            if ($award->certificate_path) {
                Storage::disk('public')->delete($award->certificate_path);
            }

            $award->delete();

            return $this->success(null, 'Award deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete award: ' . $e->getMessage());
        }
    }

    /**
     * Reorder awards for the authenticated photographer.
     */
    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'awards' => 'required|array',
                'awards.*.id' => 'required|exists:awards,id',
                'awards.*.display_order' => 'required|integer|min:1'
            ]);

            foreach ($request->awards as $awardData) {
                $award = Award::find($awardData['id']);
                
                // Ensure the award belongs to the authenticated user
                if ($award && $award->photographer_id === auth()->id()) {
                    $award->update(['display_order' => $awardData['display_order']]);
                }
            }

            return $this->success(null, 'Awards reordered successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to reorder awards: ' . $e->getMessage());
        }
    }
}
