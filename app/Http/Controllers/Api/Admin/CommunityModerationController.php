<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\CommunityDiscussion;
use App\Models\CommunityModerationAction;
use App\Models\CommunityReport;
use App\Models\User;
use Illuminate\Http\Request;

class CommunityModerationController extends Controller
{
    use ApiResponse;

    private function ensureModerator(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return $this->unauthorized('Moderator or admin access required');
        }

        return null;
    }

    public function reports(Request $request)
    {
        if ($response = $this->ensureModerator($request)) {
            return $response;
        }

        $perPage = min((int) $request->input('per_page', 30), 100);
        $items = CommunityReport::query()
            ->with(['reporter:id,name,username'])
            ->latest()
            ->paginate($perPage);

        return $this->paginated($items, 'Community reports loaded successfully');
    }

    public function discussions(Request $request)
    {
        if ($response = $this->ensureModerator($request)) {
            return $response;
        }

        $perPage = min((int) $request->input('per_page', 30), 100);
        $items = CommunityDiscussion::query()
            ->with(['user:id,name,username'])
            ->latest('last_activity_at')
            ->paginate($perPage);

        return $this->paginated($items, 'Community discussions loaded successfully');
    }

    public function resolveReport(Request $request, CommunityReport $report)
    {
        if ($response = $this->ensureModerator($request)) {
            return $response;
        }

        $validated = $request->validate([
            'status' => 'required|in:resolved,dismissed',
        ]);

        $report->update([
            'status' => $validated['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        CommunityModerationAction::query()->create([
            'moderator_user_id' => $request->user()->id,
            'action_type' => 'resolve_report',
            'reason' => 'Report marked as ' . $validated['status'],
            'metadata' => ['report_id' => $report->id],
        ]);

        return $this->success($report, 'Report updated successfully');
    }

    public function featureDiscussion(Request $request, CommunityDiscussion $discussion)
    {
        if ($response = $this->ensureModerator($request)) {
            return $response;
        }

        $validated = $request->validate([
            'is_featured' => 'required|boolean',
        ]);

        $discussion->update(['is_featured' => $validated['is_featured']]);

        CommunityModerationAction::query()->create([
            'moderator_user_id' => $request->user()->id,
            'target_user_id' => $discussion->user_id,
            'action_type' => $validated['is_featured'] ? 'feature_discussion' : 'unfeature_discussion',
            'metadata' => ['discussion_id' => $discussion->id],
        ]);

        return $this->success($discussion, 'Discussion feature state updated successfully');
    }

    public function banUser(Request $request, User $user)
    {
        if ($response = $this->ensureModerator($request)) {
            return $response;
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'is_suspended' => 'nullable|boolean',
        ]);

        $user->update([
            'is_suspended' => $validated['is_suspended'] ?? true,
            'suspension_reason' => $validated['reason'],
            'suspended_at' => now(),
        ]);

        CommunityModerationAction::query()->create([
            'moderator_user_id' => $request->user()->id,
            'target_user_id' => $user->id,
            'action_type' => 'ban_user',
            'reason' => $validated['reason'],
        ]);

        return $this->success($user->only(['id', 'name', 'is_suspended', 'suspension_reason', 'suspended_at']), 'User moderation action applied');
    }
}
