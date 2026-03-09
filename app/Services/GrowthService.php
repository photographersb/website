<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\ReferralReward;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GrowthService
{
    private const PHOTOGRAPHER_ROLES = ['photographer', 'studio_owner', 'studio_photographer'];

    private const REWARD_MILESTONES = [
        3 => 'Top Referrer Badge',
        5 => 'Platform Recognition',
        10 => 'Featured Profile Opportunity',
    ];

    public static function ensureReferralCode(User $user): string
    {
        if (!empty($user->referral_code)) {
            return $user->referral_code;
        }

        $base = null;
        if (Schema::hasColumn('users', 'username') && !empty($user->username)) {
            $base = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $user->username));
        }

        if (!$base) {
            $base = strtoupper(substr((string) Str::uuid(), 0, 8));
        }

        $code = substr($base, 0, 12);
        while (User::where('referral_code', $code)->exists()) {
            $code = strtoupper(substr($base, 0, 6) . Str::random(4));
        }

        $user->forceFill(['referral_code' => $code])->save();

        return $code;
    }

    public static function attachReferralOnRegistration(User $newUser, ?string $refCodeOrUsername, ?string $ipAddress = null): void
    {
        self::ensureReferralCode($newUser);

        $refCodeOrUsername = trim((string) $refCodeOrUsername);
        if ($refCodeOrUsername === '') {
            return;
        }

        $referrer = User::query()
            ->where('referral_code', $refCodeOrUsername)
            ->when(Schema::hasColumn('users', 'username'), function ($query) use ($refCodeOrUsername) {
                $query->orWhere('username', $refCodeOrUsername);
            })
            ->first();

        if (!$referrer) {
            return;
        }

        if ($referrer->id === $newUser->id) {
            return;
        }

        if (!empty($referrer->email) && !empty($newUser->email) && strcasecmp($referrer->email, $newUser->email) === 0) {
            return;
        }

        if (!empty($referrer->phone) && !empty($newUser->phone) && $referrer->phone === $newUser->phone) {
            return;
        }

        if (Referral::where('referred_user_id', $newUser->id)->exists()) {
            return;
        }

        $suspiciousVolume = false;
        if ($ipAddress) {
            $todayCount = Referral::query()
                ->where('referrer_user_id', $referrer->id)
                ->whereDate('created_at', now()->toDateString())
                ->where('metadata->ip_address', $ipAddress)
                ->count();

            $suspiciousVolume = $todayCount >= 5;
        }

        Referral::create([
            'referrer_user_id' => $referrer->id,
            'referred_user_id' => $newUser->id,
            'status' => $suspiciousVolume ? 'invalid' : 'pending',
            'referral_code' => $refCodeOrUsername,
            'metadata' => [
                'ip_address' => $ipAddress,
                'registered_role' => $newUser->role,
            ],
        ]);

        self::syncRewardProgress($referrer);
    }

    public static function finalizeReferral(User $referredUser): void
    {
        $referral = Referral::with('referrer')
            ->where('referred_user_id', $referredUser->id)
            ->first();

        if (!$referral || $referral->status === 'invalid') {
            return;
        }

        $referral->status = 'successful';
        $meta = $referral->metadata ?? [];
        $meta['verified_at'] = now()->toIso8601String();
        $referral->metadata = $meta;
        $referral->save();

        if ($referral->referrer) {
            self::syncRewardProgress($referral->referrer);
        }
    }

    public static function syncRewardProgress(User $referrer): void
    {
        $successfulPhotographerReferrals = Referral::query()
            ->where('referrer_user_id', $referrer->id)
            ->where('status', 'successful')
            ->whereHas('referredUser', function ($query) {
                $query->whereIn('role', self::PHOTOGRAPHER_ROLES);
            })
            ->count();

        foreach (self::REWARD_MILESTONES as $milestone => $badgeName) {
            $reward = ReferralReward::firstOrCreate(
                [
                    'user_id' => $referrer->id,
                    'milestone' => $milestone,
                ],
                [
                    'status' => 'in_progress',
                    'badge_name' => $badgeName,
                ]
            );

            $reward->referred_photographers_count = $successfulPhotographerReferrals;

            if ($successfulPhotographerReferrals >= $milestone && $reward->status !== 'achieved') {
                $reward->status = 'achieved';
                $reward->achieved_at = now();
            }

            $reward->save();
        }
    }

    public static function referralSummaryFor(User $user): array
    {
        self::ensureReferralCode($user);
        self::syncRewardProgress($user);

        $total = Referral::where('referrer_user_id', $user->id)->count();
        $successful = Referral::where('referrer_user_id', $user->id)->where('status', 'successful')->count();
        $successfulPhotographers = Referral::where('referrer_user_id', $user->id)
            ->where('status', 'successful')
            ->whereHas('referredUser', function ($query) {
                $query->whereIn('role', self::PHOTOGRAPHER_ROLES);
            })
            ->count();

        $rewards = ReferralReward::where('user_id', $user->id)
            ->orderBy('milestone')
            ->get();

        return [
            'referral_code' => $user->referral_code,
            'referral_url' => url('/register?ref=' . $user->referral_code),
            'total_referrals' => $total,
            'successful_referrals' => $successful,
            'successful_photographer_referrals' => $successfulPhotographers,
            'rewards' => $rewards,
        ];
    }
}
