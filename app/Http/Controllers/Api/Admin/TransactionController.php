<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Get all transactions with pagination and filters
     */
    public function index(Request $request)
    {
        $query = Transaction::with('user');
        
        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 10));
        
        return response()->json([
            'data' => $transactions->items(),
            'pagination' => [
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
            ]
        ]);
    }
    
    /**
     * Get a single transaction
     */
    public function show(Transaction $transaction)
    {
        return response()->json(['data' => $transaction->load('user')]);
    }
    
    /**
     * Export transactions to CSV
     */
    public function export(Request $request)
    {
        $query = Transaction::with('user');
        
        // Apply same filters as index
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        }
        
        $transactions = $query->get();
        
        $filename = 'transactions_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $path = storage_path('app/exports/' . $filename);
        
        $file = fopen($path, 'w');
        fputcsv($file, ['Transaction ID', 'User', 'Type', 'Amount', 'Status', 'Date']);
        
        foreach ($transactions as $txn) {
            fputcsv($file, [
                $txn->transaction_id,
                $txn->user->name,
                $txn->type,
                $txn->amount,
                $txn->status,
                $txn->created_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        fclose($file);
        
        return response()->download($path, $filename, ['Content-Type' => 'text/csv']);
    }
}
