<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\Photographer;
use App\Models\User;

class CompetitionPolicy
{
    /**
     * Determine if user can view the competition
     */
    public function view(User $user, Competition $competition): bool
    {
        // Anyone can view published competitions
        if ($competition->status === 'published') {
            return true;
        }

        // Draft/archived competitions: only organizer and admins
        return $this->isOrganizer($user, $competition) || $this->isAdmin($user);
    }

    /**
     * Determine if user can create competitions (photographers only)
     */
    public function create(User $user): bool
    {
        // Only verified photographers can create competitions
        $photographer = Photographer::where('user_id', $user->id)->first();
        return $photographer && $photographer->is_verified;
    }

    /**
     * Determine if user can update the competition
     */
    public function update(User $user, Competition $competition): bool
    {
        // Only organizer or admins can update
        return $this->isOrganizer($user, $competition) || $this->isAdmin($user);
    }

    /**
     * Determine if user can delete the competition
     */
    public function delete(User $user, Competition $competition): bool
    {
        // Only admins can delete competitions
        // Photographers can only delete drafts they own
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->isOrganizer($user, $competition) && $competition->status === 'draft';
    }

    /**
     * Determine if user can publish the competition
     */
    public function publish(User $user, Competition $competition): bool
    {
        // Only admins can publish
        return $this->isAdmin($user);
    }

    /**
     * Determine if user can manage judges for the competition
     */
    public function manageJudges(User $user, Competition $competition): bool
    {
        // Only admins can assign judges
        return $this->isAdmin($user);
    }

    /**
     * Check if user is the competition organizer
     */
    protected function isOrganizer(User $user, Competition $competition): bool
    {
        $photographer = Photographer::where('user_id', $user->id)->first();
        return $photographer && $competition->organizer_id === $photographer->id;
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }
}
