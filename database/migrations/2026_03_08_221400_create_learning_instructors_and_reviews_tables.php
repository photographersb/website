<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_instructor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->json('expertise')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_active')->default(true);
            $table->decimal('student_rating', 3, 2)->default(0);
            $table->unsignedInteger('courses_created')->default(0);
            $table->unsignedInteger('students_count')->default(0);
            $table->timestamps();

            $table->index(['is_approved', 'is_active'], 'lip_approved_active_idx');
        });

        Schema::create('learning_course_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('learning_courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('feedback')->nullable();
            $table->enum('status', ['published', 'hidden'])->default('published');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
            $table->index(['course_id', 'status'], 'lcr_course_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_course_reviews');
        Schema::dropIfExists('learning_instructor_profiles');
    }
};
