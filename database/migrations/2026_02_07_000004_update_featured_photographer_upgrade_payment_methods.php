<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE featured_photographer_upgrades MODIFY payment_method ENUM('bkash','nagad','upay','sslcommerz','cash')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE featured_photographer_upgrades MODIFY payment_method ENUM('bkash','nagad','upay','sslcommerz','cash')");
    }
};
