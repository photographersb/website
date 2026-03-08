<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Admin\CompetitionController as AdminCompetitionController;

// Authentication Routes
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return inertia('Login');
})->name('login');
Route::get('/register', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return inertia('Register');
})->name('register');
Route::get('/forgot-password', fn() => inertia('ForgotPassword'))->name('password.request');

// Email Verification Route
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    try {
        $user = \App\Models\User::findOrFail($id);
        
        // Verify hash
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            Log::warning("Email verification failed: Invalid hash for user {$id}");
            return redirect('/auth?error=invalid_verification_link');
        }
        
        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            Log::info("Email verification: User {$id} already verified");
            return redirect('/auth?verified=already');
        }
        
        // Mark as verified
        $user->markEmailAsVerified();
        Log::info("Email verified successfully for user {$id} ({$user->email})");
        
        return redirect('/auth?verified=success');
    } catch (\Exception $e) {
        Log::error("Email verification error: " . $e->getMessage());
        return redirect('/auth?error=verification_failed');
    }
})->name('verification.verify');

Route::get('/403', function () {
    return response()->view('errors.403', [], 403);
})->name('forbidden');

// Payment Gateway Callbacks
Route::get('/payment/callback/success', [PaymentController::class, 'successCallback'])->name('payment.success');
Route::post('/payment/callback/success', [PaymentController::class, 'successCallback']);

Route::get('/payment/callback/fail', [PaymentController::class, 'failCallback'])->name('payment.fail');
Route::post('/payment/callback/fail', [PaymentController::class, 'failCallback']);

Route::get('/payment/callback/cancel', [PaymentController::class, 'cancelCallback'])->name('payment.cancel');
Route::post('/payment/callback/cancel', [PaymentController::class, 'cancelCallback']);

// Sitemap routes
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index']);
Route::get('/sitemap/main.xml', [\App\Http\Controllers\SitemapController::class, 'main']);
Route::get('/sitemap/photographers.xml', [\App\Http\Controllers\SitemapController::class, 'photographers']);
Route::get('/sitemap/events.xml', [\App\Http\Controllers\SitemapController::class, 'events']);
Route::get('/sitemap/competitions.xml', [\App\Http\Controllers\SitemapController::class, 'competitions']);
Route::get('/sitemap/cities.xml', [\App\Http\Controllers\SitemapController::class, 'cities']);
Route::get('/sitemap/categories.xml', [\App\Http\Controllers\SitemapController::class, 'categories']);

// Public Photographer Profile Routes (SPA)
Route::get('/@{username}', function () {
    return view('app');
})->name('photographer.profile.public');
Route::get('/photographer/{id}', function () {
    return view('app');
})
    ->whereNumber('id')
    ->name('photographer.profile.legacy');

// Public API Routes for Photographer Profiles
Route::prefix('api/photographers')->group(function () {
    Route::get('/@{username}/portfolio', [\App\Http\Controllers\PublicPhotographerController::class, 'getPortfolio'])->name('photographer.portfolio.api');
    Route::get('/@{username}/packages', [\App\Http\Controllers\PublicPhotographerController::class, 'getPackages'])->name('photographer.packages.api');
    Route::get('/@{username}/reviews', [\App\Http\Controllers\PublicPhotographerController::class, 'getReviews'])->name('photographer.reviews.api');
    Route::get('/search', [\App\Http\Controllers\PublicPhotographerController::class, 'search'])->name('photographer.search.api');
});

// Admin Access Gate - Shows landing page or redirects to dashboard
Route::get('/admin', [\App\Http\Controllers\Admin\AdminAccessController::class, 'index'])
    ->name('admin.gate')
    ->middleware('throttle:30,1');

// Admin Routes are handled by Vue Router + API endpoints
// See routes/api.php for admin API endpoints (/api/v1/admin/*)

// Admin Management Pages (SPA Routes)
Route::prefix('admin')->group(function () {
    // Keep login route public for unauthenticated admins.
    Route::get('/login', function () { return view('admin.spa'); })->name('admin.login');
});

