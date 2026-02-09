<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_registrations', 'registration_code')) {
                $table->string('registration_code')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('event_registrations', 'ticket_qr_path')) {
                $table->string('ticket_qr_path')->nullable()->after('registration_code');
            }
            if (!Schema::hasColumn('event_registrations', 'quantity')) {
                $table->integer('quantity')->default(1)->after('ticket_id');
            }
        });

        DB::statement("ALTER TABLE event_registrations MODIFY COLUMN status ENUM('registered','cancelled','attended','no_show','pending_payment','confirmed') DEFAULT 'registered'");

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->unique(['event_id', 'user_id'], 'ux_event_registrations_user');
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('event_registrations', 'registration_code')) {
                $table->dropColumn('registration_code');
            }
            if (Schema::hasColumn('event_registrations', 'ticket_qr_path')) {
                $table->dropColumn('ticket_qr_path');
            }
            if (Schema::hasColumn('event_registrations', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('event_registrations', 'status')) {
                DB::statement("ALTER TABLE event_registrations MODIFY COLUMN status ENUM('pending_payment','confirmed','cancelled','refunded','attended') DEFAULT 'confirmed'");
            }
            $table->dropUnique('ux_event_registrations_user');
        });
    }
};
