<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (
                    Schema::hasColumn('notifications', 'notifiable_type')
                    && Schema::hasColumn('notifications', 'notifiable_id')
                    && Schema::hasColumn('notifications', 'read_at')
                    && !$this->indexExists('notifications', 'idx_notifications_notifiable_read')
                ) {
                    $table->index(
                        ['notifiable_type', 'notifiable_id', 'read_at'],
                        'idx_notifications_notifiable_read'
                    );
                }
            });
        }

        if (Schema::hasTable('event_tickets')) {
            Schema::table('event_tickets', function (Blueprint $table) {
                if (
                    Schema::hasColumn('event_tickets', 'event_id')
                    && Schema::hasColumn('event_tickets', 'ticket_type')
                    && !$this->indexExists('event_tickets', 'idx_event_tickets_type')
                ) {
                    $table->index(['event_id', 'ticket_type'], 'idx_event_tickets_type');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (
                    Schema::hasColumn('notifications', 'notifiable_type')
                    && Schema::hasColumn('notifications', 'notifiable_id')
                    && Schema::hasColumn('notifications', 'read_at')
                    && $this->indexExists('notifications', 'idx_notifications_notifiable_read')
                ) {
                    $table->dropIndex('idx_notifications_notifiable_read');
                }
            });
        }

        if (Schema::hasTable('event_tickets')) {
            Schema::table('event_tickets', function (Blueprint $table) {
                if (
                    Schema::hasColumn('event_tickets', 'event_id')
                    && Schema::hasColumn('event_tickets', 'ticket_type')
                    && $this->indexExists('event_tickets', 'idx_event_tickets_type')
                ) {
                    $table->dropIndex('idx_event_tickets_type');
                }
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $schema = DB::getDatabaseName();
        $result = DB::selectOne(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$schema, $table, $index]
        );

        return $result !== null;
    }
};
