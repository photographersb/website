<?php

namespace App\Services;

use App\Models\CommunityBadge;
use App\Models\CommunityDiscussion;
use App\Models\CommunityDiscussionComment;
use App\Models\CommunityGroupPost;
use App\Models\CommunityNotification;
use App\Models\CommunityUserBadge;
use App\Models\User;

class CommunityService
{
    public static function isPotentialSpam(int $userId, string $content, string $type): bool
    {
        $content = trim(mb_strtolower($content));

        if (mb_strlen($content) < 8) {
            return false;
        }

        $windowStart = now()->subMinutes(5);

        $duplicateDiscussion = $type === 'discussion'
            ? CommunityDiscussion::query()
                ->where('user_id', $userId)
                ->whereRaw('LOWER(content) = ?', [$content])
                ->where('created_at', '>=', $windowStart)
                ->exists()
            : false;

        $duplicateComment = $type === 'comment'
            ? CommunityDiscussionComment::query()
                ->where('user_id', $userId)
                ->whereRaw('LOWER(content) = ?', [$content])
                ->where('created_at', '>=', $windowStart)
                ->exists()
            : false;

        $duplicateGroupPost = $type === 'group_post'
            ? CommunityGroupPost::query()
                ->where('user_id', $userId)
                ->whereRaw('LOWER(content) = ?', [$content])
                ->where('created_at', '>=', $windowStart)
                ->exists()
            : false;

        return $duplicateDiscussion || $duplicateComment || $duplicateGroupPost;
    }

    public static function notifyUser(int $userId, string $type, string $title, ?string $message = null, array $data = []): void
    {
        CommunityNotification::query()->create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function ensureDefaultBadges(): void
    {
        $defaults = [
            ['code' => 'top_contributor', 'name' => 'Top Contributor', 'icon' => 'star'],
            ['code' => 'community_mentor', 'name' => 'Community Mentor', 'icon' => 'mentor'],
            ['code' => 'event_organizer', 'name' => 'Event Organizer', 'icon' => 'calendar'],
            ['code' => 'competition_judge', 'name' => 'Competition Judge', 'icon' => 'gavel'],
            ['code' => 'helpful_photographer', 'name' => 'Helpful Photographer', 'icon' => 'hands-helping'],
        ];

        foreach ($defaults as $badge) {
            CommunityBadge::query()->firstOrCreate(
                ['code' => $badge['code']],
                [
                    'name' => $badge['name'],
                    'icon' => $badge['icon'],
                    'description' => $badge['name'] . ' community recognition badge',
                    'is_active' => true,
                ]
            );
        }
    }

    public static function syncCommunityBadgesForUser(User $user): void
    {
        self::ensureDefaultBadges();

        $discussionCount = CommunityDiscussion::query()->where('user_id', $user->id)->count();
        $commentCount = CommunityDiscussionComment::query()->where('user_id', $user->id)->count();

        if ($discussionCount >= 5 || $commentCount >= 10) {
            self::awardBadge($user->id, 'top_contributor', 'Reached contribution milestone');
        }

        if ($user->isMentor()) {
            self::awardBadge($user->id, 'community_mentor', 'Active mentorship profile');
        }

        if ($user->isJudge()) {
            self::awardBadge($user->id, 'competition_judge', 'Judge role detected');
        }
    }

    public static function awardBadge(int $userId, string $badgeCode, ?string $reason = null): void
    {
        $badge = CommunityBadge::query()->where('code', $badgeCode)->first();
        if (!$badge) {
            return;
        }

        CommunityUserBadge::query()->firstOrCreate(
            [
                'badge_id' => $badge->id,
                'user_id' => $userId,
            ],
            [
                'awarded_reason' => $reason,
                'awarded_at' => now(),
            ]
        );
    }
}
