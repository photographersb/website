<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Services\PhotoMetadataService;
use App\Services\ImageProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
        
        return $this->paginated($submissions, 'Approved submissions retrieved successfully');
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
        
        return $this->success($submission, 'Submission details retrieved successfully');
    }

    /**
     * Submit a photo to competition (authenticated photographers)
     */
    public function store(Request $request, $competitionId, PhotoMetadataService $metadataService, ImageProcessingService $imageService)
    {
        $competition = Competition::findOrFail($competitionId);
        
        // Check if competition accepts submissions
        if ($competition->status !== 'active') {
            return $this->error('Competition is not accepting submissions', 403);
        }
        
        // Check submission deadline
        if (now()->isAfter($competition->submission_deadline)) {
            return $this->error('Submission deadline has passed', 403);
        }
        
        $user = $request->user();
        
        // Check max submissions
        $existingCount = CompetitionSubmission::forCompetition($competitionId)
            ->byPhotographer($user->id)
            ->count();
        
        if ($existingCount >= $competition->max_submissions_per_user) {
            return $this->error("Maximum {$competition->max_submissions_per_user} submissions allowed", 403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240', // 10MB max
            'location' => 'nullable|string|max:255',
            'date_taken' => 'nullable|date',
            'camera_make' => 'nullable|string|max:255',
            'camera_model' => 'nullable|string|max:255',
            'camera_settings' => 'nullable|string|max:500',
            'hashtags' => 'nullable|string|max:255',
            'is_watermarked' => 'boolean'
        ]);
        
        // Handle image upload with error handling
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Validate image before processing
            $validation = $imageService->validateImage($image, [
                'max_size' => 10240, // 10MB
                'min_width' => 1920,
                'min_height' => 1080,
                'allowed_mimes' => ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']
            ]);
            
            if (!$validation['valid']) {
                return $this->validationError([], $validation['error']);
            }
            
            try {
                // Extract EXIF metadata before processing
                $exifData = $metadataService->extractMetadata($image);
                
                // Merge EXIF with user input (user input takes priority)
                $validated = array_merge($exifData, $validated);
                
                $path = 'competitions/' . $competitionId . '/submissions';
                
                // Process and save image with error handling
                $result = $imageService->processAndSave($image, $path, [
                    'max_width' => 2048,
                    'max_height' => 2048,
                    'quality' => 85,
                    'format' => 'jpg'
                ]);
                
                if (!$result['success']) {
                    Log::error('Competition submission image processing failed', [
                        'competition_id' => $competitionId,
                        'user_id' => $user->id,
                        'error' => $result['error']
                    ]);
                    
                    return $this->error('Failed to process image: ' . $result['error'], 500);
                }
                
                $imageUrl = $result['url'];
                $imagePath = $result['path'];
                
                // Create thumbnail with error handling
                try {
                    if (!$imageService->isAvailable()) {
                        // No thumbnail, use full image
                        $thumbnailUrl = $imageUrl;
                        Log::warning('Thumbnail generation skipped - image processing not available');
                    } else {
                        $thumbnailResult = $imageService->processAndSave($image, $path, [
                            'max_width' => 400,
                            'max_height' => 400,
                            'quality' => 80,
                            'format' => 'jpg'
                        ]);
                        
                        $thumbnailUrl = $thumbnailResult['success'] ? $thumbnailResult['url'] : $imageUrl;
                    }
                } catch (\Exception $e) {
                    Log::warning('Thumbnail generation failed, using full image', [
                        'error' => $e->getMessage()
                    ]);
                    $thumbnailUrl = $imageUrl;
                }
                
                $validated['image_path'] = $imagePath;
                $validated['image_url'] = $imageUrl;
                $validated['thumbnail_url'] = $thumbnailUrl;
                
                if (isset($result['warning'])) {
                    Log::info('Image uploaded with warning: ' . $result['warning']);
                }
                
            } catch (\Exception $e) {
                Log::error('Competition submission failed', [
                    'competition_id' => $competitionId,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return $this->error('Failed to process your image. Please ensure it is a valid image file.', 500);
            }
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
        
        return $this->created($submission, 'Submission uploaded successfully! It will be reviewed before appearing in the gallery.');
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
        
        return $this->success($submissions, 'My submissions retrieved successfully');
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
            return $this->error('Cannot edit submission after it has been reviewed', 403);
        }
        
        $competition = $submission->competition;
        if (now()->isAfter($competition->submission_deadline)) {
            return $this->error('Submission deadline has passed', 403);
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
        
        return $this->success($submission, 'Submission updated successfully');
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
            return $this->error('Cannot delete submission that has received votes', 403);
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
        
        return $this->success(null, 'Submission deleted successfully');
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
        
        return $this->paginated($submissions, 'Submissions retrieved successfully');
    }

    /**
     * Approve a submission
     */
    public function approve(Request $request, $competitionId, $submissionId)
    {
        $submission = CompetitionSubmission::forCompetition($competitionId)
            ->findOrFail($submissionId);
        
        if ($submission->status !== 'pending_review') {
            return $this->error('Only pending submissions can be approved', 400);
        }
        
        $submission->update([
            'status' => 'approved',
            'rejection_reason' => null
        ]);
        
        return $this->success($submission, 'Submission approved successfully');
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
            return $this->error('Only pending submissions can be rejected', 400);
        }
        
        $submission->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['reason']
        ]);
        
        return $this->success($submission, 'Submission rejected successfully');
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
        
        return $this->success($submission, 'Submission disqualified successfully');
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
        
        return $this->success([
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'disqualified' => $disqualified
        ], 'Submission statistics retrieved successfully');
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
        
        return $this->paginated($submissions, 'All submissions retrieved successfully');
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
        
        return $this->success([
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'disqualified' => $disqualified
        ], 'All submission statistics retrieved successfully');
    }
}

