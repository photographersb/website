<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_sponsors', function (Blueprint $table) {
            $table->string('logo_credit_name')->nullable()->after('logo_url');
            $table->string('logo_credit_url')->nullable()->after('logo_credit_name');
        });
    }

    public function down(): void
    {
        Schema::table('competition_sponsors', function (Blueprint $table) {
            $table->dropColumn(['logo_credit_name', 'logo_credit_url']);
        });
    }
};
