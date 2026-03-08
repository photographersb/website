<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Transaction;
use App\Models\EventPayment;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
            $gateway = $request->input('gateway');
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
                if ($gateway) {
                    $bookingQuery->where('payment_gateway', $gateway);
                }
            }

            // Apply filters to event payments
            if ($eventQuery) {
                if ($status) {
                    if ($status === 'failed') {
                        // Event payments use "rejected" to represent a failed manual verification.
                        $eventQuery->whereIn('status', ['failed', 'rejected']);
                    } else {
                        $eventQuery->where('status', $status);
                    }
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
                if ($gateway) {
                    $eventQuery->where('method', $gateway);
                }
            }

            // Get counts/stats from both sources
            $totalRevenue = 0;
            $todayRevenue = 0;
            $monthlyRevenue = 0;
            $stats = ['total' => 0, 'completed' => 0, 'pending' => 0, 'failed' => 0];

            if ($bookingQuery) {
                $stats['completed'] += (clone $bookingQuery)->where('status', 'completed')->count();
                $stats['pending'] += (clone $bookingQuery)->where('status', 'pending')->count();
                $stats['failed'] += (clone $bookingQuery)->where('status', 'failed')->count();
                $stats['total'] += (clone $bookingQuery)->count();
                
                $completedQuery = (clone $bookingQuery)->where('status', 'completed');
                $totalRevenue += $completedQuery->sum('amount') ?? 0;
                $todayRevenue += (clone $completedQuery)->whereDate('created_at', today())->sum('amount') ?? 0;
                $monthlyRevenue += (clone $completedQuery)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount') ?? 0;
            }

            if ($eventQuery) {
                $stats['completed'] += (clone $eventQuery)->where('status', 'completed')->count();
                $stats['pending'] += (clone $eventQuery)->where('status', 'pending')->count();
                $stats['failed'] += (clone $eventQuery)->whereIn('status', ['failed', 'rejected'])->count();
                $stats['total'] += (clone $eventQuery)->count();
                
                $completedQuery = (clone $eventQuery)->where('status', 'completed');
                $totalRevenue += $completedQuery->sum('amount') ?? 0;
                $todayRevenue += (clone $completedQuery)->whereDate('created_at', today())->sum('amount') ?? 0;
                $monthlyRevenue += (clone $completedQuery)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount') ?? 0;
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
            $transactionTotal = Transaction::count();
            $eventPaymentTotal = EventPayment::count();

            $transactionCompleted = Transaction::where('status', 'completed')->count();
            $eventPaymentCompleted = EventPayment::where('status', 'completed')->count();

            $transactionPending = Transaction::where('status', 'pending')->count();
            $eventPaymentPending = EventPayment::where('status', 'pending')->count();

            $transactionFailed = Transaction::where('status', 'failed')->count();
            $eventPaymentRejected = EventPayment::where('status', 'rejected')->count();

            $transactionRefunded = Transaction::where('status', 'refunded')->count();
            $eventPaymentCancelled = EventPayment::where('status', 'cancelled')->count();

            $transactionCompletedAmount = Transaction::where('status', 'completed')->sum('amount');
            $eventPaymentCompletedAmount = EventPayment::where('status', 'completed')->sum('amount');

            $transactionTodayAmount = Transaction::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount');
            $eventPaymentTodayAmount = EventPayment::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount');

            $transactionMonthlyAmount = Transaction::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount');
            $eventPaymentMonthlyAmount = EventPayment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount');

            $transactionYearlyAmount = Transaction::where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->sum('amount');
            $eventPaymentYearlyAmount = EventPayment::where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->sum('amount');

            $stats = [
                'total' => $transactionTotal + $eventPaymentTotal,
                'completed' => $transactionCompleted + $eventPaymentCompleted,
                'pending' => $transactionPending + $eventPaymentPending,
                // Keep failed as a summary bucket that includes explicitly rejected event payments.
                'failed' => $transactionFailed + $eventPaymentRejected,
                'rejected' => $eventPaymentRejected,
                'cancelled' => $eventPaymentCancelled,
                'refunded' => $transactionRefunded,
                'total_revenue' => $transactionCompletedAmount + $eventPaymentCompletedAmount,
                'today_revenue' => $transactionTodayAmount + $eventPaymentTodayAmount,
                'monthly_revenue' => $transactionMonthlyAmount + $eventPaymentMonthlyAmount,
                'yearly_revenue' => $transactionYearlyAmount + $eventPaymentYearlyAmount,
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
            // Accept merged-list IDs such as booking_123 / event_456, plus legacy numeric IDs.
            if (str_starts_with((string) $id, 'event_')) {
                $eventPaymentId = (int) str_replace('event_', '', (string) $id);
                $eventPayment = EventPayment::with(['user', 'event'])->findOrFail($eventPaymentId);

                return $this->success((object) [
                    'id' => 'event_' . $eventPayment->id,
                    'type' => 'event_tickets',
                    'transaction_id' => $eventPayment->transaction_id,
                    'user_id' => $eventPayment->user_id,
                    'user' => $eventPayment->user,
                    'event_id' => $eventPayment->event_id,
                    'event' => $eventPayment->event,
                    'amount' => $eventPayment->amount,
                    'status' => $eventPayment->status,
                    'method' => $eventPayment->method,
                    'sender_number' => $eventPayment->sender_number,
                    'trx_id' => $eventPayment->trx_id,
                    'screenshot_path' => $eventPayment->screenshot_path,
                    'created_at' => $eventPayment->created_at,
                    'verified_at' => $eventPayment->verified_at,
                    'verified_by_user_id' => $eventPayment->verified_by_user_id,
                ], 'Transaction retrieved successfully');
            }

            $transactionId = str_starts_with((string) $id, 'booking_')
                ? (int) str_replace('booking_', '', (string) $id)
                : (int) $id;

            $transaction = Transaction::with(['user'])->find($transactionId);

            if ($transaction) {
                return $this->success($transaction, 'Transaction retrieved successfully');
            }

            // Backward-compatible fallback: if a numeric/legacy ID maps to an event payment, return it.
            $eventPayment = EventPayment::with(['user', 'event'])->find($transactionId);
            if ($eventPayment) {
                return $this->success((object) [
                    'id' => 'event_' . $eventPayment->id,
                    'type' => 'event_tickets',
                    'transaction_id' => $eventPayment->transaction_id,
                    'user_id' => $eventPayment->user_id,
                    'user' => $eventPayment->user,
                    'event_id' => $eventPayment->event_id,
                    'event' => $eventPayment->event,
                    'amount' => $eventPayment->amount,
                    'status' => $eventPayment->status,
                    'method' => $eventPayment->method,
                    'sender_number' => $eventPayment->sender_number,
                    'trx_id' => $eventPayment->trx_id,
                    'screenshot_path' => $eventPayment->screenshot_path,
                    'created_at' => $eventPayment->created_at,
                    'verified_at' => $eventPayment->verified_at,
                    'verified_by_user_id' => $eventPayment->verified_by_user_id,
                ], 'Transaction retrieved successfully');
            }

            return $this->notFound('Transaction not found');
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

            Log::info("Transaction #{$id} status updated to {$validated['status']} by admin " . Auth::id());

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

            Log::info("Transaction #{$id} refunded by admin " . Auth::id());

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
            $type = $request->input('type'); // null/all, 'booking', 'event_tickets'
            $status = $request->input('status');
            $search = $request->input('search');
            $gateway = $request->input('gateway');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            $bookingQuery = Transaction::with(['user']);
            $eventQuery = EventPayment::with(['user', 'event']);

            if ($type && $type !== 'all') {
                if ($type === 'booking') {
                    $eventQuery = null;
                } elseif ($type === 'event_tickets') {
                    $bookingQuery = null;
                }
            }

            if ($bookingQuery) {
                if ($status) {
                    $bookingQuery->where('status', $status);
                }
                if ($search) {
                    $bookingQuery->where(function ($q) use ($search) {
                        $q->where('transaction_id', 'LIKE', "%{$search}%")
                            ->orWhere('gateway_transaction_id', 'LIKE', "%{$search}%")
                            ->orWhereHas('user', function ($userQ) use ($search) {
                                $userQ->where('name', 'LIKE', "%{$search}%")
                                    ->orWhere('email', 'LIKE', "%{$search}%");
                            });
                    });
                }
                if ($gateway) {
                    $bookingQuery->where('payment_gateway', $gateway);
                }
                if ($dateFrom) {
                    $bookingQuery->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $bookingQuery->where('created_at', '<=', $dateTo);
                }
            }

            if ($eventQuery) {
                if ($status) {
                    if ($status === 'failed') {
                        $eventQuery->whereIn('status', ['failed', 'rejected']);
                    } else {
                        $eventQuery->where('status', $status);
                    }
                }
                if ($search) {
                    $eventQuery->where(function ($q) use ($search) {
                        $q->where('transaction_id', 'LIKE', "%{$search}%")
                            ->orWhere('trx_id', 'LIKE', "%{$search}%")
                            ->orWhereHas('user', function ($userQ) use ($search) {
                                $userQ->where('name', 'LIKE', "%{$search}%")
                                    ->orWhere('email', 'LIKE', "%{$search}%");
                            });
                    });
                }
                if ($gateway) {
                    $eventQuery->where('method', $gateway);
                }
                if ($dateFrom) {
                    $eventQuery->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $eventQuery->where('created_at', '<=', $dateTo);
                }
            }

            $transactions = [];

            if ($bookingQuery) {
                $bookings = $bookingQuery->get();
                foreach ($bookings as $booking) {
                    $transactions[] = (object) [
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
                    $transactions[] = (object) [
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

            usort($transactions, function ($a, $b) {
                return $b->created_at <=> $a->created_at;
            });

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
                'verified_by_user_id' => Auth::id(),
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

            Log::info("Event payment #{$paymentId} approved by admin " . Auth::id());

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
                'verified_by_user_id' => Auth::id(),
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

            Log::info("Event payment #{$paymentId} rejected by admin " . Auth::id());

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

            Log::info("Event payment #{$paymentId} cancelled by admin " . Auth::id());

            return $this->success($payment->load(['user', 'event', 'registration']), 'Event payment cancelled and ticket availability restored');
        } catch (\Exception $e) {
            Log::error('Failed to cancel event payment: ' . $e->getMessage());
            return $this->error('Failed to cancel payment', 500);
        }
    }
}
