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
        Schema::create('booking_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_request_id')->constrained('booking_requests')->onDelete('cascade');
            $table->enum('old_status', ['pending', 'accepted', 'declined', 'cancelled', 'completed'])->nullable();
            $table->enum('new_status', ['pending', 'accepted', 'declined', 'cancelled', 'completed']);
            $table->foreignId('changed_by_user_id')->constrained('users')->onDelete('cascade');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('booking_request_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_status_logs');
    }
};
