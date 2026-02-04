<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create attendance_logs table for tracking event check-ins
     */
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('registration_id')->nullable()->constrained('event_registrations')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('scanned_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Attendance details
            $table->enum('method', ['qr', 'manual'])->default('qr');
            $table->timestamp('scanned_at')->useCurrent();
            
            // Notes for manual scans
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['event_id', 'user_id']);
            $table->index(['scanned_at']);
            
            // Prevent double scanning the same registration
            $table->unique(['registration_id', 'scanned_at'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
