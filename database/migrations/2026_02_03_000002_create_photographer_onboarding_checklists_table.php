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
        Schema::create('photographer_onboarding_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photographer_id')->constrained('photographers')->onDelete('cascade');
            
            // Onboarding steps
            $table->boolean('profile_completed')->default(false);
            $table->boolean('profile_photo_uploaded')->default(false);
            $table->boolean('portfolio_added')->default(false);
            $table->boolean('phone_verified')->default(false);
            $table->boolean('city_added')->default(false);
            $table->boolean('years_of_experience_added')->default(false);
            $table->boolean('hourly_rate_set')->default(false);
            $table->boolean('bio_added')->default(false);
            $table->boolean('social_media_added')->default(false);
            $table->boolean('terms_accepted')->default(false);
            
            $table->timestamp('completed_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();

            $table->index('photographer_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photographer_onboarding_checklists');
    }
};
