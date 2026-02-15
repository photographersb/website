<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_tickets')) {
            Schema::table('event_tickets', function (Blueprint $table) {
                if (
                    Schema::hasColumn('event_tickets', 'event_id')
                    && Schema::hasColumn('event_tickets', 'is_active')
                    && $this->indexExists('event_tickets', 'idx_event_tickets_event_active')
                ) {
                    $table->dropIndex('idx_event_tickets_event_active');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('event_tickets')) {
            Schema::table('event_tickets', function (Blueprint $table) {
                if (
                    Schema::hasColumn('event_tickets', 'event_id')
                    && Schema::hasColumn('event_tickets', 'is_active')
                    && !$this->indexExists('event_tickets', 'idx_event_tickets_event_active')
                ) {
                    $table->index(['event_id', 'is_active'], 'idx_event_tickets_event_active');
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