Route::prefix('admin')->middleware(['auth', 'role:admin,super_admin,moderator'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () { return view('admin.spa'); })->name('admin.dashboard');
    Route::get('/analytics', function () { return view('admin.spa'); })->name('admin.analytics');
    Route::get('/system-health', function () { return view('admin.spa'); })->name('admin.system-health');

    // Profile & Account
    Route::get('/profile', function () { return view('admin.spa'); })->name('admin.profile');
    Route::get('/settings/account', function () { return view('admin.spa'); })->name('admin.settings.account');
    
    // Management Pages
    Route::get('/judges', function () { return view('admin.spa'); })->name('admin.judges');
    Route::get('/sponsors', function () { return view('admin.spa'); })->name('admin.sponsors');
    Route::get('/reviews', function () { return view('admin.spa'); })->name('admin.reviews');
    Route::get('/bookings', function () { return view('admin.spa'); })->name('admin.bookings');
    Route::get('/transactions', function () { return view('admin.spa'); })->name('admin.transactions');
    Route::get('/activity-logs', function () { return view('admin.spa'); })->name('admin.activity-logs');
    Route::get('/hashtags', function () { return view('admin.spa'); })->name('admin.hashtags');
    Route::get('/featured-photographers', function () { return view('admin.spa'); })->name('admin.featured-photographers');
    
    // Additional Pages
    Route::get('/notifications', function () { return view('admin.spa'); })->name('admin.notifications');
    Route::get('/error-center', function () { return view('admin.spa'); })->name('admin.error-center');
    Route::get('/share-frames', function () { return view('admin.spa'); })->name('admin.share-frames');
    Route::get('/seo', function () { return view('admin.spa'); })->name('admin.seo');
});

