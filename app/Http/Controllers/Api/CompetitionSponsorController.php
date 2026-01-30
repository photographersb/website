<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionSponsor;
use App\Services\SponsorshipService;
use Illuminate\Http\Request;

class CompetitionSponsorController extends Controller
{
    /**
     * Get all sponsors for a competition
     */
    public function index(Request $request, Competition $competition, SponsorshipService $sponsorshipService)
    {
        $activeOnly = $request->boolean('active_only', true);
        $result = $sponsorshipService->getSponsors($competition, $activeOnly);

        return response()->json([
            'status' => 'success',
            'data' => $result['data'],
            'total' => $result['total']
        ]);
    }

    /**
     * Get sponsor details
     */
    public function show($sponsorId, SponsorshipService $sponsorshipService)
    {
        $sponsor = CompetitionSponsor::find($sponsorId);
        
        if (!$sponsor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sponsor not found'
            ], 404);
        }
        
        $result = $sponsorshipService->getSponsorDetails($sponsor);

        return response()->json([
            'status' => 'success',
            'data' => $result['data']
        ]);
    }

    /**
     * Add a new sponsor
     */
    public function store(Request $request, Competition $competition, SponsorshipService $sponsorshipService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'website_url' => 'nullable|url',
            'description' => 'nullable|string',
            'tier' => 'nullable|in:platinum,gold,silver,bronze',
            'contribution_amount' => 'nullable|numeric|min:0',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $result = $sponsorshipService->addSponsor($competition, $request->all());

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null
        ], $result['success'] ? 201 : 400);
    }

    /**
     * Update a sponsor
     */
    public function update(Request $request, CompetitionSponsor $sponsor, SponsorshipService $sponsorshipService)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'website_url' => 'nullable|url',
            'description' => 'nullable|string',
            'tier' => 'nullable|in:platinum,gold,silver,bronze',
            'contribution_amount' => 'nullable|numeric|min:0',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $result = $sponsorshipService->updateSponsor($sponsor, $request->all());

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Delete a sponsor
     */
    public function destroy(CompetitionSponsor $sponsor, SponsorshipService $sponsorshipService)
    {
        $result = $sponsorshipService->deleteSponsor($sponsor);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message']
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Bulk add sponsors
     */
    public function bulkCreate(Request $request, Competition $competition, SponsorshipService $sponsorshipService)
    {
        $request->validate([
            'sponsors' => 'required|array',
            'sponsors.*.name' => 'required|string|max:255',
            'sponsors.*.logo_url' => 'nullable|string',
            'sponsors.*.website_url' => 'nullable|url',
            'sponsors.*.description' => 'nullable|string',
            'sponsors.*.tier' => 'nullable|in:platinum,gold,silver,bronze',
            'sponsors.*.contribution_amount' => 'nullable|numeric|min:0',
            'sponsors.*.display_order' => 'nullable|integer',
            'sponsors.*.is_active' => 'nullable|boolean'
        ]);

        $result = $sponsorshipService->bulkAddSponsors($competition, $request->sponsors);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null
        ], $result['success'] ? 201 : 400);
    }

    /**
     * Toggle sponsor active status
     */
    public function toggleActive(CompetitionSponsor $sponsor, SponsorshipService $sponsorshipService)
    {
        $result = $sponsorshipService->toggleActiveStatus($sponsor);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Reorder sponsors
     */
    public function reorder(Request $request, Competition $competition, SponsorshipService $sponsorshipService)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.sponsor_id' => 'required|exists:competition_sponsors,id',
            'order.*.order' => 'required|integer'
        ]);

        $result = $sponsorshipService->reorderSponsors($competition, $request->order);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message']
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Get sponsorship statistics
     */
    public function statistics(Competition $competition, SponsorshipService $sponsorshipService)
    {
        $result = $sponsorshipService->getSponsorshipStatistics($competition);

        return response()->json([
            'status' => 'success',
            'data' => $result['data']
        ]);
    }

    /**
     * Get global sponsorship statistics
     */
    public function globalStatistics(SponsorshipService $sponsorshipService)
    {
        $result = $sponsorshipService->getGlobalStatistics();

        return response()->json([
            'status' => 'success',
            'data' => $result['data']
        ]);
    }
}
