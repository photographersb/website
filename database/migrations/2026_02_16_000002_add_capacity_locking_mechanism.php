<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds capacity locking mechanism to event_tickets to prevent race condition overbooking
     */
    public function up(): void
    {
        Schema::table('event_tickets', function (Blueprint $table) {
            // Add reserved quantity tracking (for preventing race conditions)
            if (!Schema::hasColumn('event_tickets', 'reserved_qty')) {
                $table->integer('reserved_qty')->default(0)->after('quantity')->comment('Reserved for pending payments');
            }
            
            // Add lock timestamp for distributed locking
            if (!Schema::hasColumn('event_tickets', 'capacity_lock_until')) {
                $table->timestamp('capacity_lock_until')->nullable()->after('reserved_qty')->comment('Distributed lock expiry for capacity checks');
            }

            // Index for capacity checks
            $table->index(['event_id', 'is_active'], 'idx_event_tickets_event_active');
        });

        // Add tracking for registration locking
        Schema::table('event_registrations', function (Blueprint $table) {
            // Lock token for idempotency
            if (!Schema::hasColumn('event_registrations', 'lock_token')) {
                $table->string('lock_token')->nullable()->after('id')->comment('Idempotency key for lock request');
                $table->unique('lock_token', 'event_registrations_lock_token_unique');
            }

            // Lock acquired timestamp
            if (!Schema::hasColumn('event_registrations', 'locked_at')) {
                $table->timestamp('locked_at')->nullable()->after('lock_token')->comment('When capacity lock was acquired');
            }

            // Payment timeout tracking
            if (!Schema::hasColumn('event_registrations', 'payment_expires_at')) {
                $table->timestamp('payment_expires_at')->nullable()->after('locked_at')->comment('When reserved capacity expires if payment not confirmed');
            }

            // Index for finding locked registrations
            $table->index(['event_id', 'status'], 'idx_event_reg_event_status');
            $table->index('payment_expires_at', 'idx_payment_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_tickets', function (Blueprint $table) {
            $table->dropIndex('idx_event_tickets_event_active');
            if (Schema::hasColumn('event_tickets', 'capacity_lock_until')) {
                $table->dropColumn('capacity_lock_until');
            }
            if (Schema::hasColumn('event_tickets', 'reserved_qty')) {
                $table->dropColumn('reserved_qty');
            }
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropIndex('idx_payment_expires_at');
            $table->dropIndex('idx_event_reg_event_status');
            $table->dropUnique('event_registrations_lock_token_unique');
            if (Schema::hasColumn('event_registrations', 'payment_expires_at')) {
                $table->dropColumn('payment_expires_at');
            }
            if (Schema::hasColumn('event_registrations', 'locked_at')) {
                $table->dropColumn('locked_at');
            }
            if (Schema::hasColumn('event_registrations', 'lock_token')) {
                $table->dropColumn('lock_token');
            }
        });
    }
};
