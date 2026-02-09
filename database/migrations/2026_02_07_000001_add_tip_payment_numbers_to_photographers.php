<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            if (!Schema::hasColumn('photographers', 'nagad_number')) {
                $table->string('nagad_number')->nullable()->after('bkash_number');
            }
            if (!Schema::hasColumn('photographers', 'rocket_number')) {
                $table->string('rocket_number')->nullable()->after('nagad_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            if (Schema::hasColumn('photographers', 'rocket_number')) {
                $table->dropColumn('rocket_number');
            }
            if (Schema::hasColumn('photographers', 'nagad_number')) {
                $table->dropColumn('nagad_number');
            }
        });
    }
};
