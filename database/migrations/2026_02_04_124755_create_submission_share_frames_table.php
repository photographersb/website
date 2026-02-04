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
        Schema::create('submission_share_frames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_submission_id')->constrained('competition_submissions')->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('competition_share_frame_templates')->onDelete('set null');
            
            // Generated Frame Files
            $table->string('story_frame_path')->nullable(); // 9:16
            $table->string('post_frame_path')->nullable(); // 1:1
            $table->string('portrait_frame_path')->nullable(); // 4:5
            $table->string('landscape_frame_path')->nullable(); // 16:9
            
            // Metadata
            $table->string('qr_code_path')->nullable();
            $table->integer('generation_count')->default(1); // Track regenerations
            $table->timestamp('last_generated_at')->nullable();
            
            // Original image info (cached for performance)
            $table->integer('original_width')->nullable();
            $table->integer('original_height')->nullable();
            $table->string('original_orientation')->nullable(); // portrait, landscape, square
            
            $table->timestamps();
            
            // Indexes
            $table->index('competition_submission_id');
            $table->index('template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_share_frames');
    }
};
