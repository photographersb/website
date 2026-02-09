<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_sponsors', function (Blueprint $table) {
            if (!Schema::hasColumn('competition_sponsors', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('display_order');
            }
            if (!Schema::hasColumn('competition_sponsors', 'sponsored_amount')) {
                $table->decimal('sponsored_amount', 10, 2)->nullable()->after('contribution_amount');
            }
        });

        DB::statement("ALTER TABLE competition_sponsors MODIFY COLUMN tier ENUM('title','gold','silver','bronze','support','platinum') DEFAULT 'bronze'");
    }

    public function down(): void
    {
        Schema::table('competition_sponsors', function (Blueprint $table) {
            $columns = ['sort_order', 'sponsored_amount'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('competition_sponsors', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        DB::statement("ALTER TABLE competition_sponsors MODIFY COLUMN tier ENUM('platinum','gold','silver','bronze') DEFAULT 'bronze'");
    }
};
