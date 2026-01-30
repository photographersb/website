<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PhotographerController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\CompetitionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AlbumController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PhotoController;
use App\Http\Controllers\Api\Photographer\PhotographerCompetitionController;
use App\Http\Controllers\Api\Photographer\PhotographerEventController;
use App\Http\Controllers\Api\Admin\AdminCompetitionApiController;
use App\Http\Controllers\Api\Admin\AdminEventApiController;
use App\Http\Controllers\Api\CompetitionSubmissionController;
use App\Http\Controllers\Api\CompetitionVoteController;
use App\Http\Controllers\Api\CompetitionJudgeController;
use App\Http\Controllers\Api\CompetitionCategoryController;
use App\Http\Controllers\Api\CompetitionSponsorController;
use App\Http\Controllers\Api\Admin\ActivityLogController;
use App\Http\Controllers\Api\Admin\ExportController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,60');
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:3,60');
    Route::post('/auth/verify-email', [AuthController::class, 'verifyEmail'])->middleware('throttle:10,60');
    Route::post('/auth/resend-verification', [AuthController::class, 'resendVerification'])->middleware('throttle:3,60');

    // Public resources
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/cities', [CityController::class, 'index']);

    // Public photographers
    Route::get('/photographers', [PhotographerController::class, 'index']);
    Route::get('/photographers/{photographer}', [PhotographerController::class, 'show']);
    Route::get('/photographers/search', [PhotographerController::class, 'search']);

    // Public events
    Route::get('/events/stats', [EventController::class, 'stats']);
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{slug}', [EventController::class, 'show']);

    // Public competitions
    Route::get('/competitions', [CompetitionController::class, 'index']);
    Route::get('/competitions/stats', [CompetitionController::class, 'stats']);
    Route::get('/competitions/{competition}', [CompetitionController::class, 'show']);
    Route::get('/competitions/{competition}/leaderboard', [CompetitionController::class, 'leaderboard']);
    Route::get('/competitions/{competition}/winners', [CompetitionController::class, 'getWinners']);
    Route::get('/competitions/{competition}/full-leaderboard', [CompetitionController::class, 'getLeaderboard']);
    
    // Public competition submissions (gallery)
    Route::get('/competitions/{competition}/submissions', [CompetitionSubmissionController::class, 'index']);
    Route::get('/competitions/{competition}/submissions/{submission}', [CompetitionSubmissionController::class, 'show']);
    
    // Public competition voting stats
    Route::get('/competitions/{competition}/voting/stats', [CompetitionVoteController::class, 'stats']);

    // Public competition categories
    Route::get('/competitions/{competition}/categories', [CompetitionCategoryController::class, 'index']);
    Route::get('/categories/{category}', [CompetitionCategoryController::class, 'show']);
    Route::get('/categories/{category}/leaderboard', [CompetitionCategoryController::class, 'leaderboard']);
    Route::get('/competitions/{competition}/winners-by-category', [CompetitionCategoryController::class, 'winnersByCategory']);

    // Public competition sponsors
    Route::get('/competitions/{competition}/sponsors', [CompetitionSponsorController::class, 'index']);
    Route::get('/sponsors/{sponsor}', [CompetitionSponsorController::class, 'show']);

    // Certificate download (public access for certificate holders)
    Route::get('/certificates/{certificate_id}/download', [CompetitionController::class, 'downloadCertificate']);
    Route::get('/certificates/{certificate_id}', [CompetitionController::class, 'getCertificateDetails']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Bookings
        Route::post('/bookings/inquiry', [BookingController::class, 'createInquiry'])->middleware('throttle:10,1');
        Route::get('/bookings', [BookingController::class, 'myBookings']);
        Route::get('/bookings/{booking}', [BookingController::class, 'getBooking']);
        Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
        Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancelBooking']);

        // Reviews
        Route::get('/reviews', [ReviewController::class, 'index']);
        Route::post('/reviews', [ReviewController::class, 'store'])->middleware('throttle:5,60');
        Route::get('/photographers/{photographer_id}/reviews', [ReviewController::class, 'getPhotographerReviews']);

        // Events
        Route::get('/events/{eventId}/rsvp-status', [EventController::class, 'rsvpStatus']);
        Route::post('/events/{eventId}/rsvp', [EventController::class, 'rsvp'])->middleware('throttle:20,60');

        // Competitions
        Route::post('/competitions/{competition}/submit', [CompetitionController::class, 'submit']);
        Route::post('/competition-submissions/{submission}/vote', [CompetitionController::class, 'vote']);
        
        // Competition Submissions (for participants)
        Route::post('/competitions/{competition}/submissions', [CompetitionSubmissionController::class, 'store'])->middleware('throttle:10,60');
        Route::get('/competitions/{competition}/my-submissions', [CompetitionSubmissionController::class, 'mySubmissions']);
        Route::put('/competitions/{competition}/submissions/{submission}', [CompetitionSubmissionController::class, 'update'])->middleware('throttle:10,60');
        Route::delete('/competitions/{competition}/submissions/{submission}', [CompetitionSubmissionController::class, 'destroy']);
        
        // Competition Voting
        Route::post('/competitions/{competition}/submissions/{submission}/vote', [CompetitionVoteController::class, 'vote'])->middleware('throttle:60,60');
        Route::delete('/competitions/{competition}/submissions/{submission}/vote', [CompetitionVoteController::class, 'unvote'])->middleware('throttle:60,60');
        Route::get('/competitions/{competition}/submissions/{submission}/vote-status', [CompetitionVoteController::class, 'checkVote']);
        Route::get('/competitions/{competition}/my-votes', [CompetitionVoteController::class, 'myVotes']);
        
        // Judge Scoring
        Route::get('/judge/assignments', [CompetitionJudgeController::class, 'getMyAssignments']);
        Route::get('/competitions/{competition}/judge/submissions', [CompetitionJudgeController::class, 'getAssignedSubmissions']);
        Route::post('/competitions/{competition}/submissions/{submission}/score', [CompetitionJudgeController::class, 'submitScore'])->middleware('throttle:30,60');
        Route::get('/competitions/{competition}/judge/progress', [CompetitionJudgeController::class, 'getScoringProgress']);

        // Photographer Competition Management (for verified photographers)
        Route::prefix('photographer')->group(function () {
            // Dashboard
            Route::get('/dashboard', [PhotographerController::class, 'dashboard']);
            
            // Profile management
            Route::post('/profile/avatar', [PhotographerController::class, 'updateAvatar']);
            Route::patch('/profile', [PhotographerController::class, 'updateProfile']);

            // Album Management
            Route::get('/albums', [\App\Http\Controllers\Api\AlbumController::class, 'index']);
            Route::post('/albums', [\App\Http\Controllers\Api\AlbumController::class, 'store']);
            Route::get('/albums/{id}', [\App\Http\Controllers\Api\AlbumController::class, 'show']);
            Route::put('/albums/{id}', [\App\Http\Controllers\Api\AlbumController::class, 'update']);
            Route::delete('/albums/{id}', [\App\Http\Controllers\Api\AlbumController::class, 'destroy']);

            // Photo Management
            Route::post('/albums/{albumId}/photos', [\App\Http\Controllers\Api\PhotoController::class, 'store']);
            Route::put('/photos/{id}', [\App\Http\Controllers\Api\PhotoController::class, 'update']);
            Route::delete('/photos/{id}', [\App\Http\Controllers\Api\PhotoController::class, 'destroy']);
            Route::get('/photos/search-pexels', [\App\Http\Controllers\Api\PhotoController::class, 'searchPexels']);

            // Package Management
            Route::get('/packages', [\App\Http\Controllers\Api\PackageController::class, 'index']);
            Route::post('/packages', [\App\Http\Controllers\Api\PackageController::class, 'store']);
            Route::get('/packages/{id}', [\App\Http\Controllers\Api\PackageController::class, 'show']);
            Route::put('/packages/{id}', [\App\Http\Controllers\Api\PackageController::class, 'update']);
            Route::delete('/packages/{id}', [\App\Http\Controllers\Api\PackageController::class, 'destroy']);

            // Competition Management
            Route::get('/competitions', [PhotographerCompetitionController::class, 'index']);
            Route::post('/competitions', [PhotographerCompetitionController::class, 'store']);
            Route::get('/competitions/{id}', [PhotographerCompetitionController::class, 'show']);
            Route::put('/competitions/{id}', [PhotographerCompetitionController::class, 'update']);
            Route::delete('/competitions/{id}', [PhotographerCompetitionController::class, 'destroy']);

            // Event Management
            Route::get('/events', [PhotographerEventController::class, 'index']);
            Route::post('/events', [PhotographerEventController::class, 'store']);
            Route::get('/events/{id}', [PhotographerEventController::class, 'show']);
            Route::put('/events/{id}', [PhotographerEventController::class, 'update']);
            Route::delete('/events/{id}', [PhotographerEventController::class, 'destroy']);
            Route::post('/events/{id}/cancel', [PhotographerEventController::class, 'cancel']);
        });

        // Payments
        Route::post('/payments/initiate', [PaymentController::class, 'initiatePayment']);
        Route::get('/payments/transactions', [PaymentController::class, 'myTransactions']);
        Route::get('/payments/transactions/{transactionId}', [PaymentController::class, 'getTransaction']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

        // Admin Routes
        Route::prefix('admin')->group(function () {
            // Dashboard
            Route::get('/dashboard', [AdminController::class, 'dashboard']);
            
            // User Management
            Route::get('/users', [AdminController::class, 'users']);
            Route::post('/users', [AdminController::class, 'storeUser']);
            Route::get('/users/{user}', [AdminController::class, 'showUser']);
            Route::put('/users/{user}', [AdminController::class, 'updateUser']);
            Route::delete('/users/{user}', [AdminController::class, 'deleteUser']);
            Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser']);
            Route::post('/users/{user}/unsuspend', [AdminController::class, 'unsuspendUser']);
            
            // Verification Management
            Route::get('/verifications', [AdminController::class, 'getVerifications']);
            Route::post('/verifications/{verification}/approve', [AdminController::class, 'approveVerification']);
            Route::post('/verifications/{verification}/reject', [AdminController::class, 'rejectVerification']);
            
            // Audit Logs
            Route::get('/audit-logs', [AdminController::class, 'auditLogs']);
            
            // Competition Management
            Route::get('/competitions', [AdminCompetitionApiController::class, 'index']);
            Route::post('/competitions', [AdminCompetitionApiController::class, 'store']);
            Route::get('/competitions/{id}', [AdminCompetitionApiController::class, 'show']);
            Route::put('/competitions/{id}', [AdminCompetitionApiController::class, 'update']);
            Route::delete('/competitions/{id}', [AdminCompetitionApiController::class, 'destroy']);

            // Event Management
            Route::get('/events', [AdminEventApiController::class, 'index']);
            Route::post('/events', [AdminEventApiController::class, 'store']);
            Route::get('/events/{id}', [AdminEventApiController::class, 'show']);
            Route::put('/events/{id}', [AdminEventApiController::class, 'update']);
            Route::delete('/events/{id}', [AdminEventApiController::class, 'destroy']);
            Route::post('/events/bulk-update-status', [AdminEventApiController::class, 'bulkUpdateStatus']);
            Route::post('/events/{id}/toggle-featured', [AdminEventApiController::class, 'toggleFeatured']);
            
            // All Submissions (across all competitions)
            Route::get('/submissions', [CompetitionSubmissionController::class, 'allSubmissions']);
            Route::get('/submissions/stats', [CompetitionSubmissionController::class, 'allStats']);
            
            // Submission Moderation (specific competition)
            Route::get('/competitions/{competition}/submissions', [CompetitionSubmissionController::class, 'adminIndex']);
            Route::get('/competitions/{competition}/submissions/stats', [CompetitionSubmissionController::class, 'stats']);
            Route::post('/competitions/{competition}/submissions/{submission}/approve', [CompetitionSubmissionController::class, 'approve']);
            Route::post('/competitions/{competition}/submissions/{submission}/reject', [CompetitionSubmissionController::class, 'reject']);
            Route::post('/competitions/{competition}/submissions/{submission}/disqualify', [CompetitionSubmissionController::class, 'disqualify']);
            
            // Judge Management
            Route::post('/competitions/{competition}/judges', [CompetitionJudgeController::class, 'assignJudge']);
            Route::delete('/competitions/{competition}/judges/{judge}', [CompetitionJudgeController::class, 'removeJudge']);
            Route::get('/competitions/{competition}/judges', [CompetitionJudgeController::class, 'getJudges']);
            Route::get('/competitions/{competition}/scoring/stats', [CompetitionJudgeController::class, 'getScoringStats']);
            
            // Winner Calculation & Announcement
            Route::post('/competitions/{competition}/calculate-winners', [CompetitionController::class, 'calculateWinners']);
            Route::post('/competitions/{competition}/announce-winners', [CompetitionController::class, 'announceWinners']);
            
            // Certificate Generation (Admin)
            Route::post('/competitions/{competition}/generate-certificate', [CompetitionController::class, 'generateSingleCertificate']);
            Route::post('/competitions/{competition}/generate-certificates', [CompetitionController::class, 'generateAllCertificates']);
            
            // Prize Distribution (Admin)
            Route::post('/competitions/{competition}/set-prize', [CompetitionController::class, 'setPrize']);
            Route::post('/competitions/{competition}/set-all-prizes', [CompetitionController::class, 'setAllPrizes']);
            Route::post('/competitions/{competition}/update-prize-status', [CompetitionController::class, 'updatePrizeStatus']);
            Route::get('/competitions/{competition}/prize-report', [CompetitionController::class, 'getPrizeReport']);
            Route::get('/prizes/pending', [CompetitionController::class, 'getPendingPrizes']);
            Route::get('/prizes/statistics', [CompetitionController::class, 'getGlobalPrizeStats']);
            
            // Category Management (Admin)
            Route::post('/competitions/{competition}/categories', [CompetitionCategoryController::class, 'store']);
            Route::put('/categories/{category}', [CompetitionCategoryController::class, 'update']);
            Route::delete('/categories/{category}', [CompetitionCategoryController::class, 'destroy']);
            Route::post('/competitions/{competition}/categories/bulk', [CompetitionCategoryController::class, 'bulkCreate']);
            Route::post('/categories/{category}/toggle-active', [CompetitionCategoryController::class, 'toggleActive']);
            Route::get('/competitions/{competition}/categories/statistics', [CompetitionCategoryController::class, 'statistics']);
            
            // Sponsorship Management (Admin)
            Route::post('/competitions/{competition}/sponsors', [CompetitionSponsorController::class, 'store']);
            Route::put('/sponsors/{sponsor}', [CompetitionSponsorController::class, 'update']);
            Route::delete('/sponsors/{sponsor}', [CompetitionSponsorController::class, 'destroy']);
            Route::post('/competitions/{competition}/sponsors/bulk', [CompetitionSponsorController::class, 'bulkCreate']);
            Route::post('/sponsors/{sponsor}/toggle-active', [CompetitionSponsorController::class, 'toggleActive']);
            Route::post('/competitions/{competition}/sponsors/reorder', [CompetitionSponsorController::class, 'reorder']);
            Route::get('/competitions/{competition}/sponsors/statistics', [CompetitionSponsorController::class, 'statistics']);
            Route::get('/sponsors/global-statistics', [CompetitionSponsorController::class, 'globalStatistics']);
            
            // Activity Logs
            Route::get('/activity-logs', [ActivityLogController::class, 'index']);
            Route::get('/activity-logs/stats', [ActivityLogController::class, 'stats']);
            Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show']);
            Route::get('/activity-logs/user/{userId}', [ActivityLogController::class, 'userHistory']);
            Route::get('/activity-logs/model/history', [ActivityLogController::class, 'modelHistory']);
            
            // Data Exports
            Route::get('/export/users', [ExportController::class, 'exportUsers']);
            Route::get('/export/photographers', [ExportController::class, 'exportPhotographers']);
            Route::get('/export/bookings', [ExportController::class, 'exportBookings']);
            Route::get('/export/activity-logs', [ExportController::class, 'exportActivityLogs']);
            
            // Sponsor Management
            Route::get('/sponsors', [\App\Http\Controllers\Api\Admin\SponsorController::class, 'index']);
            Route::post('/sponsors', [\App\Http\Controllers\Api\Admin\SponsorController::class, 'store']);
            Route::get('/sponsors/{id}', [\App\Http\Controllers\Api\Admin\SponsorController::class, 'show']);
            Route::put('/sponsors/{id}', [\App\Http\Controllers\Api\Admin\SponsorController::class, 'update']);
            Route::delete('/sponsors/{id}', [\App\Http\Controllers\Api\Admin\SponsorController::class, 'destroy']);
            
            // Photo Category Management
            Route::get('/photo-categories', [\App\Http\Controllers\Api\Admin\PhotoCategoryController::class, 'index']);
            Route::post('/photo-categories', [\App\Http\Controllers\Api\Admin\PhotoCategoryController::class, 'store']);
            Route::get('/photo-categories/{id}', [\App\Http\Controllers\Api\Admin\PhotoCategoryController::class, 'show']);
            Route::put('/photo-categories/{id}', [\App\Http\Controllers\Api\Admin\PhotoCategoryController::class, 'update']);
            Route::delete('/photo-categories/{id}', [\App\Http\Controllers\Api\Admin\PhotoCategoryController::class, 'destroy']);
            
            // Hashtag Management
            Route::get('/hashtags', [\App\Http\Controllers\Api\Admin\HashtagController::class, 'index']);
            Route::get('/hashtags/featured', [\App\Http\Controllers\Api\Admin\HashtagController::class, 'featured']);
            Route::post('/hashtags', [\App\Http\Controllers\Api\Admin\HashtagController::class, 'store']);
            Route::get('/hashtags/{id}', [\App\Http\Controllers\Api\Admin\HashtagController::class, 'show']);
            Route::put('/hashtags/{id}', [\App\Http\Controllers\Api\Admin\HashtagController::class, 'update']);
            Route::delete('/hashtags/{id}', [\App\Http\Controllers\Api\Admin\HashtagController::class, 'destroy']);
        });
    });
});
