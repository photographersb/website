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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->enum('tier', ['free', 'premium', 'pro', 'enterprise'])->unique();
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('billing_period', ['monthly', 'yearly'])->default('monthly');
            $table->integer('discount_yearly')->default(0); // percentage
            $table->json('features')->nullable(); // Array of features
            $table->integer('max_photos')->nullable();
            $table->integer('max_videos')->nullable();
            $table->integer('max_albums')->nullable();
            $table->integer('featured_listings_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['tier', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
