<?php

namespace App\Http\Controllers\Api;

use App\Models\Photographer;
use App\Models\PhotographerTip;
use App\Notifications\TipReceivedNotification;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class PhotographerTipController extends Controller
{
    use ApiResponse;

    /**
     * Get photographer tip info (including tip phone number if available)
     */
    public function getTipInfo($photographerId)
    {
        $photographer = Photographer::with('user')->findOrFail($photographerId);

        if (!$photographer->accept_tips) {
            return $this->success([
                'photographer_id' => $photographer->id,
                'photographer_name' => $photographer->user->name,
                'accept_tips' => false,
                'tip_phone_number' => null,
                'tip_message' => $photographer->tip_message ?? 'Your tip helps me keep creating, learning, and improving for you.',
                'total_tips' => 0,
                'tip_count' => 0,
                'recent_tips' => [],
            ]);
        }

        $totalTips = PhotographerTip::getTotalTips($photographerId);
        $recentTips = PhotographerTip::getRecentTips($photographerId, 5);

        return $this->success([
            'photographer_id' => $photographer->id,
            'photographer_name' => $photographer->user->name,
            'accept_tips' => true,
            'tip_phone_number' => $photographer->tip_phone_number,
            'tip_message' => $photographer->tip_message ?? 'Your tip helps me keep creating, learning, and improving for you.',
            'total_tips' => $totalTips,
            'tip_count' => PhotographerTip::where('photographer_id', $photographerId)->completed()->count(),
            'recent_tips' => $recentTips->map(function ($tip) {
                return [
                    'amount' => $tip->amount,
                    'message' => $tip->message,
                    'donor_name' => $tip->user ? $tip->user->name : 'Anonymous',
                    'paid_at' => $tip->paid_at->diffForHumans(),
                ];
            }),
        ]);
    }

    /**
     * Initiate tip payment
     */
    public function initiateTip(Request $request, $photographerId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50|max:100000',
            'message' => 'nullable|string|max:500',
            'payment_method' => 'required|in:bkash,nagad,rocket',
        ]);

        $photographer = Photographer::findOrFail($photographerId);

        if (!$photographer->accept_tips) {
            return $this->error('This photographer is not accepting tips', 400);
        }

        $recipientNumber = $this->resolveTipNumber($photographer, $request->payment_method);
        if (!$recipientNumber) {
            return $this->error('Selected payment number is not available', 422);
        }

        // Create tip record
        $tip = PhotographerTip::create([
            'photographer_id' => $photographer->id,
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'currency' => 'BDT',
            'payment_method' => $request->payment_method,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return $this->success([
            'tip_id' => $tip->id,
            'amount' => $tip->amount,
            'payment_method' => $tip->payment_method,
            'payment_method_label' => $this->paymentMethodLabel($tip->payment_method),
            'recipient_number' => $recipientNumber,
            'message' => 'Send ৳' . number_format($tip->amount, 0) . ' via ' . $this->paymentMethodLabel($tip->payment_method),
            'status' => 'pending',
        ]);
    }

    private function resolveTipNumber(Photographer $photographer, string $method): ?string
    {
        // Return the unified tip phone number regardless of payment method
        // The photographer has set one phone number that works with any mobile payment system
        return $photographer->tip_phone_number;
    }

    private function paymentMethodLabel(string $method): string
    {
        return match ($method) {
            'bkash' => 'bKash',
            'nagad' => 'Nagad',
            'rocket' => 'Rocket',
            default => 'Payment',
        };
    }

    /**
     * Confirm tip payment
     */
    public function confirmTip(Request $request, $tipId)
    {
        $request->validate([
            'transaction_id' => 'required|string',
        ]);

        $tip = PhotographerTip::findOrFail($tipId);

        $tip->markAsCompleted($request->transaction_id);

        // Notify photographer
        $tip->photographer->user->notify(new TipReceivedNotification($tip));

        return $this->success([
            'tip_id' => $tip->id,
            'status' => 'completed',
            'message' => 'Thank you for your generous tip!',
        ]);
    }

    /**
     * Get tips for photographer (admin/owner only)
     */
    public function getPhotographerTips($photographerId)
    {
        $photographer = Photographer::findOrFail($photographerId);

        // Check authorization
        if (auth()->id() !== $photographer->user_id && !auth()->user()->hasRole('admin')) {
            return $this->forbidden('Unauthorized');
        }

        $tips = PhotographerTip::where('photographer_id', $photographerId)
            ->completed()
            ->with('user')
            ->orderByDesc('paid_at')
            ->paginate(20);

        return $this->success([
            'tips' => $tips,
            'total_amount' => PhotographerTip::getTotalTips($photographerId),
            'tip_count' => $tips->total(),
        ]);
    }
}
