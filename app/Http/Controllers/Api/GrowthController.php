<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\GrowthInvite;
use App\Models\Referral;
use App\Models\ShareLog;
use App\Models\User;
use App\Services\GrowthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class GrowthController extends Controller
{
    use ApiResponse;

    public function topReferrers(Request $request)
    {
        $limit = min((int) $request->input('limit', 20), 100);

        $items = User::query()
            ->select(['id', 'name'])
            ->when(Schema::hasColumn('users', 'username'), function ($query) {
                $query->addSelect('username');
            })
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
            ->limit($limit)
            ->get();

        return $this->success($items, 'Top referrers retrieved successfully');
    }

    public function myReferrals(Request $request)
    {
        $user = $request->user();
        $summary = GrowthService::referralSummaryFor($user);

        return $this->success($summary, 'My referral summary retrieved successfully');
    }

    public function logShare(Request $request)
    {
        $validated = $request->validate([
            'entity_type' => 'required|in:profile,event,competition,competition_submission,certificate',
            'entity_id' => 'nullable|integer|min:1',
            'platform' => 'required|in:facebook,whatsapp,linkedin,copy,twitter,telegram,native_share',
        ]);

        $user = $request->user();

        ShareLog::create([
            'user_id' => $user?->id,
            'entity_type' => $validated['entity_type'],
            'entity_id' => $validated['entity_id'] ?? null,
            'platform' => $validated['platform'],
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'created_at' => now(),
        ]);

        return $this->success([], 'Share activity tracked');
    }

    public function inviteByEmail(Request $request)
    {
        $validated = $request->validate([
            'emails' => 'required|array|min:1|max:10',
            'emails.*' => 'required|email',
        ]);

        $user = $request->user();
        $summary = GrowthService::referralSummaryFor($user);
        $sent = [];

        foreach ($validated['emails'] as $email) {
            $inviteUrl = $summary['referral_url'];
            $subject = 'Join Photographer SB — a platform for photographers in Bangladesh';
            $body = "Hi,\n\n{$user->name} invited you to join Photographer SB.\n"
                . "Join here: {$inviteUrl}\n\n"
                . "Discover photographers, competitions, and events across Bangladesh.\n\n"
                . "— Photographer SB";

            Mail::raw($body, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });

            GrowthInvite::create([
                'user_id' => $user->id,
                'email' => $email,
                'status' => 'sent',
                'sent_at' => now(),
                'metadata' => [
                    'referral_url' => $inviteUrl,
                ],
            ]);

            $sent[] = $email;
        }

        return $this->success([
            'sent' => $sent,
            'count' => count($sent),
        ], 'Invite emails sent successfully');
    }

    public function shareFrame(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:competition_participation,award_certificate,event_participation',
            'title' => 'nullable|string|max:180',
            'subtitle' => 'nullable|string|max:240',
            'entity_id' => 'nullable|integer|min:1',
        ]);

        $frame = [
            'type' => $validated['type'],
            'title' => $validated['title'] ?? 'Photographer SB Achievement',
            'subtitle' => $validated['subtitle'] ?? 'Created with Photographer SB',
            'branding' => 'Photographer SB',
            'theme' => 'burgundy-gold',
            'share_image_url' => null,
            'generated_at' => now()->toIso8601String(),
        ];

        return $this->success($frame, 'Share frame payload generated');
    }

    public function leaderboard(Request $request)
    {
        return $this->topReferrers($request);
    }
}
