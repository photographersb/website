<?php

namespace App\Policies;

use App\Models\BookingRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BookingRequest $bookingRequest): bool
    {
        // Client, photographer, or admin can view
        return $user->id === $bookingRequest->client_user_id ||
               $user->id === $bookingRequest->photographer_user_id ||
               $user->is_admin || $user->is_super_admin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any user can create a booking request
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BookingRequest $bookingRequest): bool
    {
        return false; // Cannot update; must use state transitions
    }

    /**
     * Determine whether the user can accept the booking.
     */
    public function accept(User $user, BookingRequest $bookingRequest): bool
    {
        return $user->id === $bookingRequest->photographer_user_id && $bookingRequest->isPending();
    }

    /**
     * Determine whether the user can decline the booking.
     */
    public function decline(User $user, BookingRequest $bookingRequest): bool
    {
        return $user->id === $bookingRequest->photographer_user_id && $bookingRequest->isPending();
    }

    /**
     * Determine whether the user can cancel the booking.
     */
    public function cancel(User $user, BookingRequest $bookingRequest): bool
    {
        return ($user->id === $bookingRequest->client_user_id || $user->id === $bookingRequest->photographer_user_id) &&
               $bookingRequest->canBeCancelled();
    }

    /**
     * Determine whether the user can complete the booking.
     */
    public function complete(User $user, BookingRequest $bookingRequest): bool
    {
        return $user->id === $bookingRequest->photographer_user_id && $bookingRequest->isAccepted();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BookingRequest $bookingRequest): bool
    {
        return false; // Cannot delete; only mark as cancelled
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BookingRequest $bookingRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BookingRequest $bookingRequest): bool
    {
        return false;
    }
}