// Admin Sitemap (Web-based UI)
Route::prefix('admin')->middleware(['auth', 'role:admin,super_admin,moderator'])->group(function () {
    Route::get('/system-health/sitemap', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'index'])->name('admin.sitemap.index');
    Route::post('/system-health/sitemap/run-test', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'runTest'])->name('admin.sitemap.run-test');
    Route::get('/system-health/sitemap/checks', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'getChecks'])->name('admin.sitemap.checks');
    Route::get('/system-health/sitemap/checks/{check}', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'show'])->name('admin.sitemap.show');
    Route::get('/system-health/sitemap/checks/{check}/export', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'export'])->name('admin.sitemap.export');
    Route::delete('/system-health/sitemap/checks/{check}', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'destroy'])->name('admin.sitemap.destroy');
    
    // Dev Tools (Super Admin Only, Non-Production Only)
    Route::prefix('dev')->name('admin.dev.')->middleware('role:super_admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DevToolsController::class, 'index'])->name('index');
        Route::post('/clear-cache', [\App\Http\Controllers\Admin\DevToolsController::class, 'clearCache'])->name('clear-cache');
        Route::post('/clear-view-cache', [\App\Http\Controllers\Admin\DevToolsController::class, 'clearViewCache'])->name('clear-view-cache');
        Route::post('/clear-config-cache', [\App\Http\Controllers\Admin\DevToolsController::class, 'clearConfigCache'])->name('clear-config-cache');
        Route::post('/clear-route-cache', [\App\Http\Controllers\Admin\DevToolsController::class, 'clearRouteCache'])->name('clear-route-cache');
        Route::post('/assets-info', [\App\Http\Controllers\Admin\DevToolsController::class, 'assetsInfo'])->name('assets-info');
    });

    // Site Links Management (Admin Settings)
    Route::prefix('settings/site-links')->name('admin.site-links.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SiteLinkController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\SiteLinkController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\SiteLinkController::class, 'store'])->name('store');
        Route::get('/{siteLink}/edit', [\App\Http\Controllers\Admin\SiteLinkController::class, 'edit'])->name('edit');
        Route::put('/{siteLink}', [\App\Http\Controllers\Admin\SiteLinkController::class, 'update'])->name('update');
        Route::delete('/{siteLink}', [\App\Http\Controllers\Admin\SiteLinkController::class, 'destroy'])->name('destroy');
        Route::post('/{siteLink}/toggle-active', [\App\Http\Controllers\Admin\SiteLinkController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/update-sort-orders', [\App\Http\Controllers\Admin\SiteLinkController::class, 'updateSortOrders'])->name('update-sort-orders');
        Route::post('/clear-cache', [\App\Http\Controllers\Admin\SiteLinkController::class, 'clearCache'])->name('clear-cache');
        Route::get('/preview', [\App\Http\Controllers\Admin\SiteLinkController::class, 'preview'])->name('preview');
    });

    // Share Frame Template Management (Admin Only)
    Route::prefix('competitions/{competition}/share-frame-template')->name('admin.competitions.share-frame-template.')->group(function () {
        Route::get('/edit', [\App\Http\Controllers\Admin\CompetitionShareFrameTemplateController::class, 'edit'])->name('edit');
        Route::put('/', [\App\Http\Controllers\Admin\CompetitionShareFrameTemplateController::class, 'update'])->name('update');
        Route::post('/preview', [\App\Http\Controllers\Admin\CompetitionShareFrameTemplateController::class, 'preview'])->name('preview');
        Route::delete('/', [\App\Http\Controllers\Admin\CompetitionShareFrameTemplateController::class, 'destroy'])->name('destroy');
    });

    // Certificate Management (Admin Only)
    Route::prefix('certificates')->name('admin.certificates.')->group(function () {
        Route::resource('templates', \App\Http\Controllers\Admin\CertificateTemplateController::class);
        Route::get('/', [\App\Http\Controllers\Admin\CertificateController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\CertificateController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\CertificateController::class, 'store'])->name('store');
        Route::get('/{certificate}', [\App\Http\Controllers\Admin\CertificateController::class, 'show'])->name('show');
        Route::post('/auto-issue', [\App\Http\Controllers\Admin\CertificateController::class, 'autoIssueForEvent'])->name('auto-issue');
        Route::post('/{certificate}/generate', [\App\Http\Controllers\Admin\CertificateController::class, 'generate'])->name('generate');
        Route::post('/{certificate}/download', [\App\Http\Controllers\Admin\CertificateController::class, 'download'])->name('download');
        Route::post('/{certificate}/revoke', [\App\Http\Controllers\Admin\CertificateController::class, 'revoke'])->name('revoke');
        Route::post('/{certificate}/reissue', [\App\Http\Controllers\Admin\CertificateController::class, 'reissue'])->name('reissue');
    });

    // Event Attendance QR Scanning (shared with Vue interface)
    Route::prefix('events/{event}/attendance')->name('admin.events.attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EventAttendanceController::class, 'index'])->name('index');
        Route::get('/mobile', [\App\Http\Controllers\Admin\EventAttendanceController::class, 'mobile'])->name('mobile');
        Route::post('/scan', [\App\Http\Controllers\Admin\EventAttendanceController::class, 'scan'])->name('scan');
        Route::get('/report', [\App\Http\Controllers\Admin\EventAttendanceController::class, 'report'])->name('report');
        Route::post('/export', [\App\Http\Controllers\Admin\EventAttendanceController::class, 'export'])->name('export');
    });

    // Event Certificates
    Route::get('/events/{event}/certificates', [\App\Http\Controllers\Admin\CertificateController::class, 'byEvent'])
        ->name('admin.events.certificates');
    Route::post('/events/{event}/certificates/regenerate-bulk', [\App\Http\Controllers\Admin\CertificateController::class, 'regenerateBulk'])
        ->name('admin.events.certificates.regenerate-bulk');
});

// Admin SPA fallback (keeps deep links on refresh)
Route::get('/admin/{any}', function () {
    return view('admin.spa');
})->where('any', '.*')
    ->middleware(['auth', 'role:admin,super_admin,moderator']);

// SEO Landing Pages: Categories & Locations
Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{slug}', [\App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');

Route::get('/locations', [\App\Http\Controllers\LocationController::class, 'index'])->name('locations.index');
Route::get('/locations/{slug}', [\App\Http\Controllers\LocationController::class, 'show'])->name('locations.show');

