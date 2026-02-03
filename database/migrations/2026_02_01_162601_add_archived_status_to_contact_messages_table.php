<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum to include 'archived'
        DB::statement("ALTER TABLE contact_messages MODIFY COLUMN status ENUM('pending', 'read', 'resolved', 'archived') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE contact_messages MODIFY COLUMN status ENUM('pending', 'read', 'resolved') DEFAULT 'pending'");
    }
};
