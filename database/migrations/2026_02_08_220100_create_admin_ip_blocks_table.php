<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admin_ip_blocks')) {
            return;
        }

        Schema::create('admin_ip_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45)->unique();
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('blocked_by_user_id')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('geo_country', 100)->nullable();
            $table->string('geo_region', 100)->nullable();
            $table->string('geo_city', 100)->nullable();
            $table->decimal('geo_lat', 10, 7)->nullable();
            $table->decimal('geo_lng', 10, 7)->nullable();
            $table->string('geo_timezone', 60)->nullable();
            $table->string('geo_isp', 120)->nullable();
            $table->timestamps();

            $table->foreign('blocked_by_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_ip_blocks');
    }
};
