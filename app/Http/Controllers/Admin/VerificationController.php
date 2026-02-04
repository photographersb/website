<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveVerificationRequest;
use App\Http\Requests\RejectVerificationRequest;
use App\Models\VerificationRequest;
use App\Notifications\VerificationApproved;
use App\Notifications\VerificationRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    /**
     * List all verification requests with filters
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', VerificationRequest::class);

        $query = VerificationRequest::with('user', 'reviewedBy')
            ->orderByDesc('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by user name, email, or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $verifications = $query->paginate(15);

        return inertia('Admin/Verifications/Index', [
            'verifications' => $verifications,
            'filters' => $request->only('status', 'type', 'search', 'date_from', 'date_to'),
            'statuses' => ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'],
            'types' => ['phone' => 'Phone', 'nid' => 'National ID', 'business' => 'Business']
        ]);
    }

    /**
     * Show verification request details for review
     */
    public function show(VerificationRequest $verificationRequest)
    {
        $this->authorize('view', $verificationRequest);

        $verificationRequest->load('user', 'reviewedBy');

        return inertia('Admin/Verifications/Show', [
            'verificationRequest' => $verificationRequest,
            'can' => [
                'approve' => auth()->user()->can('approve', $verificationRequest),
                'reject' => auth()->user()->can('reject', $verificationRequest)
            ]
        ]);
    }

    /**
     * Approve a verification request
     */
    public function approve(VerificationRequest $verificationRequest, ApproveVerificationRequest $request)
    {
        $this->authorize('approve', $verificationRequest);

        $admin = auth()->user();
        $adminNote = $request->validated()['admin_note'];

        // Approve the request
        $verificationRequest->approve($admin, $adminNote);

        // Send approval notification
        $verificationRequest->user->notify(new VerificationApproved($verificationRequest));

        return redirect()->route('admin.verifications.index')
            ->with('success', 'Verification request approved! Photographer has been notified.');
    }

    /**
     * Reject a verification request
     */
    public function reject(VerificationRequest $verificationRequest, RejectVerificationRequest $request)
    {
        $this->authorize('reject', $verificationRequest);

        $admin = auth()->user();
        $reason = $request->validated()['admin_note'];

        // Reject the request
        $verificationRequest->reject($admin, $reason);

        // Send rejection notification
        $verificationRequest->user->notify(new VerificationRejected($verificationRequest));

        return redirect()->route('admin.verifications.index')
            ->with('success', 'Verification request rejected. Photographer has been notified.');
    }

    /**
     * Download verification document (admin only)
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

    /**
     * Get verification statistics
     */
    public function statistics()
    {
        $this->authorize('viewAny', VerificationRequest::class);

        return response()->json([
            'pending' => VerificationRequest::pending()->count(),
            'approved' => VerificationRequest::approved()->count(),
            'rejected' => VerificationRequest::rejected()->count(),
            'total' => VerificationRequest::count(),
            'this_week' => VerificationRequest::where('created_at', '>=', now()->subWeek())->count(),
            'avg_review_time' => VerificationRequest::approved()
                ->selectRaw('AVG(HOUR(TIMEDIFF(reviewed_at, created_at))) as hours')
                ->value('hours') ?? 0
        ]);
    }
}
