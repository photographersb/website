<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('milestone');
            $table->string('status', 20)->default('in_progress');
            $table->unsignedInteger('referred_photographers_count')->default(0);
            $table->string('badge_name')->nullable();
            $table->timestamp('achieved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'milestone']);
            $table->index(['status', 'milestone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_rewards');
    }
};
