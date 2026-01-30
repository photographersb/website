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
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('sample_images'); // Simplified price field
            $table->integer('duration_hours')->nullable()->after('price'); // Simplified duration
            $table->integer('edited_photos')->default(0)->after('duration_hours');
            $table->integer('raw_photos')->default(0)->after('edited_photos');
            $table->integer('delivery_days')->default(7)->after('raw_photos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['price', 'duration_hours', 'edited_photos', 'raw_photos', 'delivery_days']);
        });
    }
};
