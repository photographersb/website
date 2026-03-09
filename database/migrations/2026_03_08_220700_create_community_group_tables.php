<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 140);
            $table->string('slug', 170)->unique();
            $table->text('description');
            $table->string('cover_image_url')->nullable();
            $table->enum('type', ['interest', 'local_club'])->default('interest');
            $table->foreignId('city_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('members_count')->default(0);
            $table->unsignedInteger('posts_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['active', 'private', 'archived'])->default('active');
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['city_id', 'status']);
            $table->index(['is_featured', 'status']);
        });

        Schema::create('community_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('community_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['admin', 'moderator', 'member'])->default('member');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['group_id', 'user_id']);
            $table->index(['user_id', 'role']);
        });

        Schema::create('community_group_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('community_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->string('image_url')->nullable();
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['active', 'hidden', 'spam'])->default('active');
            $table->timestamps();

            $table->index(['group_id', 'status']);
            $table->index(['is_featured', 'status']);
        });

        Schema::create('community_group_post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_group_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->enum('status', ['active', 'hidden', 'spam'])->default('active');
            $table->timestamps();

            $table->index(['post_id', 'status']);
        });

        Schema::create('community_group_post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_group_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['post_id', 'user_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_group_post_likes');
        Schema::dropIfExists('community_group_post_comments');
        Schema::dropIfExists('community_group_posts');
        Schema::dropIfExists('community_group_members');
        Schema::dropIfExists('community_groups');
    }
};
