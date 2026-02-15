<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE event_payments MODIFY COLUMN method ENUM('bkash','nagad','rocket','manual','card') DEFAULT 'manual'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE event_payments MODIFY COLUMN method ENUM('bkash','nagad','rocket','manual') DEFAULT 'manual'");
    }
};
