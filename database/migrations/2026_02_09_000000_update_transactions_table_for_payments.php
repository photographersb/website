<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'booking_id')) {
                $table->foreignId('booking_id')
                    ->nullable()
                    ->constrained('bookings')
                    ->nullOnDelete()
                    ->after('user_id');
            }

            if (!Schema::hasColumn('transactions', 'photographer_id')) {
                $table->foreignId('photographer_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->after('booking_id');
            }

            if (!Schema::hasColumn('transactions', 'transaction_id')) {
                $table->string('transaction_id')
                    ->nullable()
                    ->unique()
                    ->after('uuid');
            }

            if (!Schema::hasColumn('transactions', 'gateway_response')) {
                $table->json('gateway_response')->nullable()->after('gateway_reference');
            }

            if (!Schema::hasColumn('transactions', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('payment_date');
            }

            if (!Schema::hasColumn('transactions', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable()->after('net_amount');
            }

            if (!Schema::hasColumn('transactions', 'refund_status')) {
                $table->string('refund_status', 20)->nullable()->after('refund_amount');
            }

            if (!Schema::hasColumn('transactions', 'refund_reason')) {
                $table->text('refund_reason')->nullable()->after('refund_status');
            }

            if (!Schema::hasColumn('transactions', 'refund_reference')) {
                $table->string('refund_reference')->nullable()->after('refund_reason');
            }

            if (!Schema::hasColumn('transactions', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('refund_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'refunded_at')) {
                $table->dropColumn('refunded_at');
            }

            if (Schema::hasColumn('transactions', 'refund_reference')) {
                $table->dropColumn('refund_reference');
            }

            if (Schema::hasColumn('transactions', 'refund_reason')) {
                $table->dropColumn('refund_reason');
            }

            if (Schema::hasColumn('transactions', 'refund_status')) {
                $table->dropColumn('refund_status');
            }

            if (Schema::hasColumn('transactions', 'refund_amount')) {
                $table->dropColumn('refund_amount');
            }

            if (Schema::hasColumn('transactions', 'completed_at')) {
                $table->dropColumn('completed_at');
            }

            if (Schema::hasColumn('transactions', 'gateway_response')) {
                $table->dropColumn('gateway_response');
            }

            if (Schema::hasColumn('transactions', 'transaction_id')) {
                $table->dropUnique(['transaction_id']);
                $table->dropColumn('transaction_id');
            }

            if (Schema::hasColumn('transactions', 'photographer_id')) {
                $table->dropConstrainedForeignId('photographer_id');
            }

            if (Schema::hasColumn('transactions', 'booking_id')) {
                $table->dropConstrainedForeignId('booking_id');
            }
        });
    }
};
