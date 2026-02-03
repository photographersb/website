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
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100)->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type', 50)->nullable(); // mobile, desktop, tablet
            $table->string('browser', 100)->nullable();
            $table->string('os', 100)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('landing_page', 500)->nullable();
            $table->timestamp('first_visit')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->integer('page_views')->default(0);
            $table->timestamps();
            
            $table->index('last_activity');
            $table->index('created_at');
        });
        
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_log_id')->constrained()->onDelete('cascade');
            $table->string('url', 500);
            $table->string('page_title', 255)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->integer('time_on_page')->default(0); // seconds
            $table->timestamp('viewed_at');
            $table->timestamps();
            
            $table->index(['visitor_log_id', 'viewed_at']);
            $table->index('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('visitor_logs');
    }
};
