<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add missing fields to event_registrations table
     */
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            // Add registration code (human-friendly for refunds/support)
            if (!Schema::hasColumn('event_registrations', 'registration_code')) {
                $table->string('registration_code')
                    ->unique()
                    ->after('qr_token')
                    ->comment('Human-readable code for reference');
            }
            
            // Add payment status
            if (!Schema::hasColumn('event_registrations', 'payment_status')) {
                $table->enum('payment_status', ['unpaid', 'paid', 'free'])
                    ->default('free')
                    ->after('status')
                    ->comment('Payment status for paid events');
            }
            
            // Add ticket QR path
            if (!Schema::hasColumn('event_registrations', 'ticket_qr_path')) {
                $table->string('ticket_qr_path')
                    ->nullable()
                    ->after('payment_status')
                    ->comment('Path to generated QR code image');
            }
            
            // Add registered_at if not exists
            if (!Schema::hasColumn('event_registrations', 'registered_at')) {
                $table->timestamp('registered_at')
                    ->useCurrent()
                    ->after('ticket_qr_path')
                    ->comment('When user registered for event');
            }
            
            // Add unique constraint for (event_id, user_id) to prevent duplicates
            if (!Schema::hasColumn('event_registrations', 'unique_event_user')) {
                try {
                    $table->unique(['event_id', 'user_id'])->name('event_registrations_unique_event_user');
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropUnique('event_registrations_unique_event_user');
            $table->dropColumn([
                'registration_code',
                'payment_status',
                'ticket_qr_path',
                'registered_at',
            ]);
        });
    }
};
