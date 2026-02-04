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
        Schema::create('competition_share_frame_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->onDelete('cascade');
            
            // Template Design
            $table->string('name')->default('Default Template');
            $table->string('background_image')->nullable(); // Frame overlay image
            $table->string('background_color')->default('#8B0000'); // Burgundy
            $table->string('text_color')->default('#FFFFFF');
            $table->string('accent_color')->default('#D4A574'); // Gold
            $table->string('font_family')->default('DM Sans');
            
            // Text Configuration
            $table->text('cta_message')->default('Vote for me ❤️\nSupport my photo on Photographer SB');
            $table->boolean('show_competition_name')->default(true);
            $table->boolean('show_photographer_name')->default(true);
            $table->boolean('show_submission_title')->default(true);
            
            // Watermark & QR
            $table->boolean('show_watermark')->default(true);
            $table->string('watermark_position')->default('bottom-right'); // bottom-left, bottom-right, top-right, top-left
            $table->boolean('show_qr_code')->default(true);
            $table->string('qr_position')->default('bottom-left');
            
            // Layout Settings
            $table->integer('padding_top')->default(80); // pixels
            $table->integer('padding_bottom')->default(80);
            $table->integer('padding_left')->default(40);
            $table->integer('padding_right')->default(40);
            $table->string('image_fit_strategy')->default('contain'); // contain, cover
            $table->boolean('add_text_overlay_gradient')->default(true);
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index('competition_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_share_frame_templates');
    }
};
