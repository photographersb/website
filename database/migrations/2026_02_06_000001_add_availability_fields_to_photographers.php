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
        Schema::table('photographers', function (Blueprint $table) {
            // Add availability fields
            $table->boolean('is_available')->default(true)->after('accept_tips')->comment('Is photographer currently available for bookings');
            $table->enum('response_time_preference', ['under_1_hour', '1_to_3_hours', '3_to_24_hours', 'over_24_hours'])->nullable()->after('is_available')->comment('How quickly photographer typically responds');
            $table->integer('booking_lead_time')->default(0)->after('response_time_preference')->comment('Minimum days notice required for bookings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            $table->dropColumn(['is_available', 'response_time_preference', 'booking_lead_time']);
        });
    }
};
