<?php

namespace App\Services;

use App\Models\EventRegistration;
use Illuminate\Support\Facades\Log;
use App\Services\EventCapacityLockService;

class BookingStatusValidator
{
    /**
     * Valid state transitions for event registrations
     * Maps current status => array of allowed next statuses
     */
    private const VALID_TRANSITIONS = [
        'pending_payment' => ['confirmed', 'cancelled', 'failed'],
        'confirmed' => ['attended', 'cancelled', 'no_show'],
        'attended' => ['completed'],
        'completed' => [],
        'cancelled' => [],
        'failed' => ['pending_payment'],
        'no_show' => [],
    ];

    /**
     * Booking status lifecycle:
     * 1. pending_payment -> Initial state when booking created but payment not verified
     * 2. confirmed     -> Payment verified and booking confirmed
     * 3. attended      -> User attended the event
     * 4. no_show       -> Booking was confirmed but user didn't attend
     * 5. completed     -> Event complete, registration finalized
     * 6. cancelled     -> User cancelled the booking
     * 7. failed        -> Payment verification failed
     */

    /**
     * Validate if a status transition is allowed
     * 
     * @param EventRegistration $registration
     * @param string $newStatus
     * @return array {valid: bool, error?: string}
     */
    public static function validateTransition(EventRegistration $registration, string $newStatus): array
    {
        $currentStatus = $registration->status;

        // Verify new status exists
        if (!self::isValidStatus($newStatus)) {
            return [
                'valid' => false,
                'error' => "Invalid status: {$newStatus}"
            ];
        }

        // Check if transition is allowed
        $allowedTransitions = self::VALID_TRANSITIONS[$currentStatus] ?? [];

        if (!in_array($newStatus, $allowedTransitions, true)) {
            return [
                'valid' => false,
                'error' => "Cannot transition from '{$currentStatus}' to '{$newStatus}'. " .
                          "Allowed transitions: " . implode(', ', $allowedTransitions)
            ];
        }

        // Additional business logic validation
        return self::validateBusinessRules($registration, $newStatus);
    }

    /**
     * Perform status transition with validation
     * 
     * @param EventRegistration $registration
     * @param string $newStatus
     * @param array $additionalData Additional fields to update
     * @return array {success: bool, error?: string, data?: array}
     */
    public static function transitionStatus(
        EventRegistration $registration,
        string $newStatus,
        array $additionalData = []
    ): array {
        // Validate transition
        $validation = self::validateTransition($registration, $newStatus);
        if (!$validation['valid']) {
            Log::warning('Invalid status transition attempted', [
                'registration_id' => $registration->id,
                'from' => $registration->status,
                'to' => $newStatus,
                'error' => $validation['error'],
            ]);
            return ['success' => false, 'error' => $validation['error']];
        }

        try {
            // Prepare update data
            $updateData = array_merge($additionalData, ['status' => $newStatus]);

            // Handle specific status transitions
            $updateData = self::handleTransitionSideEffects($registration, $newStatus, $updateData);

            // Update registration
            $registration->update($updateData);

            Log::info('Status transition successful', [
                'registration_id' => $registration->id,
                'from' => $registration->getOriginal('status'),
                'to' => $newStatus,
                'user_id' => $registration->user_id,
                'event_id' => $registration->event_id,
            ]);

            return [
                'success' => true,
                'data' => [
                    'status' => $newStatus,
                    'updated_at' => now(),
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Status transition failed', [
                'registration_id' => $registration->id,
                'from' => $registration->status,
                'to' => $newStatus,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to update booking status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate additional business rules for status transitions
     * 
     * @param EventRegistration $registration
     * @param string $newStatus
     * @return array {valid: bool, error?: string}
     */
    private static function validateBusinessRules(
        EventRegistration $registration,
        string $newStatus
    ): array {
        $event = $registration->event;

        switch ($newStatus) {
            case 'confirmed':
                // Can only confirm if payment is verified
                if (!$registration->payment || $registration->payment->verification_status !== 'verified') {
                    return [
                        'valid' => false,
                        'error' => 'Payment must be verified before confirming booking'
                    ];
                }
                break;

            case 'completed':
                // Event must have ended
                if ($event->end_date && now()->lessThan($event->end_date)) {
                    return [
                        'valid' => false,
                        'error' => 'Cannot complete booking before event ends'
                    ];
                }
                break;

            case 'cancelled':
                // Check if cancellation is allowed for event
                if ($event->allow_cancellation === false) {
                    return [
                        'valid' => false,
                        'error' => 'Cancellations are not allowed for this event'
                    ];
                }

                // Check cancellation deadline
                if ($event->cancellation_deadline && now()->greaterThan($event->cancellation_deadline)) {
                    return [
                        'valid' => false,
                        'error' => 'Cancellation deadline has passed'
                    ];
                }
                break;

            case 'attended':
                // Must be confirmed to mark as attended
                if ($registration->status !== 'confirmed') {
                    return [
                        'valid' => false,
                        'error' => 'Only confirmed bookings can be marked as attended'
                    ];
                }
                break;

            case 'no_show':
                // Must be confirmed to mark as no-show
                if ($registration->status !== 'confirmed') {
                    return [
                        'valid' => false,
                        'error' => 'Only confirmed bookings can be marked as no-show'
                    ];
                }

                // Event must have started
                if ($event->start_date && now()->lessThan($event->start_date)) {
                    return [
                        'valid' => false,
                        'error' => 'Cannot mark as no-show before event starts'
                    ];
                }
                break;
        }

        return ['valid' => true];
    }

    /**
     * Handle side effects of status transitions
     * (e.g., refund on cancellation, release capacity, etc.)
     * 
     * @param EventRegistration $registration
     * @param string $newStatus
     * @param array $updateData
     * @return array Updated data
     */
    private static function handleTransitionSideEffects(
        EventRegistration $registration,
        string $newStatus,
        array $updateData
    ): array {
        switch ($newStatus) {
            case 'cancelled':
                // Release seat capacity if payment was released
                if ($registration->lock_token) {
                    EventCapacityLockService::releaseReservation($registration);
                }

                break;

            case 'confirmed':
                // Release transaction lock since payment is confirmed
                if ($registration->lock_token) {
                    EventCapacityLockService::confirmReservation($registration, $registration->lock_token);
                }
                break;

            case 'failed':
                // Release reserved capacity
                if ($registration->lock_token) {
                    EventCapacityLockService::releaseReservation($registration);
                }
                break;
        }

        return $updateData;
    }

    /**
     * Check if a status is valid
     * 
     * @param string $status
     * @return bool
     */
    public static function isValidStatus(string $status): bool
    {
        return in_array($status, array_keys(self::VALID_TRANSITIONS), true);
    }

    /**
     * Get all valid statuses
     * 
     * @return array
     */
    public static function getAllValidStatuses(): array
    {
        return array_keys(self::VALID_TRANSITIONS);
    }

    /**
     * Get allowed transitions for a given status
     * 
     * @param string $status
     * @return array
     */
    public static function getAllowedTransitions(string $status): array
    {
        return self::VALID_TRANSITIONS[$status] ?? [];
    }

    /**
     * Get status transition diagram (for documentation/UI)
     * 
     * @return array
     */
    public static function getTransitionDiagram(): array
    {
        return self::VALID_TRANSITIONS;
    }
}
