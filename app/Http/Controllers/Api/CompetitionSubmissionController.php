<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CompetitionSubmissionController extends Controller
{
    /**
     * Get all submissions for a competition (public gallery)
     */
    public function index(Request $request, $competitionId)
    {
        $competition = Competition::findOrFail($competitionId);
        
        $query = CompetitionSubmission::with(['photographer.photographer', 'competition'])
            ->forCompetition($competitionId)
            ->approved();
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'most_voted') {
            $query->orderBy('vote_count', 'desc');
        } elseif ($sortBy === 'trending') {
            $query->orderBy('view_count', 'desc');
        } elseif ($sortBy === 'random') {
            $query->inRandomOrder();
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $submissions = $query->paginate(20);
        
        return response()->json([
            'status' => 'success',
            'data' => $submissions
        ]);
    }

    /**
     * Get single submission details
     */
    public function show($competitionId, $submissionId)
    {
        $submission = CompetitionSubmission::with(['photographer.photographer', 'competition'])
            ->forCompetition($competitionId)
            ->findOrFail($submissionId);
        
        // Increment view count
        $submission->incrementViewCount();
        
        return response()->json([
            'status' => 'success',
            'data' => $submission
        ]);
    }

    /**
     * Submit a photo to competition (authenticated photographers)
     */
    public function store(Request $request, $competitionId)
    {
        $competition = Competition::findOrFail($competitionId);
        
        // Check if competition accepts submissions
        if ($competition->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Competition is not accepting submissions'
            ], 403);
        }
        
        // Check submission deadline
        if (now()->isAfter($competition->submission_deadline)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission deadline has passed'
            ], 403);
        }
        
        $user = $request->user();
        
        // Check max submissions
        $existingCount = CompetitionSubmission::forCompetition($competitionId)
            ->byPhotographer($user->id)
            ->count();
        
        if ($existingCount >= $competition->max_submissions_per_user) {
            return response()->json([
                'status' => 'error',
                'message' => "Maximum {$competition->max_submissions_per_user} submissions allowed"
            ], 403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:10240', // 10MB max
            'location' => 'nullable|string|max:255',
            'date_taken' => 'nullable|date',
            'camera_make' => 'nullable|string|max:255',
            'camera_model' => 'nullable|string|max:255',
            'camera_settings' => 'nullable|string|max:500',
            'hashtags' => 'nullable|string|max:255',
            'is_watermarked' => 'boolean'
        ]);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = 'competitions/' . $competitionId . '/submissions/';
            
            // Store original
            $imagePath = $image->storeAs($path, $filename, 'public');
            $imageUrl = Storage::url($imagePath);
            
            // Create thumbnail using Intervention Image v3
            $thumbnailFilename = 'thumb_' . $filename;
            $thumbnailPath = storage_path('app/public/' . $path . $thumbnailFilename);
            
            // Create thumbnail with Intervention Image v3 ImageManager
            $manager = new ImageManager(new Driver());
            $img = $manager->read($image->getRealPath());
            $img->cover(400, 400);
            $img->save($thumbnailPath);
            
            $thumbnailUrl = Storage::url($path . $thumbnailFilename);
            
            $validated['image_path'] = $imagePath;
            $validated['image_url'] = $imageUrl;
            $validated['thumbnail_url'] = $thumbnailUrl;
        }
        
        $submission = CompetitionSubmission::create([
            'competition_id' => $competitionId,
            'photographer_id' => $user->id,
            'image_path' => $validated['image_path'],
            'image_url' => $validated['image_url'],
            'thumbnail_url' => $validated['thumbnail_url'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'date_taken' => $validated['date_taken'] ?? null,
            'camera_make' => $validated['camera_make'] ?? null,
            'camera_model' => $validated['camera_model'] ?? null,
            'camera_settings' => $validated['camera_settings'] ?? null,
            'hashtags' => $validated['hashtags'] ?? null,
            'is_watermarked' => $validated['is_watermarked'] ?? false,
            'status' => 'pending_review'
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Submission uploaded successfully! It will be reviewed before appearing in the gallery.',
            'data' => $submission
        ], 201);
    }

    /**
     * Get my submissions for a competition
     */
    public function mySubmissions(Request $request, $competitionId)
    {
        $user = $request->user();
        
        $submissions = CompetitionSubmission::with(['competition'])
            ->forCompetition($competitionId)
            ->byPhotographer($user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $submissions
        ]);
    }

    /**
     * Update submission (before deadline, only if pending)
     */
    public function update(Request $request, $competitionId, $submissionId)
    {
        $user = $request->user();
        
        $submission = CompetitionSubmission::forCompetition($competitionId)
            ->byPhotographer($user->id)
            ->findOrFail($submissionId);
        
        // Check if can edit
        if ($submission->status !== 'pending_review') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot edit submission after it has been reviewed'
            ], 403);
        }
        
        $competition = $submission->competition;
        if (now()->isAfter($competition->submission_deadline)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission deadline has passed'
            ], 403);
        }
        
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'date_taken' => 'nullable|date',
            'camera_make' => 'nullable|string|max:255',
            'camera_model' => 'nullable|string|max:255',
            'camera_settings' => 'nullable|string|max:500',
            'hashtags' => 'nullable|string|max:255',
            'is_watermarked' => 'boolean'
        ]);
        
        $submission->update($validated);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Submission updated successfully',
            'data' => $submission
        ]);
    }

    /**
     * Delete submission (before deadline, only if no votes)
     */
    public function destroy(Request $request, $competitionId, $submissionId)
    {
        $user = $request->user();
        
        $submission = CompetitionSubmission::forCompetition($competitionId)
            ->byPhotographer($user->id)
            ->findOrFail($submissionId);
        
        // Check if can delete
        if ($submission->vote_count > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete submission that has received votes'
            ], 403);
        }
        
        // Delete image files
        if ($submission->image_path) {
            Storage::disk('public')->delete($submission->image_path);
        }
        if ($submission->thumbnail_url) {
            $thumbnailPath = str_replace('/storage/', '', parse_url($submission->thumbnail_url, PHP_URL_PATH));
            Storage::disk('public')->delete($thumbnailPath);
        }
        
        $submission->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Submission deleted successfully'
        ]);
    }

    /**
     * Get all submissions for admin moderation (all statuses)
     */
    public function adminIndex(Request $request, $competitionId)
    {
        $query = CompetitionSubmission::with(['photographer.photographer', 'competition'])
            ->forCompetition($competitionId);
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->pending();
            } elseif ($request->status === 'approved') {
                $query->approved();
            } else {
                $query->where('status', $request->status);
            }
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('photographer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $submissions = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return response()->json([
            'status' => 'success',
            'data' => $submissions
        ]);
    }

    /**
     * Approve a submission
     */
    public function approve(Request $request, $competitionId, $submissionId)
    {
        $submission = CompetitionSubmission::forCompetition($competitionId)
            ->findOrFail($submissionId);
        
        if ($submission->status !== 'pending_review') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only pending submissions can be approved'
            ], 400);
        }
        
        $submission->update([
            'status' => 'approved',
            'rejection_reason' => null
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Submission approved successfully',
            'data' => $submission
        ]);
    }

    /**
     * Reject a submission
     */
    public function reject(Request $request, $competitionId, $submissionId)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);
        
        $submission = CompetitionSubmission::forCompetition($competitionId)
            ->findOrFail($submissionId);
        
        if ($submission->status !== 'pending_review') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only pending submissions can be rejected'
            ], 400);
        }
        
        $submission->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['reason']
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Submission rejected successfully',
            'data' => $submission
        ]);
    }

    /**
     * Disqualify a submission
     */
    public function disqualify(Request $request, $competitionId, $submissionId)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);
        
        $submission = CompetitionSubmission::forCompetition($competitionId)
            ->findOrFail($submissionId);
        
        $submission->update([
            'status' => 'disqualified',
            'rejection_reason' => $validated['reason']
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Submission disqualified successfully',
            'data' => $submission
        ]);
    }

    /**
     * Get moderation statistics
     */
    public function stats($competitionId)
    {
        $total = CompetitionSubmission::forCompetition($competitionId)->count();
        $pending = CompetitionSubmission::forCompetition($competitionId)->pending()->count();
        $approved = CompetitionSubmission::forCompetition($competitionId)->approved()->count();
        $rejected = CompetitionSubmission::forCompetition($competitionId)->where('status', 'rejected')->count();
        $disqualified = CompetitionSubmission::forCompetition($competitionId)->where('status', 'disqualified')->count();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $total,
                'pending' => $pending,
                'approved' => $approved,
                'rejected' => $rejected,
                'disqualified' => $disqualified
            ]
        ]);
    }

    /**
     * Get all submissions across all competitions (for admin)
     */
    public function allSubmissions(Request $request)
    {
        $query = CompetitionSubmission::with(['photographer.photographer', 'competition']);
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->pending();
            } elseif ($request->status === 'approved') {
                $query->approved();
            } else {
                $query->where('status', $request->status);
            }
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('photographer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('competition', function($q) use ($search) {
                      $q->where('title', 'like', "%{$search}%");
                  });
            });
        }
        
        $submissions = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return response()->json([
            'status' => 'success',
            'data' => $submissions
        ]);
    }

    /**
     * Get statistics for all submissions across all competitions (for admin)
     */
    public function allStats()
    {
        $total = CompetitionSubmission::count();
        $pending = CompetitionSubmission::pending()->count();
        $approved = CompetitionSubmission::approved()->count();
        $rejected = CompetitionSubmission::where('status', 'rejected')->count();
        $disqualified = CompetitionSubmission::where('status', 'disqualified')->count();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $total,
                'pending' => $pending,
                'approved' => $approved,
                'rejected' => $rejected,
                'disqualified' => $disqualified
            ]
        ]);
    }
}

