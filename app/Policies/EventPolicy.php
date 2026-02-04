<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\Photographer;
use App\Models\User;

class EventPolicy
{
    /**
     * Determine if user can view the event
     */
    public function view(User $user, Event $event): bool
    {
        // Anyone can view published events
        if ($event->status === 'published') {
            return true;
        }

        // Draft events: only organizer and admins
        if ($event->status === 'draft') {
            return $this->isOrganizer($user, $event) || $this->isAdmin($user);
        }

        // Other statuses: only organizer and admins
        return $this->isOrganizer($user, $event) || $this->isAdmin($user);
    }

    /**
     * Determine if user can create events
     */
    public function create(User $user): bool
    {
        // Only verified photographers can create events
        $photographer = Photographer::where('user_id', $user->id)->first();
        return $photographer && $photographer->is_verified;
    }

    /**
     * Determine if user can update the event
     */
    public function update(User $user, Event $event): bool
    {
        // Only organizer or admins can update
        return $this->isOrganizer($user, $event) || $this->isAdmin($user);
    }

    /**
     * Determine if user can delete the event
     */
    public function delete(User $user, Event $event): bool
    {
        // Only organizer or admins can delete
        return $this->isOrganizer($user, $event) || $this->isAdmin($user);
    }

    /**
     * Determine if user can publish the event
     */
    public function publish(User $user, Event $event): bool
    {
        // Only admins can publish
        return $this->isAdmin($user);
    }

    /**
     * Check if user is the event organizer
     */
    protected function isOrganizer(User $user, Event $event): bool
    {
        $photographer = Photographer::where('user_id', $user->id)->first();
        return $photographer && $event->organizer_id === $photographer->id;
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Determine if user can perform check-in (attendance scanning)
     */
    public function checkIn(User $user, Event $event): bool
    {
        // Admin can always check-in
        if ($this->isAdmin($user)) {
            return true;
        }

        // Event organizer can check-in their own events
        return $this->isOrganizer($user, $event);
    }

    /**
     * Determine if user can manage event registrations
     */
    public function manageRegistrations(User $user, Event $event): bool
    {
        // Admin can manage all registrations
        if ($this->isAdmin($user)) {
            return true;
        }

        // Organizer can manage own event registrations
        return $this->isOrganizer($user, $event);
    }

    /**
     * Determine if user can view attendance logs
     */
    public function viewAttendanceLogs(User $user, Event $event): bool
    {
        // Admin can view all logs
        if ($this->isAdmin($user)) {
            return true;
        }

        // Organizer can view own event logs
        return $this->isOrganizer($user, $event);
    }

    /**
     * Determine if user can export attendance data
     */
    public function exportAttendance(User $user, Event $event): bool
    {
        // Same authorization as viewing attendance logs
        return $this->viewAttendanceLogs($user, $event);
    }

    /**
     * Determine if user can register for the event
     */
    public function register(User $user, Event $event): bool
    {
        // Must be published and booking must be open
        if ($event->status !== 'published' || !$event->isBookingOpen()) {
            return false;
        }

        // Must have capacity
        if (!$event->hasCapacityFor()) {
            return false;
        }

        // Cannot register twice
        if ($event->getUserRegistration($user->id)) {
            return false;
        }

        return true;
    }

    /**
     * Determine if user can cancel their registration
     */
    public function cancelRegistration(User $user, $registration): bool
    {
        // User can only cancel their own registration
        return $user->id === $registration->user_id;
    }
}
