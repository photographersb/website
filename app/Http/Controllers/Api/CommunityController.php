<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\CommunityDiscussion;
use App\Models\CommunityDiscussionComment;
use App\Models\CommunityDiscussionLike;
use App\Models\CommunityGroup;
use App\Models\CommunityGroupMember;
use App\Models\CommunityGroupPost;
use App\Models\CommunityGroupPostComment;
use App\Models\CommunityMentorshipProfile;
use App\Models\CommunityMentorshipRequest;
use App\Models\CommunityNotification;
use App\Models\CommunityReport;
use App\Models\User;
use App\Services\CommunityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    use ApiResponse;

    public function hub()
    {
        $featuredDiscussions = CommunityDiscussion::query()
            ->with(['user:id,name,username'])
            ->where('status', 'active')
            ->where('is_featured', true)
            ->latest('last_activity_at')
            ->limit(6)
            ->get();

        $localClubs = CommunityGroup::query()
            ->with(['city:id,name,slug'])
            ->where('status', 'active')
            ->where('type', 'local_club')
            ->orderByDesc('members_count')
            ->limit(8)
            ->get();

        $mentors = CommunityMentorshipProfile::query()
            ->with(['user:id,name,username'])
            ->where('is_active', true)
            ->where('availability_status', '!=', 'unavailable')
            ->orderByDesc('years_experience')
            ->limit(6)
            ->get();

        $topContributors = $this->leaderboardData(10);

        return $this->success([
            'featured_posts' => $featuredDiscussions,
            'local_groups' => $localClubs,
            'mentorship_programs' => $mentors,
            'top_contributors' => $topContributors,
            'sections' => [
                'photography_discussions',
                'local_photography_groups',
                'mentorship_programs',
                'featured_community_posts',
                'top_contributors',
            ],
        ], 'Community hub loaded successfully');
    }

    public function discussions(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 20), 50);

        $query = CommunityDiscussion::query()
            ->with(['user:id,name,username'])
            ->where('status', 'active')
            ->orderByDesc('is_featured')
            ->orderByDesc('last_activity_at');

        if ($request->filled('category')) {
            $query->where('category', (string) $request->input('category'));
        }

        if ($request->filled('q')) {
            $q = (string) $request->input('q');
            $query->where(function ($inner) use ($q) {
                $inner->where('title', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }

        $items = $query->paginate($perPage);
        return $this->paginated($items, 'Community discussions retrieved successfully');
    }

    public function storeDiscussion(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => 'required|string|min:6|max:180',
            'content' => 'required|string|min:15|max:6000',
            'category' => 'required|string|max:80',
            'tags' => 'nullable|array|max:8',
            'tags.*' => 'nullable|string|max:40',
        ]);

        if (CommunityService::isPotentialSpam($user->id, $validated['content'], 'discussion')) {
            return $this->error('Similar content was posted recently. Please wait before posting again.', 429);
        }

        $discussion = CommunityDiscussion::query()->create([
            'user_id' => $user->id,
            'title' => trim($validated['title']),
            'content' => trim($validated['content']),
            'category' => Str::lower(trim($validated['category'])),
            'tags' => $validated['tags'] ?? [],
            'last_activity_at' => now(),
        ]);

        CommunityService::syncCommunityBadgesForUser($user);

        return $this->created($discussion->load('user:id,name,username'), 'Discussion posted successfully');
    }

    public function showDiscussion(CommunityDiscussion $discussion)
    {
        $discussion->load([
            'user:id,name,username',
            'comments' => fn ($query) => $query->where('status', 'active')->latest()->limit(100),
            'comments.user:id,name,username',
        ]);

        return $this->success($discussion, 'Discussion details retrieved successfully');
    }

    public function commentDiscussion(Request $request, CommunityDiscussion $discussion)
    {
        if ($discussion->status !== 'active') {
            return $this->error('Discussion is not available for comments.', 422);
        }

        $user = $request->user();

        $validated = $request->validate([
            'content' => 'required|string|min:2|max:2000',
            'parent_id' => 'nullable|integer|exists:community_discussion_comments,id',
        ]);

        if (CommunityService::isPotentialSpam($user->id, $validated['content'], 'comment')) {
            return $this->error('Duplicate comment detected. Please avoid posting the same message repeatedly.', 429);
        }

        $comment = CommunityDiscussionComment::query()->create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => trim($validated['content']),
        ]);

        $discussion->increment('comments_count');
        $discussion->update(['last_activity_at' => now()]);

        if ($discussion->user_id !== $user->id) {
            CommunityService::notifyUser(
                $discussion->user_id,
                'discussion_reply',
                'Someone replied to your discussion',
                Str::limit($comment->content, 120),
                ['discussion_id' => $discussion->id, 'comment_id' => $comment->id]
            );
        }

        $this->notifyMentions($comment->content, $user->id, 'discussion_mention', [
            'discussion_id' => $discussion->id,
            'comment_id' => $comment->id,
        ]);

        CommunityService::syncCommunityBadgesForUser($user);

        return $this->created($comment->load('user:id,name,username'), 'Comment added successfully');
    }

    public function toggleDiscussionLike(Request $request, CommunityDiscussion $discussion)
    {
        $userId = $request->user()->id;

        $existing = CommunityDiscussionLike::query()
            ->where('discussion_id', $discussion->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $existing->delete();
            $discussion->decrement('likes_count');
            return $this->success(['liked' => false, 'likes_count' => max(0, $discussion->fresh()->likes_count)], 'Discussion unliked');
        }

        CommunityDiscussionLike::query()->create([
            'discussion_id' => $discussion->id,
            'user_id' => $userId,
        ]);
        $discussion->increment('likes_count');

        if ($discussion->user_id !== $userId) {
            CommunityService::notifyUser(
                $discussion->user_id,
                'discussion_like',
                'Your discussion received a like',
                null,
                ['discussion_id' => $discussion->id]
            );
        }

        return $this->success(['liked' => true, 'likes_count' => $discussion->fresh()->likes_count], 'Discussion liked');
    }

    public function logDiscussionShare(Request $request, CommunityDiscussion $discussion)
    {
        $discussion->increment('shares_count');
        return $this->success(['shares_count' => $discussion->fresh()->shares_count], 'Discussion share tracked');
    }

    public function deleteDiscussion(Request $request, CommunityDiscussion $discussion)
    {
        $user = $request->user();
        $isOwner = $discussion->user_id === $user->id;
        $isAdmin = method_exists($user, 'isAdmin') && $user->isAdmin();

        if (!$isOwner && !$isAdmin) {
            return $this->unauthorized('You can only remove your own discussion.');
        }

        $discussion->update([
            'status' => 'hidden',
            'last_activity_at' => now(),
        ]);

        return $this->success(['removed' => true, 'discussion_id' => $discussion->id], 'Discussion removed successfully');
    }

    public function groups(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 20), 50);

        $query = CommunityGroup::query()
            ->with(['city:id,name,slug'])
            ->where('status', 'active')
            ->orderByDesc('is_featured')
            ->orderByDesc('members_count');

        if ($request->filled('type')) {
            $query->where('type', (string) $request->input('type'));
        }

        if ($request->filled('city')) {
            $citySlug = (string) $request->input('city');
            $query->whereHas('city', fn ($q) => $q->where('slug', $citySlug));
        }

        if ($request->filled('q')) {
            $q = (string) $request->input('q');
            $query->where(fn ($inner) => $inner->where('name', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%"));
        }

        $items = $query->paginate($perPage);
        return $this->paginated($items, 'Community groups retrieved successfully');
    }

    public function storeGroup(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|min:4|max:140',
            'description' => 'required|string|min:15|max:4000',
            'cover_image_url' => 'nullable|url|max:1200',
            'type' => 'required|in:interest,local_club',
            'city_id' => 'nullable|integer|exists:locations,id',
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (CommunityGroup::query()->where('slug', $slug)->exists()) {
            $counter++;
            $slug = $baseSlug . '-' . $counter;
        }

        $group = DB::transaction(function () use ($validated, $user, $slug) {
            $group = CommunityGroup::query()->create([
                'name' => trim($validated['name']),
                'slug' => $slug,
                'description' => trim($validated['description']),
                'cover_image_url' => $validated['cover_image_url'] ?? null,
                'type' => $validated['type'],
                'city_id' => $validated['city_id'] ?? null,
                'created_by' => $user->id,
                'members_count' => 1,
            ]);

            CommunityGroupMember::query()->create([
                'group_id' => $group->id,
                'user_id' => $user->id,
                'role' => 'admin',
                'joined_at' => now(),
            ]);

            return $group;
        });

        return $this->created($group, 'Community group created successfully');
    }

    public function showGroup(CommunityGroup $group)
    {
        $group->load([
            'city:id,name,slug',
            'creator:id,name,username',
            'members' => fn ($q) => $q->latest()->limit(20),
            'members.user:id,name,username',
            'posts' => fn ($q) => $q->where('status', 'active')->latest()->limit(20),
            'posts.user:id,name,username',
        ]);

        return $this->success($group, 'Community group details retrieved successfully');
    }

    public function joinGroup(Request $request, CommunityGroup $group)
    {
        $user = $request->user();

        $existing = CommunityGroupMember::query()
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return $this->success(['joined' => true], 'Already a group member');
        }

        CommunityGroupMember::query()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $group->increment('members_count');

        return $this->success(['joined' => true, 'members_count' => $group->fresh()->members_count], 'Joined group successfully');
    }

    public function leaveGroup(Request $request, CommunityGroup $group)
    {
        $user = $request->user();

        $membership = CommunityGroupMember::query()
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$membership) {
            return $this->success(['left' => false, 'members_count' => $group->members_count], 'You are not a member of this group');
        }

        if ($membership->role === 'admin' && (int) $group->created_by === (int) $user->id) {
            return $this->error('Group creator cannot leave the group. Transfer ownership first.', 422);
        }

        $membership->delete();

        if ((int) $group->members_count > 0) {
            $group->decrement('members_count');
        }

        return $this->success(['left' => true, 'members_count' => $group->fresh()->members_count], 'Left group successfully');
    }

    public function transferGroupOwnership(Request $request, CommunityGroup $group)
    {
        $user = $request->user();
        $isCreator = (int) $group->created_by === (int) $user->id;
        $isAdmin = method_exists($user, 'isAdmin') && $user->isAdmin();

        if (!$isCreator && !$isAdmin) {
            return $this->unauthorized('Only group owner or admin can transfer ownership.');
        }

        $validated = $request->validate([
            'target_user_id' => 'required|integer|exists:users,id',
        ]);

        $targetUserId = (int) $validated['target_user_id'];
        if ($targetUserId === (int) $group->created_by) {
            return $this->error('Selected member is already the group owner.', 422);
        }

        $targetMembership = CommunityGroupMember::query()
            ->where('group_id', $group->id)
            ->where('user_id', $targetUserId)
            ->first();

        if (!$targetMembership) {
            return $this->error('Target user must be a member of this group.', 422);
        }

        DB::transaction(function () use ($group, $targetUserId) {
            CommunityGroupMember::query()
                ->where('group_id', $group->id)
                ->where('user_id', $group->created_by)
                ->update(['role' => 'moderator']);

            CommunityGroupMember::query()
                ->where('group_id', $group->id)
                ->where('user_id', $targetUserId)
                ->update(['role' => 'admin']);

            $group->update(['created_by' => $targetUserId]);
        });

        return $this->success([
            'group_id' => $group->id,
            'created_by' => $targetUserId,
        ], 'Group ownership transferred successfully');
    }

    public function archiveGroup(Request $request, CommunityGroup $group)
    {
        $user = $request->user();
        $isCreator = (int) $group->created_by === (int) $user->id;
        $isAdmin = method_exists($user, 'isAdmin') && $user->isAdmin();

        if (!$isCreator && !$isAdmin) {
            return $this->unauthorized('Only group owner or admin can archive this group.');
        }

        if ($group->status === 'archived') {
            return $this->success(['archived' => true, 'group_id' => $group->id], 'Group is already archived');
        }

        $group->update(['status' => 'archived']);

        CommunityGroupPost::query()
            ->where('group_id', $group->id)
            ->where('status', 'active')
            ->update(['status' => 'hidden']);

        return $this->success(['archived' => true, 'group_id' => $group->id], 'Group archived successfully');
    }

    public function storeGroupPost(Request $request, CommunityGroup $group)
    {
        $user = $request->user();

        if ($group->status !== 'active') {
            return $this->error('This group is not accepting new posts.', 422);
        }

        $isMember = CommunityGroupMember::query()
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$isMember) {
            return $this->error('Join this group before posting.', 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:5|max:4000',
            'image_url' => 'nullable|url|max:1200',
        ]);

        if (CommunityService::isPotentialSpam($user->id, $validated['content'], 'group_post')) {
            return $this->error('Duplicate group post detected. Please try again later.', 429);
        }

        $post = CommunityGroupPost::query()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'content' => trim($validated['content']),
            'image_url' => $validated['image_url'] ?? null,
        ]);

        $group->increment('posts_count');

        return $this->created($post->load('user:id,name,username'), 'Group post created successfully');
    }

    public function commentGroupPost(Request $request, CommunityGroupPost $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:2|max:2000',
        ]);

        $comment = CommunityGroupPostComment::query()->create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'content' => trim($validated['content']),
        ]);

        $post->increment('comments_count');

        if ($post->user_id !== $request->user()->id) {
            CommunityService::notifyUser(
                $post->user_id,
                'group_post_reply',
                'Someone commented on your group post',
                Str::limit($comment->content, 120),
                ['group_post_id' => $post->id, 'comment_id' => $comment->id]
            );
        }

        return $this->created($comment->load('user:id,name,username'), 'Comment added to group post');
    }

    public function deleteGroupPost(Request $request, CommunityGroupPost $post)
    {
        $user = $request->user();
        $isOwner = (int) $post->user_id === (int) $user->id;
        $isAdmin = method_exists($user, 'isAdmin') && $user->isAdmin();

        if (!$isOwner && !$isAdmin) {
            return $this->unauthorized('You can only remove your own group post.');
        }

        if ($post->status !== 'active') {
            return $this->success(['removed' => true, 'post_id' => $post->id], 'Post already removed');
        }

        $post->update(['status' => 'hidden']);

        if ((int) $post->group->posts_count > 0) {
            $post->group->decrement('posts_count');
        }

        return $this->success(['removed' => true, 'post_id' => $post->id], 'Group post removed successfully');
    }

    public function localClubs(Request $request)
    {
        $request->merge(['type' => 'local_club']);
        return $this->groups($request);
    }

    public function mentors(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 15), 40);

        $query = CommunityMentorshipProfile::query()
            ->with(['user:id,name,username'])
            ->where('is_active', true)
            ->orderByDesc('years_experience');

        if ($request->filled('expertise')) {
            $term = Str::lower((string) $request->input('expertise'));
            $query->whereRaw('LOWER(JSON_EXTRACT(expertise, "$")) like ?', ["%{$term}%"]);
        }

        $items = $query->paginate($perPage);

        return $this->paginated($items, 'Mentorship profiles retrieved successfully');
    }

    public function upsertMentorProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'expertise' => 'required|array|min:1|max:10',
            'expertise.*' => 'required|string|max:80',
            'years_experience' => 'required|integer|min:0|max:80',
            'availability_status' => 'required|in:available,limited,unavailable',
            'session_types' => 'required|array|min:1|max:8',
            'session_types.*' => 'required|string|max:80',
            'bio' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
        ]);

        $profile = CommunityMentorshipProfile::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'expertise' => $validated['expertise'],
                'years_experience' => $validated['years_experience'],
                'availability_status' => $validated['availability_status'],
                'session_types' => $validated['session_types'],
                'bio' => $validated['bio'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]
        );

        CommunityService::syncCommunityBadgesForUser($user);

        return $this->success($profile, 'Mentorship profile updated successfully');
    }

    public function requestMentorship(Request $request, User $mentorUser)
    {
        $requester = $request->user();

        if ($mentorUser->id === $requester->id) {
            return $this->error('You cannot request mentorship from yourself.', 422);
        }

        $mentorProfile = CommunityMentorshipProfile::query()
            ->where('user_id', $mentorUser->id)
            ->where('is_active', true)
            ->first();

        if (!$mentorProfile) {
            return $this->error('This mentor is currently unavailable.', 404);
        }

        $validated = $request->validate([
            'message' => 'required|string|min:10|max:2000',
            'preferred_session_type' => 'nullable|string|max:60',
        ]);

        $mentorshipRequest = CommunityMentorshipRequest::query()->create([
            'mentor_user_id' => $mentorUser->id,
            'requester_user_id' => $requester->id,
            'message' => trim($validated['message']),
            'preferred_session_type' => $validated['preferred_session_type'] ?? null,
        ]);

        CommunityService::notifyUser(
            $mentorUser->id,
            'mentorship_request',
            'New mentorship request received',
            Str::limit($validated['message'], 120),
            ['mentorship_request_id' => $mentorshipRequest->id]
        );

        return $this->created($mentorshipRequest, 'Mentorship request sent successfully');
    }

    public function leaderboard()
    {
        return $this->success($this->leaderboardData(50), 'Community leaderboard loaded successfully');
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $q = trim($validated['q']);

        $posts = CommunityDiscussion::query()
            ->where('status', 'active')
            ->where(function ($inner) use ($q) {
                $inner->where('title', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get(['id', 'title', 'category', 'likes_count', 'comments_count', 'created_at']);

        $groups = CommunityGroup::query()
            ->where('status', 'active')
            ->where(fn ($inner) => $inner->where('name', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%"))
            ->limit(20)
            ->get(['id', 'name', 'slug', 'type', 'members_count']);

        $members = User::query()
            ->where(function ($inner) use ($q) {
                $inner->where('name', 'like', "%{$q}%");
                if (DB::getSchemaBuilder()->hasColumn('users', 'username')) {
                    $inner->orWhere('username', 'like', "%{$q}%");
                }
            })
            ->limit(20)
            ->get(['id', 'name', 'username', 'role']);

        $topics = CommunityDiscussion::query()
            ->where('status', 'active')
            ->whereJsonContains('tags', $q)
            ->limit(20)
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->values();

        return $this->success([
            'posts' => $posts,
            'groups' => $groups,
            'members' => $members,
            'topics' => $topics,
        ], 'Community search completed successfully');
    }

    public function reportContent(Request $request)
    {
        $validated = $request->validate([
            'reportable_type' => 'required|in:discussion,discussion_comment,group_post,group_post_comment',
            'reportable_id' => 'required|integer|min:1',
            'reason' => 'required|string|max:80',
            'details' => 'nullable|string|max:2000',
        ]);

        $map = [
            'discussion' => CommunityDiscussion::class,
            'discussion_comment' => CommunityDiscussionComment::class,
            'group_post' => CommunityGroupPost::class,
            'group_post_comment' => CommunityGroupPostComment::class,
        ];

        $modelClass = $map[$validated['reportable_type']] ?? null;
        $target = $modelClass ? $modelClass::query()->find($validated['reportable_id']) : null;

        if (!$target) {
            return $this->notFound('Report target not found');
        }

        $report = CommunityReport::query()->create([
            'reportable_type' => $modelClass,
            'reportable_id' => $target->id,
            'reported_by' => $request->user()->id,
            'reason' => trim($validated['reason']),
            'details' => $validated['details'] ?? null,
        ]);

        return $this->created($report, 'Content reported successfully');
    }

    public function notifications(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 20), 50);
        $items = CommunityNotification::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($perPage);

        return $this->paginated($items, 'Community notifications loaded successfully');
    }

    public function markNotificationRead(Request $request, CommunityNotification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return $this->unauthorized('You cannot update this notification');
        }

        $notification->update(['read_at' => now()]);

        return $this->success($notification, 'Notification marked as read');
    }

    private function leaderboardData(int $limit = 20)
    {
        return User::query()
            ->select(['id', 'name', 'username'])
            ->withCount([
                'communityDiscussions as community_posts_count',
                'communityDiscussionComments as community_comments_count',
            ])
            ->orderByRaw('(community_posts_count * 5 + community_comments_count * 3) DESC')
            ->limit($limit)
            ->get()
            ->map(function ($user, $index) {
                $score = ((int) $user->community_posts_count * 5) + ((int) $user->community_comments_count * 3);

                return [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'posts' => (int) $user->community_posts_count,
                    'comments' => (int) $user->community_comments_count,
                    'helpful_answers' => (int) floor($user->community_comments_count / 2),
                    'score' => $score,
                ];
            });
    }

    private function notifyMentions(string $content, int $actorUserId, string $type, array $data = []): void
    {
        preg_match_all('/@([A-Za-z0-9_.-]{3,30})/', $content, $matches);
        $usernames = collect($matches[1] ?? [])->unique()->values();

        if ($usernames->isEmpty()) {
            return;
        }

        $mentionedUsers = User::query()
            ->whereIn('username', $usernames)
            ->where('id', '!=', $actorUserId)
            ->get(['id', 'username']);

        foreach ($mentionedUsers as $mentionedUser) {
            CommunityService::notifyUser(
                $mentionedUser->id,
                $type,
                'You were mentioned in a community post',
                null,
                $data
            );
        }
    }
}
