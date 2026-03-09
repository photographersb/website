<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('learning_courses')->cascadeOnDelete();
            $table->string('title', 180);
            $table->enum('lesson_type', ['video', 'text', 'photo_tutorial', 'resource']);
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();
            $table->json('attachments')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['course_id', 'sort_order'], 'll_course_sort_idx');
            $table->index(['course_id', 'is_published'], 'll_course_pub_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_lessons');
    }
};
