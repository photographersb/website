<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('preferred_time_slot')->nullable()->after('event_date');
            $table->string('event_type_detail')->nullable()->after('event_location'); // wedding, birthday, corporate
            $table->enum('indoor_outdoor', ['indoor', 'outdoor', 'both'])->nullable()->after('event_type_detail');
            $table->json('additional_services')->nullable()->after('requirements'); // [videography, drone, album]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['preferred_time_slot', 'event_type_detail', 'indoor_outdoor', 'additional_services']);
        });
    }
};
