<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('community_mentorship_profiles')) {
            Schema::create('community_mentorship_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->json('expertise')->nullable();
                $table->unsignedTinyInteger('years_experience')->default(0);
                $table->enum('availability_status', ['available', 'limited', 'unavailable'])->default('available');
                $table->json('session_types')->nullable();
                $table->text('bio')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'availability_status'], 'cmp_active_availability_idx');
            });
        }

        if (!Schema::hasTable('community_mentorship_requests')) {
            Schema::create('community_mentorship_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mentor_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('requester_user_id')->constrained('users')->cascadeOnDelete();
                $table->string('preferred_session_type', 60)->nullable();
                $table->text('message');
                $table->enum('status', ['pending', 'accepted', 'declined', 'completed', 'cancelled'])->default('pending');
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamps();

                $table->index(['mentor_user_id', 'status'], 'cmr_mentor_status_idx');
                $table->index(['requester_user_id', 'status'], 'cmr_requester_status_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('community_mentorship_requests');
        Schema::dropIfExists('community_mentorship_profiles');
    }
};
