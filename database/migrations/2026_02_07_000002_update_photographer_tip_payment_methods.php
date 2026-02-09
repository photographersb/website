<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE photographer_tips MODIFY payment_method ENUM('bkash','nagad','rocket','manual') DEFAULT 'bkash'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE photographer_tips MODIFY payment_method ENUM('bkash','nagad','rocket','manual') DEFAULT 'bkash'");
    }
};
