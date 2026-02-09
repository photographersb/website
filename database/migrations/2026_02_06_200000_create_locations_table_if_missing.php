<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('locations')) {
            return;
        }

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->enum('type', ['division', 'district', 'upazila'])->index();
            $table->foreignId('parent_id')->nullable()->constrained('locations')->cascadeOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'parent_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
