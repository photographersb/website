<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_date')->unique();
            $table->json('metrics');
            $table->json('alerts')->nullable();
            $table->json('security_summary')->nullable();
            $table->timestamps();

            $table->index('report_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_reports');
    }
};
