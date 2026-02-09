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
        Schema::create('site_links', function (Blueprint $table) {
            $table->id();
            $table->enum('section', [
                'navbar',
                'footer_company',
                'footer_legal',
                'footer_useful',
                'social',
                'cta'
            ])->index();
            $table->string('title');
            $table->text('url');
            $table->string('icon')->nullable()->comment('Icon class or path for social links');
            $table->string('route_name')->nullable()->comment('Internal route name (alternative to URL)');
            $table->boolean('open_in_new_tab')->default(true);
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->enum('visibility', ['public', 'guest_only', 'auth_only'])->default('public');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Composite index for common queries
            $table->index(['section', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_links');
    }
};
