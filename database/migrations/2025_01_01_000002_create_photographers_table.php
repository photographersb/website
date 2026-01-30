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
        Schema::create('photographers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->integer('experience_years')->default(0);
            $table->json('specializations')->nullable();
            $table->decimal('service_area_radius', 8, 2)->default(50); // in kilometers
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->enum('verification_type', ['none', 'phone', 'email', 'id', 'phone_email', 'phone_email_id'])->default('none');
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_until')->nullable();
            $table->integer('profile_completeness')->default(0);
            $table->integer('total_bookings')->default(0);
            $table->integer('completed_bookings')->default(0);
            $table->decimal('response_time_avg', 5, 2)->nullable(); // in hours
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_verified', 'is_featured', 'average_rating']);
            $table->index(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photographers');
    }
};
