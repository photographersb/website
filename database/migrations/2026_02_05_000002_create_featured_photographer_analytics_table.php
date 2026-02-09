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
        Schema::create('featured_photographer_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_photographer_id')->constrained('featured_photographers')->onDelete('cascade');
            $table->date('date');
            $table->integer('views')->default(0);
            $table->integer('profile_clicks')->default(0);
            $table->integer('portfolio_clicks')->default(0);
            $table->integer('inquiry_messages')->default(0);
            $table->integer('booking_requests')->default(0);
            $table->float('conversion_rate')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('featured_photographer_id', 'fp_analytics_fp_id_idx');
            $table->index('date', 'fp_analytics_date_idx');
            $table->unique(['featured_photographer_id', 'date'], 'fp_analytics_fp_id_date_unique');
            $table->index(['featured_photographer_id', 'date'], 'fp_analytics_fp_id_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_photographer_analytics');
    }
};
