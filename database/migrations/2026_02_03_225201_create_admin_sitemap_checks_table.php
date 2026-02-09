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
        if (!Schema::hasTable('admin_sitemap_checks')) {
            Schema::create('admin_sitemap_checks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('run_by_user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamp('started_at')->useCurrent();
                $table->timestamp('finished_at')->nullable();
                $table->integer('total_links')->default(0);
                $table->integer('passed')->default(0);
                $table->integer('failed')->default(0);
                $table->integer('skipped')->default(0);
                $table->timestamps();

                $table->index('run_by_user_id');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_sitemap_checks');
    }
};