// Photographer Search & Filtering (High SEO Value)
Route::prefix('photographers')->name('photographers.')->group(function () {
    Route::get('/{locationSlug}/{categorySlug}', [\App\Http\Controllers\PhotographerSearchController::class, 'byLocationAndCategory'])->name('search.location-category');
    Route::get('/location/{slug}', [\App\Http\Controllers\PhotographerSearchController::class, 'byLocation'])->name('search.location');
    Route::get('/category/{slug}', [\App\Http\Controllers\PhotographerSearchController::class, 'byCategory'])->name('search.category');
});

// Public Event Routes
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [\App\Http\Controllers\EventListingController::class, 'index'])->name('index');
    Route::get('/featured', [\App\Http\Controllers\EventListingController::class, 'featured'])->name('featured');
    Route::get('/{event:slug}', [\App\Http\Controllers\EventController::class, 'show'])->name('show');
    Route::post('/{event}/register', [\App\Http\Controllers\EventController::class, 'register'])->name('register')->middleware('auth');
});

// User Registration Dashboard (Authenticated)
Route::middleware('auth')->get('/my-registrations', [\App\Http\Controllers\EventListingController::class, 'myRegistrations'])->name('events.my-registrations');

// Event Registration & Payment Routes (Authenticated)
Route::middleware('auth')->prefix('registrations')->name('registrations.')->group(function () {
    Route::get('/{registration}/payment', [\App\Http\Controllers\EventController::class, 'payment'])->name('payment');
    Route::post('/{registration}/payment/callback', [\App\Http\Controllers\EventController::class, 'paymentCallback'])->name('payment.callback');
    Route::get('/{registration}/confirmation', [\App\Http\Controllers\EventController::class, 'confirmation'])->name('confirmation');
    Route::get('/{registration}/ticket', [\App\Http\Controllers\EventController::class, 'downloadTicket'])->name('ticket');
});

// Event Payment Routes (Public - for webhook callbacks)
Route::post('/events/payment/webhook/sslcommerz', [\App\Http\Controllers\EventPaymentController::class, 'sslcommerzWebhook'])->name('events.payment.webhook.sslcommerz');


// Share Frame Routes (Public)
Route::prefix('competitions/{competition}/submissions/{submission}/share-frame')->name('competitions.submissions.share-frame.')->group(function () {
    Route::get('/', [\App\Http\Controllers\SubmissionShareFrameController::class, 'show'])->name('show');
    Route::post('/generate', [\App\Http\Controllers\SubmissionShareFrameController::class, 'generate'])->name('generate');
    Route::post('/regenerate', [\App\Http\Controllers\SubmissionShareFrameController::class, 'regenerate'])->name('regenerate');
    Route::get('/download/{format}', [\App\Http\Controllers\SubmissionShareFrameController::class, 'download'])->name('download');
});

// Short URL Vote Redirect
Route::get('/vote/{shortUrl}', [\App\Http\Controllers\SubmissionShareFrameController::class, 'voteRedirect'])->name('vote.redirect');

// Certificate Verification (Public)
Route::get('/certificate/verify/{certificateCode}', [\App\Http\Controllers\CertificateVerificationController::class, 'verify'])->name('certificate.verify');
Route::get('/certificate/{certificateCode}/qr', [\App\Http\Controllers\CertificateVerificationController::class, 'downloadQR'])->name('certificate.qr');

