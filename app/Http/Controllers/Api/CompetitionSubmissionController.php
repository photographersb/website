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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CompetitionSubmissionController extends Controller
{
    use ApiResponse;

    /**
     * Get all submissions for a competition (public gallery)
     */
    public function index(Request $request, Competition $competition)
    {
        $competitionId = $competition->id;
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
        
        $perPage = (int) $request->get('per_page', 20);
        if ($perPage < 1) {
            $perPage = 20;
        }
        $perPage = min($perPage, 200);

        $submissions = $query->paginate($perPage);
        
        return $this->paginated($submissions, 'Approved submissions retrieved successfully');
    }

    /**
     * Get single submission details
     */
    public function show(Competition $competition, $submissionId)
    {
        $competitionId = $competition->id;
        $submissionQuery = CompetitionSubmission::with(['photographer.photographer', 'competition']);

        if ($competition->show_judge_reactions) {
            $submissionQuery->with(['scores' => function ($query) {
                $query->where('status', 'completed')
                    ->with('judge:id,name,profile_photo_url');
            }]);
        }

        $submission = $submissionQuery
            ->forCompetition($competitionId)
            ->findOrFail($submissionId);

        if (!$submission->short_url) {
            do {
                $shortCode = Str::random(8);
                $exists = CompetitionSubmission::where('short_url', $shortCode)->exists();
            } while ($exists);

            $submission->short_url = $shortCode;
            if (!$submission->share_token) {
                $submission->share_token = Str::random(32);
            }
            $submission->save();
        }
        
        // Increment view count
        $submission->incrementViewCount();
        
        return $this->success($submission, 'Submission details retrieved successfully');
    }

    /**
     * Import an image from Pexels (page or direct image URL)
     */
    public function importPexelsImage(Request $request)
    {
        $url = $request->query('url') ?: $request->input('url');

        if (!$url) {
            $json = $request->json()->all();
            $url = $json['url'] ?? null;
        }

        if (!$url) {
            $raw = trim((string) $request->getContent());
            if (filter_var($raw, FILTER_VALIDATE_URL)) {
                $url = $raw;
            }
        }

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->error('A valid Pexels URL is required.', 422);
        }
        $host = parse_url($url, PHP_URL_HOST);

        if (!$host || !preg_match('/(^|\.)pexels\.com$/i', $host)) {
            return $this->error('Only Pexels URLs are allowed.', 422);
        }

        $imageUrl = $url;

        if (!preg_match('/(^|\.)images\.pexels\.com$/i', $host)) {
            if (preg_match('/\/photo\/(\d+)\/?/i', $url, $matches)) {
                $photoId = $matches[1];
                $candidate = "https://images.pexels.com/photos/{$photoId}/pexels-photo-{$photoId}.jpeg";
                $headResponse = Http::timeout(10)
                    ->withOptions([
                        'verify' => false,
                        'allow_redirects' => true,
                    ])
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                        'Accept' => 'image/*',
                        'Referer' => 'https://www.pexels.com/',
                    ])
                    ->head($candidate);

                if ($headResponse->ok()) {
                    $imageUrl = $candidate;
                }
            }

            if ($imageUrl === $url) {
            $pageResponse = Http::timeout(20)
                ->withOptions([
                    'verify' => false,
                    'allow_redirects' => true,
                ])
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);
            if (!$pageResponse->ok()) {
                return $this->error('Unable to fetch the Pexels page. Please paste a direct Pexels image link.', 422);
            }

            $html = $pageResponse->body();
            if (preg_match('/property="og:image" content="([^"]+)"/i', $html, $matches)) {
                $imageUrl = $matches[1];
            } elseif (preg_match('/name="twitter:image" content="([^"]+)"/i', $html, $matches)) {
                $imageUrl = $matches[1];
            } elseif (preg_match('/https:\/\/images\.pexels\.com\/photos\/[^"\s]+/i', $html, $matches)) {
                $imageUrl = $matches[0];
            }
            }
        }

        $imageHost = parse_url($imageUrl, PHP_URL_HOST);
        if (!$imageHost || !preg_match('/(^|\.)images\.pexels\.com$/i', $imageHost)) {
            return $this->error('Please provide a Pexels image link.', 422);
        }

        $imageResponse = Http::timeout(25)
            ->withOptions([
                'verify' => false,
                'allow_redirects' => true,
            ])
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0',
                'Accept' => 'image/*',
                'Referer' => 'https://www.pexels.com/',
            ])
            ->get($imageUrl);
        if (!$imageResponse->ok()) {
            return $this->error('Unable to download the Pexels image.', 422);
        }

        $contentType = $imageResponse->header('Content-Type', 'image/jpeg');

        return response($imageResponse->body(), 200, [
            'Content-Type' => $contentType,
        ]);
    }

    /**
     * Submit a photo to competition (authenticated photographers)
     */
    public function store(Request $request, Competition $competition, PhotoMetadataService $metadataService, ImageProcessingService $imageService)
    {
        $competitionId = $competition->id;
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
            'is_watermarked' => 'boolean',
            'agree_to_terms' => 'accepted'
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
            'camera_settings' => $this->normalizeCameraSettings($validated['camera_settings'] ?? null),
            'hashtags' => $validated['hashtags'] ?? null,
            'is_watermarked' => $validated['is_watermarked'] ?? false,
            'status' => 'pending_review',
            'terms_accepted_at' => now(),
        ]);
        
        return $this->created($submission, 'Submission uploaded successfully! It will be reviewed before appearing in the gallery.');
    }

    private function normalizeCameraSettings(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return json_encode(['raw' => $value]);
    }

    /**
     * Get my submissions for a competition
     */
    public function mySubmissions(Request $request, Competition $competition)
    {
        $competitionId = $competition->id;
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
    public function update(Request $request, Competition $competition, $submissionId)
    {
        $competitionId = $competition->id;
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
    public function destroy(Request $request, Competition $competition, $submissionId)
    {
        $competitionId = $competition->id;
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
    public function adminIndex(Request $request, Competition $competition)
    {
        $competitionId = $competition->id;
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
        
        $submissions = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));
        
        return $this->paginated($submissions, 'Submissions retrieved successfully');
    }

    /**
     * Approve a submission
     */
    public function approve(Request $request, Competition $competition, $submissionId)
    {
        $competitionId = $competition->id;
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
    public function reject(Request $request, Competition $competition, $submissionId)
    {
        $competitionId = $competition->id;
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
    public function disqualify(Request $request, Competition $competition, $submissionId)
    {
        $competitionId = $competition->id;
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
    public function stats(Competition $competition)
    {
        $competitionId = $competition->id;
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

        if ($request->filled('competition_id')) {
            $query->where('competition_id', $request->get('competition_id'));
        }
        
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

        if ($request->filled('score_range')) {
            $range = $request->get('score_range');
            $scoreExpr = 'COALESCE(final_score, judge_score)';

            if ($range === 'high') {
                $query->whereRaw("{$scoreExpr} >= ?", [8]);
            } elseif ($range === 'medium') {
                $query->whereRaw("{$scoreExpr} >= ? AND {$scoreExpr} < ?", [5, 8]);
            } elseif ($range === 'low') {
                $query->whereRaw("{$scoreExpr} < ?", [5]);
            }
        }
        
        $submissions = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));
        
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

