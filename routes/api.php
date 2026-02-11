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
use App\Http\Controllers\Api\LocationApiController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\NotificationPreferenceController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ClickEventController;
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
use App\Http\Controllers\Api\Admin\ContactMessageController;
use App\Http\Controllers\Api\Admin\EventCheckInController;
use App\Http\Controllers\Api\Admin\AdminReviewController;
use App\Http\Controllers\Api\Admin\AdminBookingController;
use App\Http\Controllers\Api\Admin\AdminTransactionController;
use App\Http\Controllers\Api\Admin\AdminSettingsController;
use App\Http\Controllers\Api\Admin\AdminProfileController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\CertificateTemplateController;
use App\Http\Controllers\Api\Admin\ErrorCenterController;
use App\Http\Controllers\Api\BookingMessageController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\SitemapController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PhotographerSettingsController;
use App\Http\Controllers\Api\FeaturedPhotographerPublicController;
use App\Http\Controllers\Admin\SiteLinkController;
use App\Models\Competition;
use App\Models\Judge;
use App\Models\Mentor;
use App\Models\Event;
use App\Models\Photographer;

// Route Model Bindings
Route::bind('competition', function ($value) {
    return Competition::where('id', $value)
        ->orWhere('slug', $value)
        ->firstOrFail();
});
Route::model('judge', Judge::class);
Route::model('mentor', Mentor::class);
Route::bind('event', function ($value) {
    return Event::where('id', $value)
        ->orWhere('slug', $value)
        ->firstOrFail();
});
Route::model('photographer', Photographer::class);

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:20,1');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:20,1');
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,60');
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:3,60');
    Route::post('/auth/verify-email', [AuthController::class, 'verifyEmail'])->middleware('throttle:10,60');
    Route::post('/auth/resend-verification', [AuthController::class, 'resendVerification'])->middleware('throttle:3,60');
    Route::post('/auth/send-phone-otp', [AuthController::class, 'sendPhoneOtp'])->middleware('throttle:3,60');
    Route::post('/auth/verify-phone-otp', [AuthController::class, 'verifyPhoneOtp'])->middleware('throttle:5,60');
    Route::post('/auth/resend-phone-otp', [AuthController::class, 'resendPhoneOtp'])->middleware('throttle:2,60');
    
    // Social Authentication Routes
    Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\Api\SocialAuthController::class, 'redirectToProvider'])
        ->where('provider', 'google|facebook|apple')
        ->middleware('throttle:5,1');
    Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Api\SocialAuthController::class, 'handleProviderCallback'])
        ->where('provider', 'google|facebook|apple')
        ->middleware('throttle:10,1');

    // Public resources
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/cities', [CityController::class, 'index']);
    Route::get('/locations', [LocationApiController::class, 'index']);
    Route::get('/site-links', [SiteLinkController::class, 'publicIndex']);
    Route::get('/featured-photographers', [FeaturedPhotographerPublicController::class, 'index']);
    
    // Platform statistics
    Route::get('/platform/stats', [\App\Http\Controllers\Api\PlatformStatsController::class, 'index']);
    
    // Public hashtags for dropdown
    Route::get('/hashtags', [\App\Http\Controllers\Api\Admin\HashtagController::class, 'index']);
    Route::get('/hashtags/featured', [\App\Http\Controllers\Api\Admin\HashtagController::class, 'featured']);
    
    // Public photo categories for dropdown
    Route::get('/photo-categories', [\App\Http\Controllers\Api\Admin\PhotoCategoryController::class, 'index']);

    // Public mentors
    Route::get('/mentors', function () {
        return response()->json([
            'status' => 'success',
            'data' => \App\Models\Mentor::active()->ordered()->get(),
        ]);
    });
    Route::get('/mentors/{mentor}', function (\App\Models\Mentor $mentor) {
        return response()->json([
            'status' => 'success',
            'data' => $mentor->load('competitions'),
        ]);
    });

    // Public judges
    Route::get('/judges', function () {
        return response()->json([
            'status' => 'success',
            'data' => \App\Models\Judge::active()->ordered()->get(),
        ]);
    });
    Route::get('/judges/{judge}', function (\App\Models\Judge $judge) {
        return response()->json([
            'status' => 'success',
            'data' => $judge->load('competitions', 'scores'),
        ]);
    });

    // Public photographers
    Route::middleware('throttle:200,1')->group(function () {
        Route::get('/photographers', [PhotographerController::class, 'index']);
        Route::get('/photographers/search', [PhotographerController::class, 'search']);
    });
    Route::get('/photographers/@{username}', [PhotographerController::class, 'showByUsername']);
    Route::get('/photographers/{photographerSlugOrId}', [PhotographerController::class, 'show']);
    Route::post('/photographers/profile-share-visit', [PhotographerController::class, 'trackProfileShareVisit'])
        ->middleware('throttle:60,1');
    Route::get('/photographers/{photographerId}/awards', [\App\Http\Controllers\Api\PhotographerAwardController::class, 'index']);

    // Public photographer tips
    Route::get('/photographers/{photographerId}/tips/info', [\App\Http\Controllers\Api\PhotographerTipController::class, 'getTipInfo']);
    Route::post('/photographers/{photographerId}/tips/initiate', [\App\Http\Controllers\Api\PhotographerTipController::class, 'initiateTip'])->middleware('throttle:10,60');
    Route::post('/photographers/tips/{tipId}/confirm', [\App\Http\Controllers\Api\PhotographerTipController::class, 'confirmTip'])->middleware('throttle:10,60')->name('api.photographer.tip.confirm');

    // Public events
    Route::middleware('throttle:200,1')->group(function () {
        Route::get('/events/stats', [EventController::class, 'stats']);
        Route::get('/events', [\App\Http\Controllers\Api\EventApiController::class, 'index']);
        Route::get('/events/featured', [\App\Http\Controllers\Api\EventApiController::class, 'featured']);
        Route::get('/events/cities', [\App\Http\Controllers\Api\EventApiController::class, 'cities']);
    });
    Route::get('/events/{slug}', [\App\Http\Controllers\Api\EventApiController::class, 'show']);
    Route::post('/events/{event}/rsvp', [EventController::class, 'rsvp'])
        ->middleware(['auth:sanctum', 'throttle:20,60']);

    // Public competitions
    Route::middleware('throttle:200,1')->group(function () {
        Route::get('/competitions', [CompetitionController::class, 'index']);
        Route::get('/competitions/stats', [CompetitionController::class, 'stats']);
    });
    Route::get('/competitions/{competition}', [CompetitionController::class, 'show']);

    // Public reviews/testimonials
    Route::get('/reviews/featured', [ReviewController::class, 'featured']);
    Route::get('/competitions/{competition}/leaderboard', [CompetitionController::class, 'leaderboard']);
    Route::get('/competitions/{competition}/winners', [CompetitionController::class, 'getWinners']);
    Route::get('/competitions/{competition}/full-leaderboard', [CompetitionController::class, 'getLeaderboard']);
    
    // Public competition submissions (gallery)
    Route::get('/competitions/{competition}/submissions', [CompetitionSubmissionController::class, 'index']);
    Route::get('/competitions/{competition}/submissions/{submission}', [CompetitionSubmissionController::class, 'show']);
    Route::post('/pexels/import', [CompetitionSubmissionController::class, 'importPexelsImage'])
        ->middleware('throttle:10,1');
    
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
    
    // Public platform sponsors
    Route::get('/sponsors', function () {
        return \App\Models\Sponsor::where('status', 'active')
            ->orderBy('display_order')
            ->get(['id', 'name', 'slug', 'logo', 'website', 'description']);
    });
    
    // Sponsor inquiry
    Route::post('/sponsor-inquiry', [ContactController::class, 'sponsorInquiry'])
        ->middleware('throttle:5,1');
    
    // General contact form
    Route::post('/contact', [ContactController::class, 'contact'])
        ->middleware('throttle:5,1');

    // Certificate download (public access for certificate holders)
    Route::get('/certificates/{certificate_id}/download', [CompetitionController::class, 'downloadCertificate']);
    Route::get('/certificates/{certificate_id}', [CompetitionController::class, 'getCertificateDetails']);

    // SEO Sitemaps (public)
    Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
    Route::get('/sitemap/photographers.xml', [SitemapController::class, 'photographers'])->name('api.sitemap.photographers');
    Route::get('/sitemap/categories.xml', [SitemapController::class, 'categories'])->name('api.sitemap.categories');
    Route::get('/sitemap/cities.xml', [SitemapController::class, 'cities'])->name('api.sitemap.cities');
    Route::get('/sitemap/competitions.xml', [SitemapController::class, 'competitions'])->name('api.sitemap.competitions');
    Route::get('/sitemap/static.xml', [SitemapController::class, 'static'])->name('api.sitemap.static');

    // Health check (public)
    Route::get('/health', [\App\Http\Controllers\Api\HealthController::class, 'check']);

    // Click tracking (public, batched)
    Route::post('/clicks/batch', [ClickEventController::class, 'storeBatch'])->middleware('throttle:600,1');

    // Featured Photographers Payment Webhooks (public)
    Route::post('/featured-photographers/payments/bkash-callback', [\App\Http\Controllers\Api\FeaturedPhotographerPaymentController::class, 'bkashCallback']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        
        // Social Authentication Protected Routes
        Route::post('/auth/link-social-account', [\App\Http\Controllers\Api\SocialAuthController::class, 'linkAccount'])
            ->middleware('throttle:5,60');
        Route::post('/auth/unlink-social-account', [\App\Http\Controllers\Api\SocialAuthController::class, 'unlinkAccount'])
            ->middleware('throttle:5,60');
        Route::get('/auth/linked-accounts', [\App\Http\Controllers\Api\SocialAuthController::class, 'getLinkedAccounts']);

        // Bookings
        Route::post('/bookings/inquiry', [BookingController::class, 'createInquiry'])->middleware('throttle:10,1');
        Route::get('/bookings', [BookingController::class, 'myBookings']);
        Route::get('/bookings/{booking}', [BookingController::class, 'getBooking']);
        Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
        Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancelBooking']);
        Route::post('/bookings/{booking}/invoice/generate', [BookingController::class, 'generateInvoice'])->middleware('throttle:5,60');
        Route::get('/bookings/{booking}/invoice/download', [BookingController::class, 'downloadInvoice'])->name('bookings.invoice.download');
        Route::post('/bookings/{booking}/invoice/email', [BookingController::class, 'emailInvoice'])->middleware('throttle:3,60');

        // Reviews

                // P0: Booking Messages
                Route::prefix('bookings/{booking}/messages')->group(function () {
                    Route::get('/', [BookingMessageController::class, 'index'])->name('booking.messages.index');
                    Route::post('/', [BookingMessageController::class, 'store'])->middleware('throttle:20,60')->name('booking.messages.store');
                    Route::get('/{message}', [BookingMessageController::class, 'show'])->name('booking.messages.show');
                    Route::post('/{message}/read', [BookingMessageController::class, 'markAsRead'])->name('booking.messages.read');
                    Route::delete('/{message}', [BookingMessageController::class, 'destroy'])->name('booking.messages.destroy');
                });
        
                // Mark all booking messages as read
                Route::post('/bookings/{booking}/messages/mark-all-read', [BookingMessageController::class, 'markAllAsRead'])->middleware('throttle:10,60');

                // P0: Photographer Verification
                // Featured Photographers Payments (authenticated photographer)
        Route::prefix('featured-photographers/payments')->group(function () {
            Route::post('/initiate', [\App\Http\Controllers\Api\FeaturedPhotographerPaymentController::class, 'initiate'])->middleware('throttle:10,60');
            Route::get('/statistics', [\App\Http\Controllers\Api\FeaturedPhotographerPaymentController::class, 'statistics']);
            Route::get('/{payment}', [\App\Http\Controllers\Api\FeaturedPhotographerPaymentController::class, 'show']);
            Route::get('/featured/{featured}', [\App\Http\Controllers\Api\FeaturedPhotographerPaymentController::class, 'forFeaturedPhotographer']);
        });

        // Featured Photographers Upgrade (authenticated photographer)
        Route::prefix('featured-photographers/upgrade')->group(function () {
            Route::get('/options/{featured}', [\App\Http\Controllers\Api\FeaturedPhotographerUpgradeController::class, 'getUpgradeOptions'])->middleware('throttle:10,60');
            Route::post('/{featured}', [\App\Http\Controllers\Api\FeaturedPhotographerUpgradeController::class, 'initiateUpgrade'])->middleware('throttle:5,60');
            Route::post('/{upgrade}/confirm', [\App\Http\Controllers\Api\FeaturedPhotographerUpgradeController::class, 'confirmUpgrade'])->middleware('throttle:10,60');
            Route::get('/{featured}/history', [\App\Http\Controllers\Api\FeaturedPhotographerUpgradeController::class, 'getHistory']);
        });

        // Featured Photographers Analytics (authenticated photographer)
        Route::prefix('featured-photographers/analytics')->group(function () {
            Route::get('/{featured}', [\App\Http\Controllers\Api\FeaturedPhotographerAnalyticsController::class, 'show']);
            Route::post('/{featured}/record-view', [\App\Http\Controllers\Api\FeaturedPhotographerAnalyticsController::class, 'recordView']);
            Route::post('/{featured}/record-profile-click', [\App\Http\Controllers\Api\FeaturedPhotographerAnalyticsController::class, 'recordProfileClick']);
            Route::post('/{featured}/record-portfolio-click', [\App\Http\Controllers\Api\FeaturedPhotographerAnalyticsController::class, 'recordPortfolioClick']);
            Route::post('/{featured}/record-inquiry', [\App\Http\Controllers\Api\FeaturedPhotographerAnalyticsController::class, 'recordInquiry']);
            Route::post('/{featured}/record-booking', [\App\Http\Controllers\Api\FeaturedPhotographerAnalyticsController::class, 'recordBooking']);
            Route::get('/{featured}/export', [\App\Http\Controllers\Api\FeaturedPhotographerAnalyticsController::class, 'export']);
        });

        // Photographer Tips (authenticated users)
        Route::prefix('photographers')->group(function () {
            Route::get('/{photographerId}/tips', [\App\Http\Controllers\Api\PhotographerTipController::class, 'getPhotographerTips']);
        });

        // Photographer Settings (authenticated photographer)
        Route::prefix('photographer/settings')->group(function () {
            Route::get('/', [PhotographerSettingsController::class, 'getSettings']);
            Route::put('/profile', [PhotographerSettingsController::class, 'updateProfile']);
            Route::post('/profile', [PhotographerSettingsController::class, 'updateProfile']);
            Route::put('/tips', [PhotographerSettingsController::class, 'updateTips']);
            Route::put('/social', [PhotographerSettingsController::class, 'updateSocial']);
            Route::put('/availability', [PhotographerSettingsController::class, 'updateAvailability']);
        });

        Route::prefix('verifications')->group(function () {
                    // Photographer endpoints
                    Route::get('/status/{photographer}', [VerificationController::class, 'getStatus'])->name('verifications.status');
                    Route::post('/submit', [VerificationController::class, 'submitRequest'])->middleware('throttle:30,60')->name('verifications.submit');
                    Route::post('/renew', [VerificationController::class, 'renewVerification'])->middleware('throttle:30,60')->name('verifications.renew');
            
                    // Admin endpoints
                    Route::middleware('role:admin,super_admin')->group(function () {
                        Route::get('/pending', [VerificationController::class, 'getPendingRequests'])->name('verifications.pending');
                        Route::post('/{verificationRequest}/approve', [VerificationController::class, 'approveRequest'])->middleware('throttle:10,60')->name('verifications.approve');
                        Route::post('/{verificationRequest}/reject', [VerificationController::class, 'rejectRequest'])->middleware('throttle:10,60')->name('verifications.reject');
                        Route::post('/{photographer}/verifications/{verification}/revoke', [VerificationController::class, 'revokeVerification'])->middleware('throttle:10,60')->name('verifications.revoke');
                        Route::get('/{photographer}/history', [VerificationController::class, 'getPhotographerHistory'])->name('verifications.history');
                    });
                });

        Route::get('/reviews', [ReviewController::class, 'index']);
        Route::post('/reviews', [ReviewController::class, 'store'])->middleware('throttle:5,60');
        Route::get('/photographers/{photographer_id}/reviews', [ReviewController::class, 'getPhotographerReviews']);

        // Competitions
        Route::post('/competitions/{competition}/submit', [CompetitionController::class, 'submit']);
        
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
            Route::post('/profile-share', [PhotographerController::class, 'logProfileShare'])
                ->middleware('throttle:30,1');

            // Album Management
            Route::get('/albums', [\App\Http\Controllers\Api\AlbumController::class, 'index']);
            Route::post('/albums', [\App\Http\Controllers\Api\AlbumController::class, 'store']);
            Route::get('/albums/{id}', [\App\Http\Controllers\Api\AlbumController::class, 'show']);
            Route::put('/albums/{id}', [\App\Http\Controllers\Api\AlbumController::class, 'update']);
            Route::delete('/albums/{id}', [\App\Http\Controllers\Api\AlbumController::class, 'destroy']);

            // Photo Management
            Route::post('/albums/{albumId}/photos', [\App\Http\Controllers\Api\PhotoController::class, 'store']);
            Route::post('/albums/{albumId}/photos/upload', [\App\Http\Controllers\Api\PhotoController::class, 'upload']);
            Route::put('/photos/{id}', [\App\Http\Controllers\Api\PhotoController::class, 'update']);
            Route::delete('/photos/{id}', [\App\Http\Controllers\Api\PhotoController::class, 'destroy']);
            Route::get('/photos/search-pexels', [\App\Http\Controllers\Api\PhotoController::class, 'searchPexels']);

            // Package Management
            Route::get('/packages', [\App\Http\Controllers\Api\PackageController::class, 'index']);
            Route::post('/packages', [\App\Http\Controllers\Api\PackageController::class, 'store']);
            Route::get('/packages/{id}', [\App\Http\Controllers\Api\PackageController::class, 'show']);
            Route::put('/packages/{id}', [\App\Http\Controllers\Api\PackageController::class, 'update']);
            Route::post('/packages/{id}/images', [\App\Http\Controllers\Api\PackageController::class, 'uploadImages']);
            Route::delete('/packages/{id}', [\App\Http\Controllers\Api\PackageController::class, 'destroy']);

            // Awards Management
            Route::get('/awards', [PhotographerController::class, 'getAwards']);
            Route::post('/awards', [PhotographerController::class, 'storeAward']);
            Route::put('/awards/{id}', [PhotographerController::class, 'updateAward']);
            Route::delete('/awards/{id}', [PhotographerController::class, 'deleteAward']);

            // My Submissions (competition entries)
            Route::get('/submissions', [PhotographerController::class, 'getMySubmissions']);
            
            // My Event RSVPs (registered events)
            Route::get('/event-rsvps', [PhotographerController::class, 'getMyEventRsvps']);

            // Notifications
            Route::get('/notifications', [PhotographerController::class, 'getNotifications']);
            Route::get('/notifications/unread-count', [PhotographerController::class, 'getUnreadNotificationCount']);
            Route::post('/notifications/{id}/read', [PhotographerController::class, 'markNotificationAsRead']);
            Route::post('/notifications/mark-all-read', [PhotographerController::class, 'markAllNotificationsAsRead']);
            Route::delete('/notifications/{id}', [PhotographerController::class, 'deleteNotification']);

            // Achievements & Gamification
            Route::get('/achievements', [PhotographerController::class, 'getAchievements']);

            // Onboarding Checklist
            Route::get('/onboarding/checklist', [\App\Http\Controllers\Api\PhotographerOnboardingController::class, 'getChecklist']);
            Route::put('/onboarding/checklist/update-step', [\App\Http\Controllers\Api\PhotographerOnboardingController::class, 'updateStep']);
            Route::get('/onboarding/progress', [\App\Http\Controllers\Api\PhotographerOnboardingController::class, 'getProgress']);

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
            
            // Awards & Achievements Management
            Route::get('/awards', [\App\Http\Controllers\Api\PhotographerAwardController::class, 'index']);
            Route::post('/awards', [\App\Http\Controllers\Api\PhotographerAwardController::class, 'store']);
            Route::put('/awards/{id}', [\App\Http\Controllers\Api\PhotographerAwardController::class, 'update']);
            Route::delete('/awards/{id}', [\App\Http\Controllers\Api\PhotographerAwardController::class, 'destroy']);
            Route::post('/awards/reorder', [\App\Http\Controllers\Api\PhotographerAwardController::class, 'reorder']);
        });

        // Payments
        Route::post('/payments/initiate', [PaymentController::class, 'initiatePayment']);
        Route::get('/payments/transactions', [PaymentController::class, 'myTransactions']);
        Route::get('/payments/transactions/{transactionId}', [PaymentController::class, 'getTransaction']);
        Route::post('/payments/{transactionId}/refund', [PaymentController::class, 'refund'])->middleware('throttle:3,60');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
        
        // Create Notifications (Testing/Admin)
        Route::post('/notifications/create-test', [\App\Http\Controllers\Api\NotificationTestController::class, 'createTestNotification'])
            ->middleware('role:admin,super_admin');
        Route::post('/notifications/create-admin', [\App\Http\Controllers\Api\NotificationTestController::class, 'createAdminNotification'])
            ->middleware('role:admin,super_admin');

        // Activity Logs - User's own activity
        Route::get('/my-activity', [ActivityLogController::class, 'myActivity']);

        // Notification Preferences
        Route::get('/notification-preferences', [NotificationPreferenceController::class, 'index']);
        Route::put('/notification-preferences', [NotificationPreferenceController::class, 'update']);
        Route::post('/notification-preferences/reset', [NotificationPreferenceController::class, 'reset']);
        Route::get('/notification-channels', [NotificationPreferenceController::class, 'channels']);

        // Judge Routes
        Route::prefix('judge')->middleware('role:judge,admin,super_admin')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Api\Judge\JudgeDashboardController::class, 'dashboard']);
            Route::get('/competitions', [\App\Http\Controllers\Api\Judge\JudgeDashboardController::class, 'myCompetitions']);
            Route::get('/competitions/{competition}/submissions', [\App\Http\Controllers\Api\Judge\JudgeDashboardController::class, 'competitionSubmissions']);
            Route::get('/competitions/{competition}/submissions/{submission}', [\App\Http\Controllers\Api\Judge\JudgeDashboardController::class, 'getSubmission']);
            Route::post('/competitions/{competition}/submissions/{submission}/score', [\App\Http\Controllers\Api\Judge\JudgeDashboardController::class, 'submitScore']);
            Route::get('/scoring-history', [\App\Http\Controllers\Api\Judge\JudgeDashboardController::class, 'scoringHistory']);
        });

        // Admin Routes
        Route::prefix('admin')->middleware('role:admin,super_admin,moderator')->group(function () {
            // Media Uploads
            Route::post('/media/upload', [\App\Http\Controllers\Api\Admin\MediaUploadController::class, 'upload']);

            // Dashboard
            Route::get('/dashboard', [AdminController::class, 'dashboard']);
            Route::get('/analytics', [AdminController::class, 'analytics']);
            Route::get('/system-health', [AdminController::class, 'systemHealth']);
            Route::get('/health', [\App\Http\Controllers\Api\HealthController::class, 'admin']);
            Route::get('/sitemaps/health', [\App\Http\Controllers\Api\Admin\SitemapHealthController::class, 'index']);

            // Profile & Account
            Route::get('/profile', [AdminProfileController::class, 'show']);
            Route::put('/profile', [AdminProfileController::class, 'updateProfile']);
            Route::put('/account', [AdminProfileController::class, 'updateAccount']);
            Route::put('/account/password', [AdminProfileController::class, 'updatePassword']);
            
            // User Management
            Route::get('/users', [AdminController::class, 'users']);
            Route::post('/users', [AdminController::class, 'storeUser']);
            Route::get('/users/{user}', [AdminController::class, 'showUser']);
            Route::put('/users/{user}', [AdminController::class, 'updateUser']);
            Route::delete('/users/{user}', [AdminController::class, 'deleteUser']);
            Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser']);
            Route::post('/users/{user}/unsuspend', [AdminController::class, 'unsuspendUser']);
            Route::post('/users/{user}/promote-to-mentor', [AdminController::class, 'promoteToMentor']);
            Route::post('/users/{user}/promote-to-judge', [AdminController::class, 'promoteToJudge']);
            
            // Verification Management
            Route::get('/verifications', [AdminController::class, 'getVerifications']);
            Route::post('/verifications/{verification}/approve', [AdminController::class, 'approveVerification']);
            Route::post('/verifications/{verification}/reject', [AdminController::class, 'rejectVerification']);
            
            // Audit Logs
            Route::get('/audit-logs', [AdminController::class, 'auditLogs']);

            // Roles & Permissions
            Route::get('/roles', [RoleController::class, 'index']);
            Route::post('/roles', [RoleController::class, 'store']);
            Route::put('/roles/{role}', [RoleController::class, 'update']);
            Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
            
            // Activity Logs
            Route::get('/activity-logs', [ActivityLogController::class, 'index']);
            Route::get('/activity-logs/statistics', [ActivityLogController::class, 'statistics']);
            Route::get('/activity-logs/export', [ActivityLogController::class, 'export']);
            Route::get('/activity-logs/model/{modelType}/{modelId}', [ActivityLogController::class, 'modelActivity']);
            
            // Judges Management
            Route::get('/judges', [\App\Http\Controllers\Api\Admin\JudgeController::class, 'index']);
            Route::post('/judges', [\App\Http\Controllers\Api\Admin\JudgeController::class, 'store']);
            Route::get('/judges/{id}', [\App\Http\Controllers\Api\Admin\JudgeController::class, 'show'])->where('id', '[0-9]+');
            Route::put('/judges/{id}', [\App\Http\Controllers\Api\Admin\JudgeController::class, 'update'])->where('id', '[0-9]+');
            Route::delete('/judges/{id}', [\App\Http\Controllers\Api\Admin\JudgeController::class, 'destroy'])->where('id', '[0-9]+');
            Route::post('/judges/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\JudgeController::class, 'toggleStatus'])->where('id', '[0-9]+');
            Route::post('/judges/{id}/assign-competitions', [\App\Http\Controllers\Api\Admin\JudgeController::class, 'assignCompetitions'])->where('id', '[0-9]+');
            
            // Sponsors Management
            Route::get('/sponsors', [\App\Http\Controllers\Api\Admin\SponsorManagementController::class, 'index']);
            Route::post('/sponsors', [\App\Http\Controllers\Api\Admin\SponsorManagementController::class, 'store']);
            Route::get('/sponsors/{sponsor}', [\App\Http\Controllers\Api\Admin\SponsorManagementController::class, 'show']);
            Route::put('/sponsors/{sponsor}', [\App\Http\Controllers\Api\Admin\SponsorManagementController::class, 'update']);
            Route::delete('/sponsors/{sponsor}', [\App\Http\Controllers\Api\Admin\SponsorManagementController::class, 'destroy']);
            Route::post('/sponsors/{sponsor}/toggle-status', [\App\Http\Controllers\Api\Admin\SponsorManagementController::class, 'toggleStatus']);
            
            // Notifications (for header notifications)
            Route::get('/notifications', [\App\Http\Controllers\Api\Admin\NotificationController::class, 'index']);
            Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\Api\Admin\NotificationController::class, 'markAsRead']);
            Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Api\Admin\NotificationController::class, 'markAllAsRead']);
            Route::delete('/notifications/{id}', [\App\Http\Controllers\Api\Admin\NotificationController::class, 'destroy']);
            Route::delete('/notifications/delete-read', [\App\Http\Controllers\Api\Admin\NotificationController::class, 'deleteRead']);
            
            // Error Center
            Route::get('/errors', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'index']);
            Route::patch('/errors/{error}', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'updateStatus']);
            Route::get('/error-logs', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'index']);
            Route::get('/error-logs/statistics', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'statistics']);
            Route::get('/error-logs/export', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'export']);
            Route::get('/error-logs/{error}', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'show']);
            Route::post('/error-logs/{error}/block-ip', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'blockIp']);
            Route::post('/error-logs/{error}/unblock-ip', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'unblockIp']);
            Route::post('/error-logs/{error}/unlock-throttle', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'unlockThrottle']);
            Route::post('/error-logs/{error}/resolve', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'resolve']);
            Route::post('/error-logs/{error}/unresolve', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'unresolve']);
            Route::post('/error-logs/{error}/mute', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'mute']);
            Route::post('/error-logs/{error}/unmute', [\App\Http\Controllers\Api\Admin\ErrorController::class, 'unmute']);
            
            // Share Frame Templates
            Route::get('/share-frames', [\App\Http\Controllers\Api\Admin\ShareFrameController::class, 'index']);
            Route::post('/share-frames', [\App\Http\Controllers\Api\Admin\ShareFrameController::class, 'store']);
            Route::get('/share-frames/{shareFrame}', [\App\Http\Controllers\Api\Admin\ShareFrameController::class, 'show']);
            Route::put('/share-frames/{shareFrame}', [\App\Http\Controllers\Api\Admin\ShareFrameController::class, 'update']);
            Route::delete('/share-frames/{shareFrame}', [\App\Http\Controllers\Api\Admin\ShareFrameController::class, 'destroy']);
            
            // Featured Photographers Management
            Route::get('/featured-photographers', [\App\Http\Controllers\Api\Admin\FeaturedPhotographerController::class, 'index']);
            Route::post('/featured-photographers', [\App\Http\Controllers\Api\Admin\FeaturedPhotographerController::class, 'store']);
            Route::put('/featured-photographers/{featuredPhotographer}', [\App\Http\Controllers\Api\Admin\FeaturedPhotographerController::class, 'update']);
            Route::delete('/featured-photographers/{featuredPhotographer}', [\App\Http\Controllers\Api\Admin\FeaturedPhotographerController::class, 'destroy']);
            Route::patch('/featured-photographers/{featuredPhotographer}/toggle', [\App\Http\Controllers\Api\Admin\FeaturedPhotographerController::class, 'toggle']);
            Route::get('/photographers/search', [\App\Http\Controllers\Api\Admin\FeaturedPhotographerController::class, 'searchPhotographers']);
            
            // Featured Photographers Payments (Admin)
            Route::get('/featured-photographers/payments/statistics', [\App\Http\Controllers\Api\FeaturedPhotographerPaymentController::class, 'adminIndex']);
            
            // Featured Photographers Upgrades (Admin)
            Route::get('/featured-photographers/upgrades', [\App\Http\Controllers\Api\FeaturedPhotographerUpgradeController::class, 'adminIndex']);
            Route::post('/featured-photographers/upgrades/{upgrade}/verify-cash', [\App\Http\Controllers\Api\FeaturedPhotographerUpgradeController::class, 'verifyCashPayment']);
            
            // Competition Management
            Route::get('/competitions', [AdminCompetitionApiController::class, 'index']);
            Route::post('/competitions', [AdminCompetitionApiController::class, 'store']);
            Route::get('/competitions/{id}', [AdminCompetitionApiController::class, 'show']);
            Route::put('/competitions/{id}', [AdminCompetitionApiController::class, 'update']);
            Route::delete('/competitions/{id}', [AdminCompetitionApiController::class, 'destroy']);
            
            // Winner calculation routes
            Route::post('/competitions/{competition}/calculate-winners', [\App\Http\Controllers\Admin\CompetitionController::class, 'calculateWinners']);
            Route::post('/competitions/{competition}/announce-winners', [\App\Http\Controllers\Admin\CompetitionController::class, 'announceWinners']);
            Route::get('/competitions/{competition}/winners', [\App\Http\Controllers\Admin\CompetitionController::class, 'getWinners']);
            Route::get('/competitions/{competition}/leaderboard', [\App\Http\Controllers\Admin\CompetitionController::class, 'getLeaderboard']);

            // Event Management
            Route::get('/events', [AdminEventApiController::class, 'index']);
            Route::post('/events', [AdminEventApiController::class, 'store']);
            Route::get('/events/{id}', [AdminEventApiController::class, 'show']);
            Route::put('/events/{id}', [AdminEventApiController::class, 'update']);
            Route::delete('/events/{id}', [AdminEventApiController::class, 'destroy']);
            Route::post('/events/bulk-update-status', [AdminEventApiController::class, 'bulkUpdateStatus']);
            Route::post('/events/{id}/toggle-featured', [AdminEventApiController::class, 'toggleFeatured']);
            
            // Event Check-in Management
            Route::get('/events/{event}/check-in', [EventCheckInController::class, 'index']);
            Route::post('/events/{event}/check-in/scan', [EventCheckInController::class, 'scan']);
            Route::get('/events/{event}/check-in/registrations', [EventCheckInController::class, 'getRegistrations']);
            Route::get('/events/{event}/check-in/qr/{qrToken}', [EventCheckInController::class, 'getByQrToken']);
            Route::post('/events/{event}/check-in/manual', [EventCheckInController::class, 'manualCheckIn']);
            Route::post('/registrations/{registration}/check-in/undo', [EventCheckInController::class, 'undoCheckIn']);
            Route::get('/events/{event}/check-in/export', [EventCheckInController::class, 'exportCheckInReport']);
            
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
            
            // Category Management (Admin) - COMPETITION CATEGORIES ONLY
            Route::post('/competitions/{competition}/categories', [CompetitionCategoryController::class, 'store']);
            Route::put('/competitions/{competition}/categories/{category}', [CompetitionCategoryController::class, 'update']);
            Route::delete('/competitions/{competition}/categories/{category}', [CompetitionCategoryController::class, 'destroy']);
            Route::post('/competitions/{competition}/categories/bulk', [CompetitionCategoryController::class, 'bulkCreate']);
            Route::post('/competitions/{competition}/categories/{category}/toggle-active', [CompetitionCategoryController::class, 'toggleActive']);
            Route::get('/competitions/{competition}/categories/statistics', [CompetitionCategoryController::class, 'statistics']);
            
            // Platform Sponsor Management (must be before competition sponsors to avoid conflicts)
            Route::get('/platform-sponsors', [\App\Http\Controllers\Api\Admin\SponsorController::class, 'index']);
            Route::post('/platform-sponsors', [\App\Http\Controllers\Api\Admin\SponsorController::class, 'store']);
            Route::get('/platform-sponsors/{id}', [\App\Http\Controllers\Api\Admin\SponsorController::class, 'show']);
            Route::put('/platform-sponsors/{id}', [\App\Http\Controllers\Api\Admin\SponsorController::class, 'update']);
            Route::delete('/platform-sponsors/{id}', [\App\Http\Controllers\Api\Admin\SponsorController::class, 'destroy']);
            Route::post('/upload-logo', [\App\Http\Controllers\Api\Admin\SponsorController::class, 'uploadLogo']);
            
            // Contact Messages / Inquiries
            Route::get('/contact-messages', [ContactMessageController::class, 'index']);
            Route::post('/contact-messages', [ContactMessageController::class, 'store']);
            Route::get('/contact-messages/stats', [ContactMessageController::class, 'stats']);
            Route::get('/contact-messages/{id}', [ContactMessageController::class, 'show']);
            Route::put('/contact-messages/{id}', [ContactMessageController::class, 'update']);
            Route::put('/contact-messages/{id}/respond', [ContactMessageController::class, 'markAsResponded']);
            Route::put('/contact-messages/{id}/archive', [ContactMessageController::class, 'archive']);
            Route::patch('/contact-messages/{id}', [ContactMessageController::class, 'updateStatus']);
            Route::delete('/contact-messages/{id}', [ContactMessageController::class, 'destroy']);
            
            // User Approval Management
            Route::get('/pending-users', [\App\Http\Controllers\Api\Admin\UserApprovalController::class, 'index']);
            Route::get('/approval-stats', [\App\Http\Controllers\Api\Admin\UserApprovalController::class, 'stats']);
            Route::post('/users/{id}/approve', [\App\Http\Controllers\Api\Admin\UserApprovalController::class, 'approve']);
            Route::post('/users/{id}/reject', [\App\Http\Controllers\Api\Admin\UserApprovalController::class, 'reject']);
            Route::post('/users/bulk-approve', [\App\Http\Controllers\Api\Admin\UserApprovalController::class, 'bulkApprove']);
            
            // Sponsorship Management (Competition-specific)
            Route::post('/competitions/{competition}/sponsors', [CompetitionSponsorController::class, 'store']);
            Route::put('/competition-sponsors/{sponsor}', [CompetitionSponsorController::class, 'update']);
            Route::delete('/competition-sponsors/{sponsor}', [CompetitionSponsorController::class, 'destroy']);
            Route::post('/competitions/{competition}/sponsors/bulk', [CompetitionSponsorController::class, 'bulkCreate']);
            Route::post('/competition-sponsors/{sponsor}/toggle-active', [CompetitionSponsorController::class, 'toggleActive']);
            Route::post('/competitions/{competition}/sponsors/reorder', [CompetitionSponsorController::class, 'reorder']);
            Route::get('/competitions/{competition}/sponsors/statistics', [CompetitionSponsorController::class, 'statistics']);
            Route::get('/competition-sponsors/global-statistics', [CompetitionSponsorController::class, 'globalStatistics']);
            
            // Activity Logs
            Route::get('/activity-logs', [ActivityLogController::class, 'index']);
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
            
            // Mentor Management
            Route::get('/mentors', [\App\Http\Controllers\Api\Admin\MentorController::class, 'index']);
            Route::post('/mentors', [\App\Http\Controllers\Api\Admin\MentorController::class, 'store']);
            Route::get('/mentors/{id}', [\App\Http\Controllers\Api\Admin\MentorController::class, 'show'])->where('id', '[0-9]+');
            Route::put('/mentors/{id}', [\App\Http\Controllers\Api\Admin\MentorController::class, 'update'])->where('id', '[0-9]+');
            Route::delete('/mentors/{id}', [\App\Http\Controllers\Api\Admin\MentorController::class, 'destroy'])->where('id', '[0-9]+');
            Route::post('/mentors/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\MentorController::class, 'toggleStatus'])->where('id', '[0-9]+');
            Route::post('/mentors/reorder', [\App\Http\Controllers\Api\Admin\MentorController::class, 'updateOrder']);
            
            
            // Certificate Templates (P0-007)
            Route::get('/certificate-templates', [CertificateTemplateController::class, 'index']);
            Route::post('/certificate-templates', [CertificateTemplateController::class, 'store']);
            Route::get('/certificate-templates/{id}', [CertificateTemplateController::class, 'show']);
            Route::put('/certificate-templates/{id}', [CertificateTemplateController::class, 'update']);
            Route::delete('/certificate-templates/{id}', [CertificateTemplateController::class, 'destroy']);
            Route::get('/certificate-templates/type/{type}/default', [CertificateTemplateController::class, 'getDefault']);
            
            // Notice Management
            Route::get('/notices', [\App\Http\Controllers\Api\Admin\NoticeController::class, 'index']);
            Route::post('/notices', [\App\Http\Controllers\Api\Admin\NoticeController::class, 'store']);
            Route::get('/notices/{id}', [\App\Http\Controllers\Api\Admin\NoticeController::class, 'show']);
            Route::put('/notices/{id}', [\App\Http\Controllers\Api\Admin\NoticeController::class, 'update']);
            Route::delete('/notices/{id}', [\App\Http\Controllers\Api\Admin\NoticeController::class, 'destroy']);
            Route::get('/notices/roles/available', [\App\Http\Controllers\Api\Admin\NoticeController::class, 'getRoles']);
            
            // SEO Meta Management
            Route::get('/seo/all', [\App\Http\Controllers\Api\Admin\SeoMetaController::class, 'index']);
            Route::get('/seo', [\App\Http\Controllers\Api\Admin\SeoMetaController::class, 'show']);
            Route::post('/seo', [\App\Http\Controllers\Api\Admin\SeoMetaController::class, 'store']);
            Route::post('/seo/generate', [\App\Http\Controllers\Api\Admin\SeoMetaController::class, 'generate']);
            Route::post('/seo/preview', [\App\Http\Controllers\Api\Admin\SeoMetaController::class, 'preview']);
            Route::delete('/seo', [\App\Http\Controllers\Api\Admin\SeoMetaController::class, 'destroy']);
            
            // City Management
            Route::get('/locations', [CityController::class, 'adminIndex']);
            Route::post('/locations', [CityController::class, 'store']);
            Route::get('/locations/{id}', [CityController::class, 'show']);
            Route::put('/locations/{id}', [CityController::class, 'update']);
            Route::delete('/locations/{id}', [CityController::class, 'destroy']);

            // Legacy cities endpoints (alias to locations)
            Route::get('/cities', [CityController::class, 'adminIndex']);
            Route::post('/cities', [CityController::class, 'store']);
            Route::get('/cities/{id}', [CityController::class, 'show']);
            Route::put('/cities/{id}', [CityController::class, 'update']);
            Route::delete('/cities/{id}', [CityController::class, 'destroy']);
            
            // Category Management
            Route::get('/categories', [CategoryController::class, 'adminIndex']);
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::get('/categories/{id}', [CategoryController::class, 'show']);
            Route::put('/categories/{id}', [CategoryController::class, 'update']);
            Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
            
            // Photographer Management
            Route::get('/photographers', [AdminController::class, 'getPhotographers']);
            Route::post('/photographers', [AdminController::class, 'storePhotographer']);
            Route::get('/photographers/{id}', [AdminController::class, 'showPhotographer']);
            Route::put('/photographers/{id}', [AdminController::class, 'updatePhotographer']);
            Route::delete('/photographers/{id}', [AdminController::class, 'deletePhotographer']);
            Route::post('/photographers/{id}/verify', [AdminController::class, 'verifyPhotographer']);
            Route::post('/photographers/{id}/feature', [AdminController::class, 'featurePhotographer']);
            Route::post('/photographers/{photographer}/onboarding/reset', [\App\Http\Controllers\Api\PhotographerOnboardingController::class, 'resetChecklist']);
            Route::get('/photographers/onboarding/pending', [\App\Http\Controllers\Api\PhotographerOnboardingController::class, 'getPendingOnboardings']);
            
            // Review Management
            Route::get('/reviews', [AdminReviewController::class, 'index']);
            Route::get('/reviews/stats', [AdminReviewController::class, 'stats']);
            Route::put('/reviews/{id}/status', [AdminReviewController::class, 'updateStatus']);
            Route::post('/reviews/{id}/report', [AdminReviewController::class, 'markAsReported']);
            Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy']);
            Route::post('/reviews/bulk-update', [AdminReviewController::class, 'bulkUpdateStatus']);
            
            // Booking Management
            Route::get('/bookings', [AdminBookingController::class, 'index']);
            Route::get('/bookings/stats', [AdminBookingController::class, 'stats']);
            Route::get('/bookings/{id}', [AdminBookingController::class, 'show']);
            Route::put('/bookings/{id}/status', [AdminBookingController::class, 'updateStatus']);
            Route::delete('/bookings/{id}', [AdminBookingController::class, 'destroy']);
            
            // Transaction Management
            Route::get('/transactions', [AdminTransactionController::class, 'index']);
            Route::get('/transactions/stats', [AdminTransactionController::class, 'stats']);
            Route::get('/transactions/{id}', [AdminTransactionController::class, 'show']);
            Route::put('/transactions/{id}/status', [AdminTransactionController::class, 'updateStatus']);
            Route::post('/transactions/{id}/refund', [AdminTransactionController::class, 'refund']);
            Route::get('/transactions/export', [AdminTransactionController::class, 'export']);
            
            // Settings Management
            Route::get('/settings', [AdminSettingsController::class, 'index']);
            Route::put('/settings/{key}', [AdminSettingsController::class, 'update']);
            Route::post('/settings/bulk', [AdminSettingsController::class, 'bulkUpdate']);
            Route::get('/settings/category/{category}', [AdminSettingsController::class, 'getCategory']);
            Route::post('/settings/reset', [AdminSettingsController::class, 'reset']);
            
            // Error Center - System Error Tracking & Management
            Route::prefix('error-logs')->group(function () {
                Route::get('/', [ErrorCenterController::class, 'index']);
                Route::get('/statistics', [ErrorCenterController::class, 'statistics']);
                Route::get('{id}', [ErrorCenterController::class, 'show'])->middleware('role:super_admin');
                Route::post('{id}/resolve', [ErrorCenterController::class, 'markResolved']);
                Route::post('{id}/unresolve', [ErrorCenterController::class, 'markUnresolved']);
                Route::post('{id}/mute', [ErrorCenterController::class, 'mute']);
                Route::post('{id}/unmute', [ErrorCenterController::class, 'unmute']);
                Route::post('clear-resolved', [ErrorCenterController::class, 'clearResolved'])->middleware('role:super_admin');
                Route::get('export', [ErrorCenterController::class, 'export']);
            });
        });

        // User Notices (for any authenticated user)
        Route::get('/notices/my-notices', [\App\Http\Controllers\Api\Admin\NoticeController::class, 'getMyNotices']);
        Route::post('/notices/{id}/read', [\App\Http\Controllers\Api\Admin\NoticeController::class, 'markAsRead']);
    });
});

