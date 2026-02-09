<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('event_payments', 'event_id')) {
                $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null')->after('id');
            }
            if (!Schema::hasColumn('event_payments', 'registration_id')) {
                $table->foreignId('registration_id')->nullable()->constrained('event_registrations')->onDelete('cascade')->after('event_id');
            }
            if (!Schema::hasColumn('event_payments', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->after('registration_id');
            }
            if (!Schema::hasColumn('event_payments', 'method')) {
                $table->enum('method', ['bkash', 'nagad', 'rocket', 'manual'])->default('manual')->after('user_id');
            }
            if (!Schema::hasColumn('event_payments', 'sender_number')) {
                $table->string('sender_number')->nullable()->after('method');
            }
            if (!Schema::hasColumn('event_payments', 'trx_id')) {
                $table->string('trx_id')->nullable()->after('sender_number');
            }
            if (!Schema::hasColumn('event_payments', 'screenshot_path')) {
                $table->string('screenshot_path')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('event_payments', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('screenshot_path');
            }
            if (!Schema::hasColumn('event_payments', 'verified_by_user_id')) {
                $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->onDelete('set null')->after('admin_note');
            }
            if (!Schema::hasColumn('event_payments', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by_user_id');
            }
        });

        DB::statement("ALTER TABLE event_payments MODIFY COLUMN status ENUM('pending','approved','rejected','completed','failed','cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('event_payments', function (Blueprint $table) {
            $columns = [
                'event_id',
                'registration_id',
                'user_id',
                'method',
                'sender_number',
                'trx_id',
                'screenshot_path',
                'admin_note',
                'verified_by_user_id',
                'verified_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('event_payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        DB::statement("ALTER TABLE event_payments MODIFY COLUMN status ENUM('pending','completed','failed','cancelled') DEFAULT 'pending'");
    }
};
