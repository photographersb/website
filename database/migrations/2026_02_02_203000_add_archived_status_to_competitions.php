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
        DB::statement("ALTER TABLE competitions MODIFY status ENUM('draft','active','judging','completed','cancelled','archived') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE competitions SET status = 'cancelled' WHERE status = 'archived'");
        DB::statement("ALTER TABLE competitions MODIFY status ENUM('draft','active','judging','completed','cancelled') NOT NULL DEFAULT 'draft'");
    }
};
