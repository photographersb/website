<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            // Add tip_phone_number field as replacement for the manual tip phone entry
            // This is the new unified field for receiving tips via mobile payment
            $table->string('tip_phone_number')->nullable()->after('accept_tips')->comment('Phone number for receiving tips via bKash, Nagad, or Rocket');
        });
    }

    public function down(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            $table->dropColumn(['tip_phone_number']);
        });
    }
};
