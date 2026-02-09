<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_logs', 'registration_id')) {
                $table->foreignId('registration_id')->nullable()->constrained('event_registrations')->onDelete('set null');
            }
            if (!Schema::hasColumn('attendance_logs', 'method')) {
                $table->enum('method', ['qr', 'manual'])->default('qr');
            }
            if (!Schema::hasColumn('attendance_logs', 'scanned_at')) {
                $table->timestamp('scanned_at')->useCurrent();
            }
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->unique(['event_id', 'registration_id'], 'ux_attendance_event_registration');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropUnique('ux_attendance_event_registration');
        });
    }
};
