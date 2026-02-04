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
        Schema::create('admin_sitemap_check_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_id')->constrained('admin_sitemap_checks')->cascadeOnDelete();
            $table->string('module', 100);
            $table->string('route_name', 255);
            $table->string('url', 500);
            $table->string('method', 10)->default('GET');
            $table->integer('status_code')->nullable();
            $table->integer('response_time_ms')->default(0);
            $table->enum('result_status', ['passed', 'failed', 'skipped'])->default('skipped');
            $table->text('error_summary')->nullable();
            $table->timestamps();

            $table->index('check_id');
            $table->index('module');
            $table->index('result_status');
            $table->index('status_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_sitemap_check_results');
    }
};
