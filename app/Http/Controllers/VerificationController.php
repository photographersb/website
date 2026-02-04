<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVerificationRequest;
use App\Models\VerificationRequest;
use App\Notifications\VerificationRequestSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    /**
     * Show verification dashboard/status page
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get user's verification status
        $verification = $user->verification();
        $latestRequest = $user->verificationRequests()->latest()->first();

        return inertia('Verification/Index', [
            'verification' => $verification,
            'latestRequest' => $latestRequest,
            'verificationTypes' => [
                'phone' => 'Phone Verification',
                'nid' => 'National ID',
                'business' => 'Business Verification'
            ]
        ]);
    }

    /**
     * Show create verification request form
     */
    public function create()
    {
        return inertia('Verification/Create', [
            'verificationTypes' => [
                ['value' => 'phone', 'label' => 'Phone Verification', 'description' => 'Quick phone verification'],
                ['value' => 'nid', 'label' => 'National ID', 'description' => 'Government ID verification'],
                ['value' => 'business', 'label' => 'Business Verification', 'description' => 'Full business verification']
            ]
        ]);
    }

    /**
     * Store a new verification request
     */
    public function store(StoreVerificationRequest $request)
    {
        $user = auth()->user();

        // Check if there's already a pending request
        if ($user->verificationRequests()->pending()->exists()) {
            return back()->with('error', 'You already have a pending verification request.');
        }

        $data = $request->validated();

        // Handle file uploads
        if ($request->hasFile('document_front_path')) {
            $data['document_front_path'] = $request->file('document_front_path')
                ->store('verifications/documents', 'private');
        }

        if ($request->hasFile('document_back_path')) {
            $data['document_back_path'] = $request->file('document_back_path')
                ->store('verifications/documents', 'private');
        }

        if ($request->hasFile('selfie_path')) {
            $data['selfie_path'] = $request->file('selfie_path')
                ->store('verifications/selfies', 'private');
        }

        // Create verification request
        $verificationRequest = $user->verificationRequests()->create($data);

        // Send notification
        $user->notify(new VerificationRequestSubmitted($verificationRequest));

        return redirect()->route('verification.index')
            ->with('success', 'Verification request submitted! We will review your documents within 2-3 business days.');
    }

    /**
     * Show verification request details (photographer viewing their own request)
     */
    public function show(VerificationRequest $verificationRequest)
    {
        $this->authorize('view', $verificationRequest);

        $verificationRequest->load('user', 'reviewedBy');

        return inertia('Verification/Show', [
            'verificationRequest' => $verificationRequest
        ]);
    }

    /**
     * Download a document (secure access)
     */
    public function downloadDocument(VerificationRequest $verificationRequest, string $type)
    {
        $this->authorize('view', $verificationRequest);

        $fieldMap = [
            'front' => 'document_front_path',
            'back' => 'document_back_path',
            'selfie' => 'selfie_path'
        ];

        if (!isset($fieldMap[$type]) || !$verificationRequest->{$fieldMap[$type]}) {
            abort(404);
        }

        return Storage::disk('private')->download($verificationRequest->{$fieldMap[$type]});
    }
}
