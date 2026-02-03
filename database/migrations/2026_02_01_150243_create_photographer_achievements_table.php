<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // first_booking, ten_bookings, etc.
            $table->string('name'); // "First Booking"
            $table->text('description');
            $table->string('icon')->nullable(); // emoji or icon class
            $table->string('badge_color')->default('gray'); // gray, bronze, silver, gold, platinum
            $table->integer('required_count')->default(1); // threshold to unlock
            $table->integer('points')->default(10); // reward points
            $table->string('category')->default('bookings'); // bookings, reviews, portfolio, competitions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('photographer_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photographer_id')->constrained('photographers')->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained('achievements')->onDelete('cascade');
            $table->integer('progress')->default(0); // current progress
            $table->boolean('is_unlocked')->default(false);
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();

            $table->unique(['photographer_id', 'achievement_id']);
            $table->index(['photographer_id', 'is_unlocked']);
        });

        Schema::create('photographer_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photographer_id')->unique()->constrained('photographers')->onDelete('cascade');
            $table->integer('profile_views')->default(0);
            $table->integer('profile_views_this_month')->default(0);
            $table->integer('total_points')->default(0);
            $table->integer('level')->default(1);
            $table->decimal('conversion_rate', 5, 2)->default(0); // % of views that become bookings
            $table->integer('response_rate')->default(100); // % of inquiries responded to
            $table->integer('average_response_time')->default(0); // minutes
            $table->integer('repeat_client_rate')->default(0); // % of repeat clients
            $table->integer('portfolio_completeness')->default(0); // 0-100
            $table->json('monthly_earnings')->nullable(); // earnings by month
            $table->json('growth_metrics')->nullable(); // various growth indicators
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photographer_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('photographer_stats');
    }
};
