<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SEO Meta table (polymorphic)
        Schema::create('seo_meta', function (Blueprint $table) {
            $table->id();
            $table->morphs('model'); // model_type, model_id
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            
            // Open Graph
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            
            // Twitter Card
            $table->string('twitter_card')->default('summary_large_image')->nullable();
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            
            // Robots
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);
            $table->string('robots_snippet')->nullable(); // max-snippet-length
            
            // Schema.org JSON-LD
            $table->longText('schema_json')->nullable();
            
            // Internal fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_auto_generated')->default(false);
            $table->timestamps();

            // Indexes for performance
            // Note: morphs() already creates an index on (model_type, model_id)
            $table->index('canonical_url');
            $table->index('created_at');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_meta');
    }
};
