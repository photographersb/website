<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Transaction;
use App\Models\EventPayment;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class AdminTransactionController extends Controller
{
    use ApiResponse;
    /**
     * Get all transactions (booking & event payments) with filters and pagination
     */
    public function index(Request $request)
    {
        try {
            $type = $request->input('type'); // null/all, 'booking', 'event_tickets'
            $status = $request->input('status');
            $search = $request->input('search');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 15);

            // Get transactions (booking payments)
            $bookingQuery = Transaction::with(['user']);
            $eventQuery = EventPayment::with(['user', 'event']);

            // Filter type
            if ($type && $type !== 'all') {
                if ($type === 'booking') {
                    $eventQuery = null;
                } elseif ($type === 'event_tickets') {
                    $bookingQuery = null;
                }
            }

            // Apply filters to booking transactions
            if ($bookingQuery) {
                if ($status) {
                    $bookingQuery->where('status', $status);
                }
                if ($search) {
                    $bookingQuery->where(function($q) use ($search) {
                        $q->where('transaction_id', 'LIKE', "%{$search}%")
                          ->orWhere('gateway_transaction_id', 'LIKE', "%{$search}%")
                          ->orWhereHas('user', function($userQ) use ($search) {
                              $userQ->where('name', 'LIKE', "%{$search}%")
                                    ->orWhere('email', 'LIKE', "%{$search}%");
                          });
                    });
                }
                if ($dateFrom) {
                    $bookingQuery->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $bookingQuery->where('created_at', '<=', $dateTo);
                }
            }

            // Apply filters to event payments
            if ($eventQuery) {
                if ($status) {
                    $eventQuery->where('status', $status);
                }
                if ($search) {
                    $eventQuery->where(function($q) use ($search) {
                        $q->where('transaction_id', 'LIKE', "%{$search}%")
                          ->orWhere('trx_id', 'LIKE', "%{$search}%")
                          ->orWhereHas('user', function($userQ) use ($search) {
                              $userQ->where('name', 'LIKE', "%{$search}%")
                                    ->orWhere('email', 'LIKE', "%{$search}%");
                          });
                    });
                }
                if ($dateFrom) {
                    $eventQuery->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $eventQuery->where('created_at', '<=', $dateTo);
                }
            }

            // Get counts/stats from both sources
            $totalRevenue = 0;
            $todayRevenue = 0;
            $monthlyRevenue = 0;
            $stats = ['total' => 0, 'completed' => 0, 'pending' => 0, 'failed' => 0];

            if ($bookingQuery) {
                $bookingStats = (clone $bookingQuery)->select('status')->get()->groupBy('status')->map->count();
                $stats['completed'] += $bookingStats->get('completed', 0);
                $stats['pending'] += $bookingStats->get('pending', 0);
                $stats['failed'] += $bookingStats->get('failed', 0);
                $stats['total'] += (clone $bookingQuery)->count();
                
                $completedQuery = (clone $bookingQuery)->where('status', 'completed');
                $totalRevenue += $completedQuery->sum('amount') ?? 0;
                $todayRevenue += (clone $completedQuery)->whereDate('created_at', today())->sum('amount') ?? 0;
                $monthlyRevenue += (clone $completedQuery)->whereMonth('created_at', now()->month)->sum('amount') ?? 0;
            }

            if ($eventQuery) {
                $eventStats = (clone $eventQuery)->select('status')->get()->groupBy('status')->map->count();
                $stats['completed'] += $eventStats->get('completed', 0);
                $stats['pending'] += $eventStats->get('pending', 0);
                $stats['failed'] += $eventStats->get('failed', 0);
                $stats['total'] += (clone $eventQuery)->count();
                
                $completedQuery = (clone $eventQuery)->where('status', 'completed');
                $totalRevenue += $completedQuery->sum('amount') ?? 0;
                $todayRevenue += (clone $completedQuery)->whereDate('created_at', today())->sum('amount') ?? 0;
                $monthlyRevenue += (clone $completedQuery)->whereMonth('created_at', now()->month)->sum('amount') ?? 0;
            }

            $stats['total_revenue'] = $totalRevenue;
            $stats['today_revenue'] = $todayRevenue;
            $stats['monthly_revenue'] = $monthlyRevenue;

            // Get paginated results, combining both types
            $results = [];

            if ($bookingQuery) {
                $bookings = $bookingQuery->orderBy('created_at', 'desc')->get();
                foreach ($bookings as $booking) {
                    $results[] = (object)[
                        'id' => 'booking_' . $booking->id,
                        'type' => 'booking',
                        'transaction_id' => $booking->transaction_id,
                        'user_id' => $booking->user_id,
                        'user' => $booking->user,
                        'event_id' => null,
                        'event' => null,
                        'amount' => $booking->amount,
                        'status' => $booking->status,
                        'method' => $booking->payment_gateway,
                        'sender_number' => null,
                        'trx_id' => $booking->gateway_transaction_id,
                        'screenshot_path' => null,
                        'created_at' => $booking->created_at,
                        'verified_at' => null,
                        'verified_by_user_id' => null,
                    ];
                }
            }

            if ($eventQuery) {
                $events = $eventQuery->get();
                foreach ($events as $event) {
                    $results[] = (object)[
                        'id' => 'event_' . $event->id,
                        'type' => 'event_tickets',
                        'transaction_id' => $event->transaction_id,
                        'user_id' => $event->user_id,
                        'user' => $event->user,
                        'event_id' => $event->event_id,
                        'event' => $event->event,
                        'amount' => $event->amount,
                        'status' => $event->status,
                        'method' => $event->method,
                        'sender_number' => $event->sender_number,
                        'trx_id' => $event->trx_id,
                        'screenshot_path' => $event->screenshot_path,
                        'created_at' => $event->created_at,
                        'verified_at' => $event->verified_at,
                        'verified_by_user_id' => $event->verified_by_user_id,
                    ];
                }
            }

            // Sort combined results by created_at desc
            usort($results, function($a, $b) {
                return $b->created_at <=> $a->created_at;
            });

            // Paginate manually
            $total = count($results);
            $lastPage = ceil($total / $perPage);
            $offset = ($page - 1) * $perPage;
            $items = array_slice($results, $offset, $perPage);

            return $this->success($items, 'Transactions retrieved successfully', 200, [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch transactions: ' . $e->getMessage());
            return $this->error('Failed to fetch transactions', 500);
        }
    }

    /**
     * Get transaction statistics
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => Transaction::count(),
                'completed' => Transaction::where('status', 'completed')->count(),
                'pending' => Transaction::where('status', 'pending')->count(),
                'failed' => Transaction::where('status', 'failed')->count(),
                'refunded' => Transaction::where('status', 'refunded')->count(),
                'total_revenue' => Transaction::where('status', 'completed')->sum('amount'),
                'today_revenue' => Transaction::where('status', 'completed')
                    ->whereDate('created_at', today())
                    ->sum('amount'),
                'monthly_revenue' => Transaction::where('status', 'completed')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount'),
                'yearly_revenue' => Transaction::where('status', 'completed')
                    ->whereYear('created_at', now()->year)
                    ->sum('amount'),
            ];

            return $this->success($stats, 'Transaction statistics retrieved');
        } catch (\Exception $e) {
            Log::error('Failed to fetch transaction stats: ' . $e->getMessage());
            return $this->error('Failed to fetch stats', 500);
        }
    }

    /**
     * Get single transaction details
     */
    public function show($id)
    {
        try {
            $transaction = Transaction::with(['user'])
                ->findOrFail($id);

            return $this->success($transaction, 'Transaction retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to fetch transaction: ' . $e->getMessage());
            return $this->notFound('Transaction not found');
        }
    }

    /**
     * Update transaction status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded,cancelled'
        ]);

        try {
            $transaction = Transaction::findOrFail($id);
            $transaction->update(['status' => $validated['status']]);

            Log::info("Transaction #{$id} status updated to {$validated['status']} by admin " . auth()->user()->id);

            return $this->success($transaction->load('user'), 'Transaction status updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update transaction status: ' . $e->getMessage());
            return $this->error('Failed to update transaction', 500);
        }
    }

    /**
     * Mark transaction as refunded
     */
    public function refund(Request $request, $id)
    {
        $validated = $request->validate([
            'refund_reason' => 'nullable|string|max:500'
        ]);

        try {
            $transaction = Transaction::findOrFail($id);
            
            if ($transaction->status !== 'completed') {
                return $this->validationError(['transaction' => 'Only completed transactions can be refunded'], 'Validation failed');
            }

            $transaction->update([
                'status' => 'refunded',
                'refund_reason' => $validated['refund_reason'] ?? null,
                'refunded_at' => now()
            ]);

            Log::info("Transaction #{$id} refunded by admin " . auth()->user()->id);

            return $this->success($transaction, 'Transaction refunded successfully');
        } catch (\Exception $e) {
            Log::error('Failed to refund transaction: ' . $e->getMessage());
            return $this->error('Failed to refund transaction', 500);
        }
    }

    /**
     * Export transactions report
     */
    public function export(Request $request)
    {
        // This would generate CSV/Excel export
        // For now, return JSON that can be processed by frontend
        try {
            $query = Transaction::with(['user']);

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            $transactions = $query->orderBy('created_at', 'desc')->get();

            return $this->success([
                'transactions' => $transactions,
                'export_date' => now()->toDateTimeString()
            ], 'Transactions exported successfully');
        } catch (\Exception $e) {
            Log::error('Failed to export transactions: ' . $e->getMessage());
            return $this->error('Failed to export transactions', 500);
        }
    }

    /**
     * Approve an event payment (sets to completed and confirms registration)
     */
    public function approveEventPayment(Request $request, $paymentId)
    {
        try {
            $payment = EventPayment::findOrFail($paymentId);

            if ($payment->status !== 'pending') {
                return $this->validationError(
                    ['payment' => 'Only pending payments can be approved'],
                    'Validation failed'
                );
            }

            // Update payment status
            $payment->update([
                'status' => 'completed',
                'verified_by_user_id' => auth()->user()->id,
                'verified_at' => now(),
                'admin_note' => $request->input('admin_note'),
            ]);

            // Confirm the registration
            if ($payment->registration) {
                $payment->registration->update([
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                ]);

                // Increase sold_count on approval
                $ticket = $payment->registration->ticket;
                if ($ticket) {
                    $qty = $payment->registration->qty ?? 1;
                    $ticket->increment('sold_count', $qty);
                    Log::info("Increased ticket #{$ticket->id} sold_count by {$qty} (now: {$ticket->fresh()->sold_count})");
                }
            }

            Log::info("Event payment #{$paymentId} approved by admin " . auth()->user()->id);

            return $this->success($payment->load(['user', 'event']), 'Event payment approved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to approve event payment: ' . $e->getMessage());
            return $this->error('Failed to approve payment', 500);
        }
    }

    /**
     * Reject an event payment
     */
    public function rejectEventPayment(Request $request, $paymentId)
    {
        try {
            $payment = EventPayment::findOrFail($paymentId);

            if ($payment->status !== 'pending') {
                return $this->validationError(
                    ['payment' => 'Only pending payments can be rejected'],
                    'Validation failed'
                );
            }

            // Update payment status
            $payment->update([
                'status' => 'rejected',
                'verified_by_user_id' => auth()->user()->id,
                'verified_at' => now(),
                'admin_note' => $request->input('admin_note'),
            ]);

            // Cancel the registration and restore ticket availability
            if ($payment->registration) {
                $payment->registration->update(['status' => 'cancelled']);

                // Decrease sold_count back on rejection
                $ticket = $payment->registration->ticket;
                if ($ticket) {
                    $qty = $payment->registration->qty ?? 1;
                    $newSoldCount = max(0, $ticket->sold_count - $qty);
                    $ticket->update(['sold_count' => $newSoldCount]);
                    Log::info("Decreased ticket #{$ticket->id} sold_count by {$qty} (now: {$ticket->fresh()->sold_count})");
                }
            }

            Log::info("Event payment #{$paymentId} rejected by admin " . auth()->user()->id);

            return $this->success($payment->load(['user', 'event']), 'Event payment rejected successfully');
        } catch (\Exception $e) {
            Log::error('Failed to reject event payment: ' . $e->getMessage());
            return $this->error('Failed to reject payment', 500);
        }
    }

    /**
     * Cancel an approved event payment and restore ticket availability
     */
    public function cancelEventPayment(Request $request, $paymentId)
    {
        try {
            $payment = EventPayment::findOrFail($paymentId);

            if ($payment->status !== 'completed') {
                return $this->validationError(
                    ['payment' => 'Only approved payments can be cancelled'],
                    'Validation failed'
                );
            }

            // Update payment status to cancelled
            $payment->update([
                'status' => 'cancelled',
                'admin_note' => $request->input('admin_note', 'Payment cancelled by admin'),
            ]);

            // Cancel the registration and restore ticket availability
            if ($payment->registration) {
                $payment->registration->update(['status' => 'cancelled']);

                // Restore sold_count on cancellation
                $ticket = $payment->registration->ticket;
                if ($ticket) {
                    $qty = $payment->registration->qty ?? 1;
                    $newSoldCount = max(0, $ticket->sold_count - $qty);
                    $ticket->update(['sold_count' => $newSoldCount]);
                    Log::info("Restored ticket #{$ticket->id} sold_count by {$qty} (now: {$ticket->fresh()->sold_count})");
                }
            }

            Log::info("Event payment #{$paymentId} cancelled by admin " . auth()->user()->id);

            return $this->success($payment->load(['user', 'event', 'registration']), 'Event payment cancelled and ticket availability restored');
        } catch (\Exception $e) {
            Log::error('Failed to cancel event payment: ' . $e->getMessage());
            return $this->error('Failed to cancel payment', 500);
        }
    }
}
