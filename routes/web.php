<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Admin\CompetitionController as AdminCompetitionController;

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

// Public Photographer Profile Routes (SEO-friendly)
Route::get('/@{username}', [\App\Http\Controllers\PublicPhotographerController::class, 'showByUsername'])->name('photographer.profile.public');
Route::get('/photographer/{id}', [\App\Http\Controllers\PublicPhotographerController::class, 'showById'])
    ->whereNumber('id')
    ->name('photographer.profile.legacy');

// Public API Routes for Photographer Profiles
Route::prefix('api/photographers')->group(function () {
    Route::get('/@{username}/portfolio', [\App\Http\Controllers\PublicPhotographerController::class, 'getPortfolio'])->name('photographer.portfolio.api');
    Route::get('/@{username}/packages', [\App\Http\Controllers\PublicPhotographerController::class, 'getPackages'])->name('photographer.packages.api');
    Route::get('/@{username}/reviews', [\App\Http\Controllers\PublicPhotographerController::class, 'getReviews'])->name('photographer.reviews.api');
    Route::get('/search', [\App\Http\Controllers\PublicPhotographerController::class, 'search'])->name('photographer.search.api');
});

// Admin Routes are handled by Vue Router + API endpoints
// See routes/api.php for admin API endpoints (/api/v1/admin/*)

// Admin Sitemap (Web-based UI)
Route::prefix('admin')->group(function () {
    Route::get('/sitemap', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'index'])->name('admin.sitemap');
    Route::post('/sitemap/test', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'startTest'])->name('admin.sitemap.test');
    Route::get('/sitemap/checks/{check}', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'viewCheck'])->name('admin.sitemap.check');
    Route::get('/sitemap/checks/{check}/stats', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'checkStats'])->name('admin.sitemap.stats');
    Route::get('/sitemap/checks/{check}/export', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'exportCsv'])->name('admin.sitemap.export');
    Route::delete('/sitemap/checks/{check}', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'deleteCheck'])->name('admin.sitemap.delete');
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

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');

require __DIR__.'/auth.php';
