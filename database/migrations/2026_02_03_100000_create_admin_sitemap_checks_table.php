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
        Schema::create('admin_sitemap_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('started_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('finished_at')->nullable();
            $table->integer('total_links')->default(0);
            $table->integer('passed_links')->default(0);
            $table->integer('failed_links')->default(0);
            $table->integer('skipped_links')->default(0);
            $table->string('status')->default('running')->comment('running, completed, failed');
            $table->text('error_summary')->nullable();
            $table->timestamps();

            $table->index('started_by_user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_sitemap_checks');
    }
};
