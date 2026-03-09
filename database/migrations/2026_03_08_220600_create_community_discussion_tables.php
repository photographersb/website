<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 180);
            $table->longText('content');
            $table->string('category', 80);
            $table->json('tags')->nullable();
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->unsignedInteger('shares_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['active', 'hidden', 'locked', 'spam'])->default('active');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'is_featured']);
            $table->index(['category', 'status']);
            $table->index('last_activity_at');
        });

        Schema::create('community_discussion_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained('community_discussions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('community_discussion_comments')->nullOnDelete();
            $table->text('content');
            $table->enum('status', ['active', 'hidden', 'spam'])->default('active');
            $table->timestamps();

            $table->index(['discussion_id', 'status']);
            $table->index('user_id');
        });

        Schema::create('community_discussion_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained('community_discussions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['discussion_id', 'user_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_discussion_likes');
        Schema::dropIfExists('community_discussion_comments');
        Schema::dropIfExists('community_discussions');
    }
};