// Booking Marketplace Routes
Route::middleware('auth')->group(function () {
    // Client - Request booking from photographer
    Route::get('/@{photographerUsername}/book', [\App\Http\Controllers\BookingRequestController::class, 'create'])->name('booking.create');
    Route::post('/bookings', [\App\Http\Controllers\BookingRequestController::class, 'store'])->name('booking.store');
    
    // Booking Details & Actions
    Route::get('/bookings/{booking}', [\App\Http\Controllers\BookingRequestController::class, 'show'])->name('booking.show');
    Route::post('/bookings/{booking}/accept', [\App\Http\Controllers\BookingRequestController::class, 'accept'])->name('booking.accept');
    Route::post('/bookings/{booking}/decline', [\App\Http\Controllers\BookingRequestController::class, 'decline'])->name('booking.decline');
    Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\BookingRequestController::class, 'cancel'])->name('booking.cancel');
    Route::post('/bookings/{booking}/complete', [\App\Http\Controllers\BookingRequestController::class, 'complete'])->name('booking.complete');
    
    // Booking Lists (Client & Photographer)
    Route::get('/my-bookings/client', [\App\Http\Controllers\BookingRequestController::class, 'clientBookings'])->name('booking.client.list');
    Route::get('/my-bookings/photographer', [\App\Http\Controllers\BookingRequestController::class, 'photographerBookings'])->name('booking.photographer.list');
    
    // Messaging
    Route::post('/bookings/{booking}/messages', [\App\Http\Controllers\BookingMessageController::class, 'store'])->name('booking.message.store');
    Route::delete('/messages/{message}', [\App\Http\Controllers\BookingMessageController::class, 'delete'])->name('booking.message.delete');
    Route::post('/bookings/{booking}/messages/read', [\App\Http\Controllers\BookingMessageController::class, 'markAsRead'])->name('booking.messages.read.web');
});

// Admin Booking Management Routes
Route::middleware(['auth', 'role:super_admin'])->prefix('admin/bookings')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('admin.booking.index');
    Route::get('/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('admin.booking.show');
    Route::post('/{booking}/cancel', [\App\Http\Controllers\Admin\BookingController::class, 'cancel'])->name('admin.booking.cancel');
    Route::post('/{booking}/dispute', [\App\Http\Controllers\Admin\BookingController::class, 'dispute'])->name('admin.booking.dispute');
    Route::get('/statistics/get', [\App\Http\Controllers\Admin\BookingController::class, 'statistics'])->name('admin.booking.statistics');
});



// Photographer Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/verification', [\App\Http\Controllers\VerificationController::class, 'index'])->name('verification.index');
    Route::get('/verification/create', [\App\Http\Controllers\VerificationController::class, 'create'])->name('verification.create');
    Route::post('/verification', [\App\Http\Controllers\VerificationController::class, 'store'])->name('verification.store');
    Route::get('/verification/{verificationRequest}', [\App\Http\Controllers\VerificationController::class, 'show'])->name('verification.show');
    Route::get('/verification/{verificationRequest}/download/{type}', [\App\Http\Controllers\VerificationController::class, 'downloadDocument'])->name('verification.download');
});

// Admin Verification Routes
Route::middleware(['auth'])->prefix('admin/verifications')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\VerificationController::class, 'index'])->name('admin.verifications.index');
    Route::get('/{verificationRequest}', [\App\Http\Controllers\Admin\VerificationController::class, 'show'])->name('admin.verifications.show');
    Route::post('/{verificationRequest}/approve', [\App\Http\Controllers\Admin\VerificationController::class, 'approve'])->name('admin.verifications.approve');
    Route::post('/{verificationRequest}/reject', [\App\Http\Controllers\Admin\VerificationController::class, 'reject'])->name('admin.verifications.reject');
    Route::get('/{verificationRequest}/download/{type}', [\App\Http\Controllers\Admin\VerificationController::class, 'downloadDocument'])->name('admin.verifications.download');
    Route::get('/statistics/get', [\App\Http\Controllers\Admin\VerificationController::class, 'statistics'])->name('admin.verifications.statistics');
});
// Test Error Routes (for Error Center testing)
Route::get('/test-error', function () {
    throw new \Exception('🧪 Test Error: This is a P4 test exception for Error Center');
});

Route::get('/test-error-critical', function () {
    throw new \PDOException('🔴 Critical P0 Error: Database connection failed');
});

Route::get('/test-error-query', function () {
    throw new \Illuminate\Database\QueryException(
        'mysql',
        'SELECT * FROM non_existent_table',
        [],
        new \Exception('Table not found')
    );
});

// Authentication is handled by Vue Router navigation guards in resources/js/app.js

// Catch-all for SPA - but exclude API routes
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api).*');

require __DIR__.'/auth.php';


