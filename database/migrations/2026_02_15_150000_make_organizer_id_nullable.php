<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make organizer_id nullable to support draft events without organizer
        Schema::table('events', function (Blueprint $table) {
            // Drop the foreign key constraint first
            try {
                $table->dropForeign(['organizer_id']);
            } catch (\Exception $e) {
                // Constraint might not exist or have different name
            }
        });

        // Now modify the column to be nullable and re-add the foreign key
        DB::statement('ALTER TABLE events MODIFY organizer_id BIGINT UNSIGNED NULL');
        
        Schema::table('events', function (Blueprint $table) {
            // Re-add foreign key with nullable column
            $table->foreign('organizer_id')
                  ->references('id')
                  ->on('photographers')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            try {
                $table->dropForeign(['organizer_id']);
            } catch (\Exception $e) {
                // Ignore if not exists
            }
        });

        // Restore as NOT NULL (set to 1 for existing nulls first)
        DB::statement('UPDATE events SET organizer_id = 1 WHERE organizer_id IS NULL');
        DB::statement('ALTER TABLE events MODIFY organizer_id BIGINT UNSIGNED NOT NULL');
        
        Schema::table('events', function (Blueprint $table) {
            $table->foreign('organizer_id')
                  ->references('id')
                  ->on('photographers')
                  ->onDelete('cascade');
        });
    }
};
