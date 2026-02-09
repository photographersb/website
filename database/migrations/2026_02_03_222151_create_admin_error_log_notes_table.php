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
        if (!Schema::hasTable('admin_error_log_notes')) {
            Schema::create('admin_error_log_notes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('error_log_id');
                $table->text('note');
                $table->unsignedBigInteger('added_by_user_id');
                $table->timestamps();
                
                // Foreign Keys
                $table->foreign('error_log_id')
                    ->references('id')
                    ->on('admin_error_logs')
                    ->onDelete('cascade');
                    
                $table->foreign('added_by_user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                    
                // Index for quick lookups
                $table->index('error_log_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_error_log_notes');
    }
};
