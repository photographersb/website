<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventTicket;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventCapacityLockService
{
    // Lock timeout: 15 minutes for payment completion
    private const LOCK_TIMEOUT_MINUTES = 15;
    
    // Distributed lock timeout: 5 seconds
    private const DISTRIBUTED_LOCK_TIMEOUT_SECONDS = 5;

    /**
     * Attempt to reserve capacity for a registration
     * Uses pessimistic locking to prevent race conditions
     * 
     * @param Event $event
     * @param EventTicket $ticket
     * @param int $requestedQty
     * @param int $userId
     * @return array {success: bool, data?: array, error?: string}
     */
    public static function reserve(
        Event $event,
        EventTicket $ticket,
        int $requestedQty,
        int $userId
    ): array {
        try {
            return DB::transaction(function () use ($event, $ticket, $requestedQty, $userId) {
                // 1. Lock the ticket row for update (pessimistic locking)
                $lockedTicket = DB::table('event_tickets')
                    ->where('id', $ticket->id)
                    ->lockForUpdate()
                    ->first();

                if (!$lockedTicket) {
                    return [
                        'success' => false,
                        'error' => 'Ticket not found during reservation'
                    ];
                }

                // 2. Calculate available capacity
                $totalSold = DB::table('event_registrations')
                    ->where('ticket_id', $ticket->id)
                    ->whereIn('status', ['confirmed', 'attended', 'completed', 'no_show'])
                    ->sum('qty');

                $reserved = $lockedTicket->reserved_qty ?? 0;
                $available = $lockedTicket->quantity - $totalSold - $reserved;

                // 3. Verify sufficient capacity
                if ($available < $requestedQty) {
                    return [
                        'success' => false,
                        'error' => sprintf(
                            'Insufficient capacity. Requested: %d, Available: %d',
                            $requestedQty,
                            max(0, $available)
                        )
                    ];
                }

                // 4. Generate lock token for idempotency
                $lockToken = Str::uuid()->toString();

                // 5. Update reserved quantity
                DB::table('event_tickets')
                    ->where('id', $ticket->id)
                    ->update([
                        'reserved_qty' => $reserved + $requestedQty,
                        'capacity_lock_until' => now()->addSeconds(self::DISTRIBUTED_LOCK_TIMEOUT_SECONDS),
                        'updated_at' => now(),
                    ]);

                // 6. Log the reservation
                \Log::info('Capacity reserved', [
                    'ticket_id' => $ticket->id,
                    'event_id' => $event->id,
                    'qty' => $requestedQty,
                    'user_id' => $userId,
                    'lock_token' => $lockToken,
                    'available_before' => $available,
                ]);

                return [
                    'success' => true,
                    'data' => [
                        'lock_token' => $lockToken,
                        'available_capacity' => $available - $requestedQty,
                        'reserved_at' => now(),
                        'expires_at' => now()->addMinutes(self::LOCK_TIMEOUT_MINUTES),
                    ]
                ];
            }, attempts: 3);

        } catch (\Exception $e) {
            \Log::error('Capacity reservation failed', [
                'ticket_id' => $ticket->id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to reserve capacity. Please try again.'
            ];
        }
    }

    /**
     * Confirm a reserved capacity (converts reserved to actual sale)
     * Called after successful payment verification
     * 
     * @param EventRegistration $registration
     * @param string $lockToken
     * @return array {success: bool, error?: string}
     */
    public static function confirmReservation(
        EventRegistration $registration,
        string $lockToken
    ): array {
        try {
            return DB::transaction(function () use ($registration, $lockToken) {
                // 1. Verify lock token matches
                if ($registration->lock_token !== $lockToken) {
                    \Log::warning('Lock token mismatch in confirmation', [
                        'registration_id' => $registration->id,
                        'expected' => $registration->lock_token,
                        'received' => $lockToken,
                    ]);

                    return [
                        'success' => false,
                        'error' => 'Invalid lock token. Reservation may have expired.'
                    ];
                }

                // 2. Verify reservation hasn't expired
                if ($registration->payment_expires_at && now()->greaterThan($registration->payment_expires_at)) {
                    // Release the reserved capacity
                    self::releaseReservation($registration);

                    return [
                        'success' => false,
                        'error' => 'Reservation expired. Please start over.'
                    ];
                }

                // 3. Lock ticket for update
                $ticket = EventTicket::lockForUpdate()->find($registration->ticket_id);

                if (!$ticket) {
                    return [
                        'success' => false,
                        'error' => 'Ticket not found'
                    ];
                }

                // 4. Verify reserved capacity still exists
                if ($ticket->reserved_qty < $registration->qty) {
                    return [
                        'success' => false,
                        'error' => 'Reserved capacity was released due to timeout'
                    ];
                }

                // 5. Release reserved capacity (it's now confirmed in the registration record)
                $ticket->update([
                    'reserved_qty' => max(0, $ticket->reserved_qty - $registration->qty),
                    'updated_at' => now(),
                ]);

                // 6. Clear lock info from registration
                $registration->update([
                    'lock_token' => null,
                    'locked_at' => null,
                    'payment_expires_at' => null,
                ]);

                \Log::info('Capacity reservation confirmed', [
                    'registration_id' => $registration->id,
                    'ticket_id' => $ticket->id,
                    'qty' => $registration->qty,
                ]);

                return ['success' => true];

            }, attempts: 3);

        } catch (\Exception $e) {
            \Log::error('Capacity confirmation failed', [
                'registration_id' => $registration->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to confirm reservation'
            ];
        }
    }

    /**
     * Release a reserved capacity (when payment fails or times out)
     * 
     * @param EventRegistration $registration
     * @return array {success: bool}
     */
    public static function releaseReservation(EventRegistration $registration): array
    {
        try {
            return DB::transaction(function () use ($registration) {
                $ticket = EventTicket::lockForUpdate()->find($registration->ticket_id);

                if (!$ticket) {
                    return ['success' => false];
                }

                $ticket->update([
                    'reserved_qty' => max(0, $ticket->reserved_qty - $registration->qty),
                    'updated_at' => now(),
                ]);

                $registration->update([
                    'lock_token' => null,
                    'locked_at' => null,
                    'payment_expires_at' => null,
                ]);

                \Log::info('Capacity reservation released', [
                    'registration_id' => $registration->id,
                    'ticket_id' => $ticket->id,
                    'qty' => $registration->qty,
                ]);

                return ['success' => true];

            }, attempts: 3);

        } catch (\Exception $e) {
            \Log::error('Capacity release failed', [
                'registration_id' => $registration->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false];
        }
    }

    /**
     * Clean up expired reservations (scheduled task)
     * Release capacity for registrations that exceeded payment timeout
     * 
     * @return array {released: int, failed: int}
     */
    public static function cleanupExpiredReservations(): array
    {
        try {
            $expired = EventRegistration::where('payment_expires_at', '<', now())
                ->where('status', 'pending_payment')
                ->get();

            $released = 0;
            $failed = 0;

            foreach ($expired as $registration) {
                if (self::releaseReservation($registration)['success']) {
                    $released++;
                } else {
                    $failed++;
                }
            }

            \Log::info('Completed cleanup of expired reservations', [
                'released' => $released,
                'failed' => $failed,
            ]);

            return compact('released', 'failed');

        } catch (\Exception $e) {
            \Log::error('Cleanup of expired reservations failed: ' . $e->getMessage());
            return ['released' => 0, 'failed' => 0];
        }
    }

    /**
     * Check current capacity status for a ticket
     * Useful for real-time availability updates
     * 
     * @param EventTicket $ticket
     * @return array {total: int, sold: int, reserved: int, available: int}
     */
    public static function getCapacityStatus(EventTicket $ticket): array
    {
        try {
            // Use fresh data
            $ticket->refresh();

            $sold = EventRegistration::where('ticket_id', $ticket->id)
                ->where('status', '!=', 'cancelled')
                ->sum('qty');

            $reserved = (int) ($ticket->reserved_qty ?? 0);
            $available = $ticket->quantity - $sold - $reserved;

            return [
                'total' => $ticket->quantity,
                'sold' => $sold,
                'reserved' => $reserved,
                'available' => max(0, $available),
            ];

        } catch (\Exception $e) {
            \Log::error('Failed to get capacity status: ' . $e->getMessage());
            return [
                'total' => 0,
                'sold' => 0,
                'reserved' => 0,
                'available' => 0,
            ];
        }
    }
}
