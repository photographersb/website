<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
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

        return $this->success($result['data'], 'Sponsors retrieved successfully', 200, [
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
            return $this->notFound('Sponsor not found');
        }
        
        $result = $sponsorshipService->getSponsorDetails($sponsor);

        return $this->success($result['data'], 'Sponsor details retrieved successfully');
    }

    /**
     * Add a new sponsor
     */
    public function store(Request $request, Competition $competition, SponsorshipService $sponsorshipService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:5120',
            'logo_credit_name' => 'nullable|string|max:255',
            'logo_credit_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url',
            'description' => 'nullable|string',
            'tier' => 'nullable|in:platinum,gold,silver,bronze',
            'contribution_amount' => 'nullable|numeric|min:0',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $result = $sponsorshipService->addSponsor($competition, $request->all());

        if (!$result['success']) {
            return $this->error($result['message'], 400);
        }

        return $this->created($result['data'], $result['message']);
    }

    /**
     * Update a sponsor
     */
    public function update(Request $request, CompetitionSponsor $sponsor, SponsorshipService $sponsorshipService)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'logo' => 'nullable|image|max:5120',
            'logo_credit_name' => 'nullable|string|max:255',
            'logo_credit_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url',
            'description' => 'nullable|string',
            'tier' => 'nullable|in:platinum,gold,silver,bronze',
            'contribution_amount' => 'nullable|numeric|min:0',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $result = $sponsorshipService->updateSponsor($sponsor, $request->all());

        if (!$result['success']) {
            return $this->error($result['message'], 400);
        }

        return $this->success($result['data'], $result['message']);
    }

    /**
     * Delete a sponsor
     */
    public function destroy(CompetitionSponsor $sponsor, SponsorshipService $sponsorshipService)
    {
        $result = $sponsorshipService->deleteSponsor($sponsor);

        if (!$result['success']) {
            return $this->error($result['message'], 400);
        }

        return $this->success(null, $result['message']);
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
            'sponsors.*.logo_credit_name' => 'nullable|string|max:255',
            'sponsors.*.logo_credit_url' => 'nullable|url|max:255',
            'sponsors.*.website_url' => 'nullable|url',
            'sponsors.*.description' => 'nullable|string',
            'sponsors.*.tier' => 'nullable|in:platinum,gold,silver,bronze',
            'sponsors.*.contribution_amount' => 'nullable|numeric|min:0',
            'sponsors.*.display_order' => 'nullable|integer',
            'sponsors.*.is_active' => 'nullable|boolean'
        ]);

        $result = $sponsorshipService->bulkAddSponsors($competition, $request->sponsors);

        if (!$result['success']) {
            return $this->error($result['message'], 400);
        }

        return $this->created($result['data'], $result['message']);
    }

    /**
     * Toggle sponsor active status
     */
    public function toggleActive(CompetitionSponsor $sponsor, SponsorshipService $sponsorshipService)
    {
        $result = $sponsorshipService->toggleActiveStatus($sponsor);

        if (!$result['success']) {
            return $this->error($result['message'], 400);
        }

        return $this->success($result['data'], $result['message']);
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

        if (!$result['success']) {
            return $this->error($result['message'], 400);
        }

        return $this->success(null, $result['message']);
    }

    /**
     * Get sponsorship statistics
     */
    public function statistics(Competition $competition, SponsorshipService $sponsorshipService)
    {
        $result = $sponsorshipService->getSponsorshipStatistics($competition);

        return $this->success($result['data'], 'Sponsorship statistics retrieved successfully');
    }

    /**
     * Get global sponsorship statistics
     */
    public function globalStatistics(SponsorshipService $sponsorshipService)
    {
        $result = $sponsorshipService->getGlobalStatistics();

        return $this->success($result['data'], 'Global sponsorship statistics retrieved successfully');
    }
}
