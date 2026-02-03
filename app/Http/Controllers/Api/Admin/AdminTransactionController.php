<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminTransactionController extends Controller
{
    use ApiResponse;
    /**
     * Get all transactions with filters and pagination
     */
    public function index(Request $request)
    {
        try {
            // Build base query for stats calculation (before pagination)
            $statsQuery = Transaction::query();

            // Apply same filters to stats query
            if ($request->has('status')) {
                $statsQuery->where('status', $request->status);
            }

            if ($request->has('type')) {
                $statsQuery->where('transaction_type', $request->type);
            }

            if ($request->has('gateway')) {
                $statsQuery->where('payment_gateway', $request->gateway);
            }

            if ($request->has('search')) {
                $statsQuery->where(function($q) use ($request) {
                    $q->where('transaction_id', 'LIKE', "%{$request->search}%")
                      ->orWhere('gateway_transaction_id', 'LIKE', "%{$request->search}%")
                      ->orWhereHas('user', function($userQ) use ($request) {
                          $userQ->where('name', 'LIKE', "%{$request->search}%")
                                ->orWhere('email', 'LIKE', "%{$request->search}%");
                      });
                });
            }

            if ($request->has('date_from')) {
                $statsQuery->where('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $statsQuery->where('created_at', '<=', $request->date_to);
            }

            // Calculate stats from all filtered records (before pagination)
            $stats = [
                'total' => $statsQuery->count(),
                'completed' => (clone $statsQuery)->where('status', 'completed')->count(),
                'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
                'failed' => (clone $statsQuery)->where('status', 'failed')->count(),
                'refunded' => (clone $statsQuery)->where('status', 'refunded')->count(),
                'total_revenue' => (clone $statsQuery)->where('status', 'completed')->sum('amount') ?? 0,
                'today_revenue' => (clone $statsQuery)->where('status', 'completed')->whereDate('created_at', today())->sum('amount') ?? 0,
                'monthly_revenue' => (clone $statsQuery)->where('status', 'completed')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount') ?? 0,
                'yearly_revenue' => (clone $statsQuery)->where('status', 'completed')->whereYear('created_at', now()->year)->sum('amount') ?? 0,
            ];

            // Build query for paginated data (with eager loading)
            $query = Transaction::with(['user']);

            // Apply same filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('type')) {
                $query->where('transaction_type', $request->type);
            }

            if ($request->has('gateway')) {
                $query->where('payment_gateway', $request->gateway);
            }

            if ($request->has('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('transaction_id', 'LIKE', "%{$request->search}%")
                      ->orWhere('gateway_transaction_id', 'LIKE', "%{$request->search}%")
                      ->orWhereHas('user', function($userQ) use ($request) {
                          $userQ->where('name', 'LIKE', "%{$request->search}%")
                                ->orWhere('email', 'LIKE', "%{$request->search}%");
                      });
                });
            }

            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            // Sort by newest first
            $query->orderBy('created_at', 'desc');

            $transactions = $query->paginate($request->per_page ?? 15);

            return $this->success($transactions->items(), 'Transactions retrieved successfully', 200, [
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
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
}
