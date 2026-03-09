<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('learning_courses')->cascadeOnDelete();
            $table->enum('status', ['enrolled', 'completed', 'cancelled'])->default('enrolled');
            $table->unsignedInteger('completed_lessons')->default(0);
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('certificate_id')->nullable()->constrained('certificates')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
            $table->index(['status', 'completion_percentage'], 'le_status_progress_idx');
        });

        Schema::create('learning_lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('learning_enrollments')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('learning_lessons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['enrollment_id', 'lesson_id']);
            $table->index(['user_id', 'completed_at'], 'llp_user_completed_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_lesson_progress');
        Schema::dropIfExists('learning_enrollments');
    }
};
