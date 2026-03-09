<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_courses', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 180)->unique();
            $table->string('title', 180);
            $table->text('description');
            $table->string('category', 100);
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->string('cover_image_url')->nullable();
            $table->foreignId('instructor_user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->unsignedInteger('lessons_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'is_featured'], 'lc_status_featured_idx');
            $table->index(['category', 'difficulty_level'], 'lc_category_diff_idx');
            $table->index('instructor_user_id', 'lc_instructor_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_courses');
    }
};
