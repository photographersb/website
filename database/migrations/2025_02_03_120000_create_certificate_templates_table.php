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
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->enum('type', ['participation', 'finalist', 'winner', 'merit']);
            $table->decimal('width', 8, 2)->comment('Width in millimeters');
            $table->decimal('height', 8, 2)->comment('Height in millimeters');
            $table->string('background_color')->default('#ffffff')->comment('Hex color');
            $table->string('accent_color')->default('#8B0000')->comment('Hex color');
            $table->string('text_color')->default('#000000')->comment('Hex color');
            $table->enum('title_font', ['serif', 'sans-serif', 'monospace'])->default('serif');
            $table->boolean('is_default')->default(false);
            $table->longText('template_content')->nullable()->comment('HTML template content');
            $table->timestamps();
            $table->index('type');
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
