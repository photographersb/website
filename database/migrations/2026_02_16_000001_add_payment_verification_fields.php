<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_payments', function (Blueprint $table) {
            // Add verification fields
            if (!Schema::hasColumn('event_payments', 'verification_status')) {
                $table->string('verification_status')->default('pending')->after('transaction_id');
            }
            if (!Schema::hasColumn('event_payments', 'gateway_response')) {
                $table->json('gateway_response')->nullable()->after('verification_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_payments', function (Blueprint $table) {
            if (Schema::hasColumn('event_payments', 'verification_status')) {
                $table->dropColumn('verification_status');
            }
            if (Schema::hasColumn('event_payments', 'gateway_response')) {
                $table->dropColumn('gateway_response');
            }
        });
    }
};
