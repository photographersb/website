<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuoteController extends Controller
{
    public function send(Request $request, $inquiryId)
    {
        $inquiry = Inquiry::with('user')->findOrFail($inquiryId);

        // Check if authenticated user is the photographer for this inquiry
        if ($request->user()->photographer->id !== $inquiry->photographer_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to send quote for this inquiry'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'package_id' => 'nullable|exists:packages,id',
            'amount' => 'required|numeric|min:100|max:1000000',
            'description' => 'required|string|min:20|max:2000',
            'validity_days' => 'required|integer|min:1|max:30',
            'terms_conditions' => 'nullable|string|max:5000',
            'deliverables' => 'nullable|array',
            'deliverables.*.item' => 'required|string|max:200',
            'deliverables.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create quote
        $quote = Quote::create([
            'inquiry_id' => $inquiry->id,
            'photographer_id' => $inquiry->photographer_id,
            'package_id' => $request->package_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'validity_days' => $request->validity_days,
            'terms_conditions' => $request->terms_conditions,
            'deliverables' => $request->deliverables ? json_encode($request->deliverables) : null,
            'status' => 'sent',
            'expires_at' => now()->addDays($request->validity_days),
        ]);

        // Update inquiry status
        $inquiry->update(['status' => 'quoted']);

        // Send notification to client
        $inquiry->user->notify(new \App\Notifications\QuoteReceived($quote));

        return response()->json([
            'status' => 'success',
            'message' => 'Quote sent successfully',
            'data' => $quote->load('package')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $quote = Quote::findOrFail($id);

        // Check authorization
        if ($request->user()->photographer->id !== $quote->photographer_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        // Can only update if status is 'sent' or 'countered'
        if (!in_array($quote->status, ['sent', 'countered'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot update quote with status: ' . $quote->status
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'sometimes|numeric|min:100|max:1000000',
            'description' => 'sometimes|string|min:20|max:2000',
            'validity_days' => 'sometimes|integer|min:1|max:30',
            'terms_conditions' => 'nullable|string|max:5000',
            'deliverables' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only(['amount', 'description', 'terms_conditions', 'deliverables']);
        
        if ($request->has('validity_days')) {
            $updateData['validity_days'] = $request->validity_days;
            $updateData['expires_at'] = now()->addDays($request->validity_days);
        }

        if ($request->has('deliverables')) {
            $updateData['deliverables'] = json_encode($request->deliverables);
        }

        $quote->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Quote updated successfully',
            'data' => $quote->fresh()
        ]);
    }

    public function list(Request $request)
    {
        $photographer = $request->user()->photographer;

        $quotes = Quote::with(['inquiry.user', 'package'])
            ->where('photographer_id', $photographer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $quotes
        ]);
    }

    public function show(Request $request, $id)
    {
        $quote = Quote::with(['inquiry.user', 'package', 'photographer.user'])
            ->findOrFail($id);

        // Check authorization (photographer or client)
        $user = $request->user();
        $isPhotographer = $user->photographer && $user->photographer->id === $quote->photographer_id;
        $isClient = $user->id === $quote->inquiry->user_id;

        if (!$isPhotographer && !$isClient) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $quote
        ]);
    }
}
