<?php

namespace App\Http\Controllers\Api;

use App\Models\Photographer;
use App\Models\PhotographerTip;
use App\Notifications\TipReceivedNotification;
use Illuminate\Http\Request;

class PhotographerTipController extends Controller
{
    /**
     * Get photographer tip info (including bKash number if available)
     */
    public function getTipInfo($photographerId)
    {
        $photographer = Photographer::with('user')->findOrFail($photographerId);

        if (!$photographer->accept_tips) {
            return $this->success([
                'photographer_id' => $photographer->id,
                'photographer_name' => $photographer->user->name,
                'accept_tips' => false,
                'bkash_number' => null,
                'nagad_number' => null,
                'rocket_number' => null,
                'tip_message' => $photographer->tip_message ?? 'Support your favorite photographer!',
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
            'bkash_number' => $photographer->bkash_number,
            'nagad_number' => $photographer->nagad_number,
            'rocket_number' => $photographer->rocket_number,
            'tip_message' => $photographer->tip_message ?? 'Support your favorite photographer!',
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
        return match ($method) {
            'bkash' => $photographer->bkash_number,
            'nagad' => $photographer->nagad_number,
            'rocket' => $photographer->rocket_number,
            default => null,
        };
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
