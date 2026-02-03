<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photographer;
use App\Models\UserVerification;
use App\Models\VerificationRequest;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use ApiResponse;

    /**
     * Get photographer verification status
     */
    public function getStatus(Photographer $photographer)
    {
        $user = $photographer->user;

        if (!$user) {
            return $this->error('Photographer user not found', 404);
        }

        $verifications = $user->verifications()
            ->select('id', 'verification_type', 'verification_status', 'verified_at', 'expires_at')
            ->get()
            ->map(function ($v) {
                return [
                    'type' => $v->verification_type,
                    'status' => $v->verification_status,
                    'verified_at' => $v->verified_at,
                    'expires_at' => $v->expires_at,
                    'is_expired' => $v->isExpired()
                ];
            });

        $pendingRequests = $user->verificationRequests()
            ->where('status', 'pending')
            ->count();

        return $this->success([
            'verifications' => $verifications,
            'pending_requests' => $pendingRequests
        ], 'Verification status retrieved');
    }

    /**
     * Submit verification request
     */
    public function submitRequest(Request $request)
    {
        $user = auth()->user();

        if (!$user->isPhotographer()) {
            return $this->error('Only photographers can submit verification requests', 403);
        }

        $validated = $request->validate([
            'request_type' => 'required|in:nid,business_license,tax_certificate,studio_address',
            'submitted_documents' => 'nullable|array',
            'submitted_documents.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png'
        ]);

        try {
            $documents = [];
            if ($request->hasFile('submitted_documents')) {
                foreach ($request->file('submitted_documents') as $file) {
                    $path = $file->store('verifications', 'public');
                    $documents[] = [
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'upload_date' => now()
                    ];
                }
            }

            $verificationRequest = $user->verificationRequests()->create([
                'request_type' => $validated['request_type'],
                'submitted_documents' => $documents ?: null,
                'status' => 'pending'
            ]);

            return $this->success($verificationRequest, 'Verification request submitted successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to submit verification request: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get pending verification requests (admin only)
     */
    public function getPendingRequests(Request $request)
    {
        // Check if user is authenticated and is admin
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $perPage = $request->get('per_page', 15);

        $requests = VerificationRequest::where('status', 'pending')
            ->with(['user' => function ($query) {
                $query->select('id', 'name', 'email', 'phone');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->success($requests, 'Pending verification requests retrieved');
    }

    /**
     * Approve verification request
     */
    public function approveRequest(Request $request, VerificationRequest $verificationRequest)
    {
        $this->authorize('isAdmin', auth()->user());

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $verificationRequest->approve(auth()->user(), $validated['notes'] ?? null);

            // Create or update user verification record
            UserVerification::updateOrCreate(
                [
                    'user_id' => $verificationRequest->user_id,
                    'verification_type' => $verificationRequest->request_type
                ],
                [
                    'verification_status' => 'approved',
                    'verified_by_admin_id' => auth()->id(),
                    'verified_at' => now(),
                    'expires_at' => now()->addMonths(12)
                ]
            );

            return $this->success($verificationRequest, 'Verification request approved');
        } catch (\Exception $e) {
            return $this->error('Failed to approve verification request: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Reject verification request
     */
    public function rejectRequest(Request $request, VerificationRequest $verificationRequest)
    {
        $this->authorize('isAdmin', auth()->user());

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $verificationRequest->reject(
                auth()->user(),
                $validated['reason'],
                $validated['notes'] ?? null
            );

            return $this->success($verificationRequest, 'Verification request rejected');
        } catch (\Exception $e) {
            return $this->error('Failed to reject verification request: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Revoke a verification
     */
    public function revokeVerification(Request $request, Photographer $photographer, UserVerification $verification)
    {
        $this->authorize('isAdmin', auth()->user());

        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $user = $photographer->user;

            if (!$user || $verification->user_id !== $user->id) {
                return $this->error('Verification not found', 404);
            }

            $verification->update([
                'verification_status' => 'revoked',
                'notes' => 'Revoked: ' . $validated['reason']
            ]);

            return $this->success($verification, 'Verification revoked successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to revoke verification: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get verification history for a photographer (admin only)
     */
    public function getPhotographerHistory(Photographer $photographer)
    {
        $this->authorize('isAdmin', auth()->user());

        $user = $photographer->user;

        if (!$user) {
            return $this->error('Photographer user not found', 404);
        }

        $verifications = $user->verifications()
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($verifications, 'Photographer verification history retrieved');
    }

    /**
     * Renew an expired verification
     */
    public function renewVerification(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error('Unauthorized', 401);
        }

        $validated = $request->validate([
            'verification_type' => 'required|string|in:nid,business_license,tax_certificate,studio_address'
        ]);

        try {
            $verification = $user->verifications()
                ->where('verification_type', $validated['verification_type'])
                ->first();

            if (!$verification) {
                return $this->error('Verification not found', 404);
            }

            if ($verification->verification_status !== 'approved') {
                return $this->error('Only approved verifications can be renewed', 422);
            }

            // Create a new verification request for renewal
            $renewalRequest = VerificationRequest::create([
                'user_id' => $user->id,
                'request_type' => $validated['verification_type'],
                'status' => 'pending',
                'reason' => 'Renewal of expired verification'
            ]);

            return $this->success($renewalRequest, 'Renewal request submitted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to submit renewal request: ' . $e->getMessage(), 500);
        }
    }
}
