<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Referral;
use App\Models\ShareLog;
use App\Models\User;

class GrowthDashboardController extends Controller
{
    use ApiResponse;

    public function overview()
    {
        $totalReferrals = Referral::count();
        $successfulReferrals = Referral::where('status', 'successful')->count();
        $profileShares = ShareLog::where('entity_type', 'profile')->count();
        $competitionShares = ShareLog::whereIn('entity_type', ['competition', 'competition_submission'])->count();
        $eventShares = ShareLog::where('entity_type', 'event')->count();

        $topReferrers = User::query()
            ->whereHas('referralsMade', function ($query) {
                $query->where('status', 'successful')
                    ->whereHas('referredUser', function ($inner) {
                        $inner->whereIn('role', ['photographer', 'studio_owner', 'studio_photographer']);
                    });
            })
            ->withCount([
                'referralsMade as successful_photographer_referrals' => function ($query) {
                    $query->where('status', 'successful')
                        ->whereHas('referredUser', function ($inner) {
                            $inner->whereIn('role', ['photographer', 'studio_owner', 'studio_photographer']);
                        });
                },
            ])
            ->orderByDesc('successful_photographer_referrals')
            ->limit(10)
            ->get(['id', 'name', 'username', 'referral_code']);

        $sharesByPlatform = ShareLog::query()
            ->selectRaw('platform, COUNT(*) as total')
            ->groupBy('platform')
            ->orderByDesc('total')
            ->get();

        $sharesByType = ShareLog::query()
            ->selectRaw('entity_type, COUNT(*) as total')
            ->groupBy('entity_type')
            ->orderByDesc('total')
            ->get();

        return $this->success([
            'metrics' => [
                'total_referrals' => $totalReferrals,
                'successful_referrals' => $successfulReferrals,
                'profile_shares' => $profileShares,
                'competition_shares' => $competitionShares,
                'event_shares' => $eventShares,
            ],
            'top_referrers' => $topReferrers,
            'shares_by_platform' => $sharesByPlatform,
            'shares_by_type' => $sharesByType,
        ], 'Growth dashboard metrics retrieved successfully');
    }
}
