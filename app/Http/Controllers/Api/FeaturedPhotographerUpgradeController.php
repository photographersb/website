<?php

namespace App\Http\Controllers\Api;

use App\Models\FeaturedPhotographer;
use App\Models\FeaturedPhotographerUpgrade;
use App\Models\FeaturedPhotographerPayment;
use App\Notifications\PackageUpgradedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeaturedPhotographerUpgradeController extends Controller
{
    /**
     * Get upgrade options for a featured photographer
     */
    public function getUpgradeOptions($id)
    {
        $featured = FeaturedPhotographer::with('photographer')
            ->where('id', $id)
            ->firstOrFail();

        if (!auth()->check() || auth()->id() !== $featured->photographer_id) {
            return $this->forbidden('Unauthorized to view upgrade options');
        }

        $currentPackage = $featured->package_tier ?? 'Starter';
        $packages = [
            'Starter' => ['price' => 999, 'features' => 5],
            'Professional' => ['price' => 2499, 'features' => 15],
            'Enterprise' => ['price' => 5999, 'features' => 'Unlimited'],
        ];

        $upgradeOptions = [];
        foreach ($packages as $packageName => $details) {
            if ($packageName !== $currentPackage) {
                $priceData = FeaturedPhotographerUpgrade::calculateUpgradePrice(
                    $currentPackage,
                    $packageName,
                    $featured->end_date
                );

                $upgradeOptions[] = [
                    'package' => $packageName,
                    'current_package' => $currentPackage,
                    'price_details' => $priceData,
                    'features' => $details['features'],
                    'is_upgrade' => $priceData['new_price'] > $priceData['current_price'],
                ];
            }
        }

        return $this->success([
            'featured_photographer' => $featured,
            'upgrade_options' => $upgradeOptions,
            'days_remaining' => now()->diffInDays($featured->end_date),
        ]);
    }

    /**
     * Initiate upgrade payment
     */
    public function initiateUpgrade(Request $request, $id)
    {
        $request->validate([
            'to_package' => 'required|in:Starter,Professional,Enterprise',
            'payment_method' => 'required|in:bkash,nagad,upay,sslcommerz,cash',
        ]);

        $featured = FeaturedPhotographer::with('photographer')
            ->where('id', $id)
            ->firstOrFail();

        if (!auth()->check() || auth()->id() !== $featured->photographer_id) {
            return $this->forbidden('Unauthorized to upgrade');
        }

        $currentPackage = $featured->package_tier ?? 'Starter';

        if ($currentPackage === $request->to_package) {
            return $this->error('Same package selected', 400);
        }

        $priceData = FeaturedPhotographerUpgrade::calculateUpgradePrice(
            $currentPackage,
            $request->to_package,
            $featured->end_date
        );

        if ($priceData['total_amount'] == 0) {
            // Free upgrade (downgrade)
            return $this->processUpgradeWithoutPayment($featured, $request->to_package);
        }

        // Create upgrade record
        $upgrade = FeaturedPhotographerUpgrade::create([
            'featured_photographer_id' => $featured->id,
            'from_package' => $currentPackage,
            'to_package' => $request->to_package,
            'prorated_amount' => $priceData['prorated_amount'],
            'discount_amount' => $priceData['discount_amount'],
            'total_amount' => $priceData['total_amount'],
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
        ]);

        // Route to appropriate payment method
        return match ($request->payment_method) {
            'bkash' => $this->initiateBkashUpgrade($upgrade),
            'nagad' => $this->initiateNagadUpgrade($upgrade),
            'upay' => $this->initiateUpayUpgrade($upgrade),
            'sslcommerz' => $this->initiateSslcommerzUpgrade($upgrade),
            'cash' => $this->initiateCashUpgrade($upgrade),
            default => $this->error('Invalid payment method', 400),
        };
    }

    /**
     * Process bKash payment for upgrade
     */
    private function initiateBkashUpgrade(FeaturedPhotographerUpgrade $upgrade)
    {
        try {
            // bKash API implementation
            // Reference: https://github.com/mahedihsl/bkash

            $bkashConfig = [
                'app_key' => config('payment.bkash_app_key'),
                'app_secret' => config('payment.bkash_app_secret'),
                'username' => config('payment.bkash_username'),
                'password' => config('payment.bkash_password'),
                'base_url' => config('payment.bkash_base_url'),
            ];

            $referenceNumber = 'UPG-' . $upgrade->id . '-' . time();

            // Initialize payment (placeholder)
            $response = [
                'bkashURL' => "https://checkout.bkash.com/payment/" . $referenceNumber,
                'paymentID' => $referenceNumber,
            ];

            $upgrade->update(['reference_number' => $referenceNumber]);

            return $this->success([
                'upgrade_id' => $upgrade->id,
                'payment_url' => $response['bkashURL'] ?? null,
                'redirect_url' => route('api.featured.upgrade.bkash-callback'),
                'amount' => $upgrade->total_amount,
            ]);
        } catch (\Exception $e) {
            $upgrade->markAsFailed($e->getMessage());
            return $this->error('Failed to initiate bKash payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process Nagad payment for upgrade
     */
    private function initiateNagadUpgrade(FeaturedPhotographerUpgrade $upgrade)
    {
        try {
            // Nagad API implementation
            // Reference: https://github.com/codeboxrcodehub/nagad

            $nagadConfig = [
                'merchant_id' => config('payment.nagad_merchant_id'),
                'merchant_key' => config('payment.nagad_merchant_key'),
                'base_url' => config('payment.nagad_base_url'),
            ];

            $referenceNumber = 'UPG-NAG-' . $upgrade->id . '-' . time();

            // Initialize payment (placeholder)
            $response = [
                'paymentURL' => "https://api.nagad.com.bd/payment/" . $referenceNumber,
                'paymentID' => $referenceNumber,
            ];

            $upgrade->update(['reference_number' => $referenceNumber]);

            return $this->success([
                'upgrade_id' => $upgrade->id,
                'payment_url' => $response['paymentURL'] ?? null,
                'redirect_url' => route('api.featured.upgrade.nagad-callback'),
                'amount' => $upgrade->total_amount,
            ]);
        } catch (\Exception $e) {
            $upgrade->markAsFailed($e->getMessage());
            return $this->error('Failed to initiate Nagad payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process uPay payment for upgrade
     */
    private function initiateUpayUpgrade(FeaturedPhotographerUpgrade $upgrade)
    {
        try {
            // uPay API implementation
            // Reference: https://github.com/codeboxrcodehub/upay

            $upayConfig = [
                'merchant_id' => config('payment.upay_merchant_id'),
                'api_key' => config('payment.upay_api_key'),
                'base_url' => config('payment.upay_base_url'),
            ];

            $referenceNumber = 'UPG-UPAY-' . $upgrade->id . '-' . time();

            // Initialize payment (placeholder)
            $response = [
                'paymentURL' => "https://api.upay.com.bd/payment/" . $referenceNumber,
                'paymentID' => $referenceNumber,
            ];

            $upgrade->update(['reference_number' => $referenceNumber]);

            return $this->success([
                'upgrade_id' => $upgrade->id,
                'payment_url' => $response['paymentURL'] ?? null,
                'redirect_url' => route('api.featured.upgrade.upay-callback'),
                'amount' => $upgrade->total_amount,
            ]);
        } catch (\Exception $e) {
            $upgrade->markAsFailed($e->getMessage());
            return $this->error('Failed to initiate uPay payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process SSLCommerz payment for upgrade
     */
    private function initiateSslcommerzUpgrade(FeaturedPhotographerUpgrade $upgrade)
    {
        try {
            // SSLCommerz implementation
            // Reference: https://github.com/sslcommerz/SSLCommerz-Laravel

            $sslConfig = [
                'store_id' => config('payment.sslcommerz_store_id'),
                'store_password' => config('payment.sslcommerz_store_password'),
                'base_url' => config('payment.sslcommerz_base_url'),
            ];

            $referenceNumber = 'UPG-SSL-' . $upgrade->id . '-' . time();

            // Initialize payment (placeholder)
            $response = [
                'GatewayPageURL' => "https://sandbox.sslcommerz.com/gwprocess/v4/gw.php?sessionkey=" . $referenceNumber,
                'sessionKey' => $referenceNumber,
            ];

            $upgrade->update(['reference_number' => $referenceNumber]);

            return $this->success([
                'upgrade_id' => $upgrade->id,
                'payment_url' => $response['GatewayPageURL'] ?? null,
                'redirect_url' => route('api.featured.upgrade.sslcommerz-callback'),
                'amount' => $upgrade->total_amount,
            ]);
        } catch (\Exception $e) {
            $upgrade->markAsFailed($e->getMessage());
            return $this->error('Failed to initiate SSLCommerz payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process Cash/Manual payment for upgrade
     */
    private function initiateCashUpgrade(FeaturedPhotographerUpgrade $upgrade)
    {
        try {
            $referenceNumber = 'UPG-CASH-' . $upgrade->id . '-' . time();

            $upgrade->update([
                'payment_status' => 'pending',
                'reference_number' => $referenceNumber,
                'notes' => 'Waiting for manual payment verification',
            ]);

            return $this->success([
                'upgrade_id' => $upgrade->id,
                'amount' => $upgrade->total_amount,
                'reference_number' => $referenceNumber,
                'status' => 'pending_verification',
                'message' => 'Please provide payment proof. Admin will verify and complete the upgrade.',
                'instructions' => 'Send payment to the account number shown in your portal and notify admin.',
            ]);
        } catch (\Exception $e) {
            $upgrade->markAsFailed($e->getMessage());
            return $this->error('Failed to initiate cash payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process free upgrade without payment
     */
    private function processUpgradeWithoutPayment(FeaturedPhotographer $featured, $toPackage)
    {
        $upgrade = FeaturedPhotographerUpgrade::create([
            'featured_photographer_id' => $featured->id,
            'from_package' => $featured->package_tier ?? 'Starter',
            'to_package' => $toPackage,
            'prorated_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 0,
            'payment_method' => 'free',
            'payment_status' => 'completed',
            'upgraded_at' => now(),
        ]);

        $featured->update(['package_tier' => $toPackage]);

        $featured->photographer->notify(new PackageUpgradedNotification($featured, $upgrade));

        return $this->success([
            'upgrade_id' => $upgrade->id,
            'status' => 'completed',
            'message' => 'Package downgraded successfully',
            'new_package' => $toPackage,
        ]);
    }

    /**
     * Handle payment success - update upgrade status
     */
    public function confirmUpgrade(Request $request, $id)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'reference_number' => 'nullable|string',
        ]);

        $upgrade = FeaturedPhotographerUpgrade::where('id', $id)
            ->where('featured_photographer_id', auth()->user()->featuredPhotographers->pluck('id')->toArray())
            ->firstOrFail();

        DB::transaction(function () use ($upgrade, $request) {
            $upgrade->markAsCompleted($request->transaction_id);

            // Log payment
            FeaturedPhotographerPayment::create([
                'featured_photographer_id' => $upgrade->featured_photographer_id,
                'amount' => $upgrade->total_amount,
                'payment_method' => $upgrade->payment_method,
                'status' => 'completed',
                'transaction_id' => $request->transaction_id,
                'reference_number' => $request->reference_number,
                'payment_details' => json_encode([
                    'type' => 'upgrade',
                    'upgrade_id' => $upgrade->id,
                    'from_package' => $upgrade->from_package,
                    'to_package' => $upgrade->to_package,
                ]),
                'paid_at' => now(),
            ]);
        });

        // Notify photographer
        $upgrade->featuredPhotographer->photographer->notify(new PackageUpgradedNotification(
            $upgrade->featuredPhotographer,
            $upgrade
        ));

        return $this->success([
            'upgrade_id' => $upgrade->id,
            'status' => 'completed',
            'message' => 'Package upgraded successfully',
            'new_package' => $upgrade->to_package,
        ]);
    }

    /**
     * Get upgrade history
     */
    public function getHistory($id)
    {
        $featured = FeaturedPhotographer::where('id', $id)->firstOrFail();

        if (!auth()->check() || auth()->id() !== $featured->photographer_id) {
            return $this->forbidden('Unauthorized');
        }

        $upgrades = $featured->upgrades()
            ->orderByDesc('created_at')
            ->paginate(15);

        return $this->success([
            'upgrades' => $upgrades,
        ]);
    }

    /**
     * Admin: Get all upgrades
     */
    public function adminIndex()
    {
        if (!auth()->user()->hasRole('admin')) {
            return $this->forbidden('Admin access required');
        }

        $upgrades = FeaturedPhotographerUpgrade::with(['featuredPhotographer.photographer'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->success([
            'upgrades' => $upgrades,
        ]);
    }

    /**
     * Admin: Verify cash payment
     */
    public function verifyCashPayment(Request $request, $id)
    {
        if (!auth()->user()->hasRole('admin')) {
            return $this->forbidden('Admin access required');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $upgrade = FeaturedPhotographerUpgrade::findOrFail($id);

        if ($request->status === 'approved') {
            $upgrade->markAsCompleted('CASH-' . time());
            $upgrade->featured_photographer_id->update(['package_tier' => $upgrade->to_package]);
            $upgrade->featuredPhotographer->photographer->notify(new PackageUpgradedNotification(
                $upgrade->featuredPhotographer,
                $upgrade
            ));
        } else {
            $upgrade->markAsFailed($request->notes ?? 'Rejected by admin');
        }

        return $this->success([
            'upgrade_id' => $upgrade->id,
            'status' => $upgrade->payment_status,
        ]);
    }
}
