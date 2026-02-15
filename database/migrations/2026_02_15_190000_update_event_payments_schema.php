<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_payments', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('event_payments', 'event_id')) {
                $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('cascade');
            }
            if (!Schema::hasColumn('event_payments', 'registration_id')) {
                $table->foreignId('registration_id')->nullable();
            }
            if (!Schema::hasColumn('event_payments', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('event_payments', 'method')) {
                $table->string('method')->nullable(); // card, bkash, nagad, rocket, manual
            }
            if (!Schema::hasColumn('event_payments', 'sender_number')) {
                $table->string('sender_number')->nullable();
            }
            if (!Schema::hasColumn('event_payments', 'trx_id')) {
                $table->string('trx_id')->nullable();
            }
            if (!Schema::hasColumn('event_payments', 'screenshot_path')) {
                $table->string('screenshot_path')->nullable();
            }
            if (!Schema::hasColumn('event_payments', 'admin_note')) {
                $table->text('admin_note')->nullable();
            }
            if (!Schema::hasColumn('event_payments', 'verified_by_user_id')) {
                $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('event_payments', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
        });

        // Make transaction_id nullable if it exists
        if (Schema::hasColumn('event_payments', 'transaction_id')) {
            Schema::table('event_payments', function (Blueprint $table) {
                $table->string('transaction_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('event_payments', function (Blueprint $table) {
            // Revert transaction_id to NOT nullable
            if (Schema::hasColumn('event_payments', 'transaction_id')) {
                $table->string('transaction_id')->change();
            }
        });
    }
};
