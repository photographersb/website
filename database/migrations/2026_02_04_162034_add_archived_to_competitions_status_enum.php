<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'archived' status to competitions enum
        DB::statement("ALTER TABLE competitions MODIFY COLUMN status ENUM('draft', 'active', 'judging', 'completed', 'cancelled', 'archived') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'archived' status from competitions enum
        DB::statement("ALTER TABLE competitions MODIFY COLUMN status ENUM('draft', 'active', 'judging', 'completed', 'cancelled') DEFAULT 'draft'");
    }
};
