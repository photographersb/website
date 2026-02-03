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
            $table->foreignId('check_id')->constrained('admin_sitemap_checks')->onDelete('cascade');
            $table->string('route_name')->nullable();
            $table->string('url');
            $table->string('method')->default('GET');
            $table->string('module')->comment('Dashboard, Users, Photographers, Events, etc');
            $table->integer('status_code')->nullable();
            $table->integer('response_time_ms')->default(0);
            $table->enum('result_status', ['passed', 'failed', 'skipped'])->default('skipped');
            $table->text('error_summary')->nullable();
            $table->text('error_details')->nullable();
            $table->boolean('has_blank_body')->default(false);
            $table->timestamps();

            $table->index('check_id');
            $table->index('status_code');
            $table->index('result_status');
            $table->index('module');
            $table->index('route_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_sitemap_check_results');
        Schema::dropIfExists('admin_sitemap_checks');
    }
};
