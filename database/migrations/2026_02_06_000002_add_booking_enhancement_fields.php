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
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('reminder_sent_at')->nullable()->after('completed_at');
            $table->boolean('allow_review')->default(false)->after('reminder_sent_at');
            $table->timestamp('review_requested_at')->nullable()->after('allow_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_at', 'allow_review', 'review_requested_at']);
        });
    }
};
