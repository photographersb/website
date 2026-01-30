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

// Admin Routes are handled by Vue Router + API endpoints
// See routes/api.php for admin API endpoints (/api/v1/admin/*)
// Authentication is handled by Vue Router navigation guards in resources/js/app.js

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');

require __DIR__.'/auth.php';
