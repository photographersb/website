<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('guest', 'client', 'photographer', 'studio_owner', 'studio_manager', 'studio_photographer', 'judge', 'moderator', 'admin', 'super_admin') NOT NULL DEFAULT 'guest'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('guest', 'client', 'photographer', 'studio_owner', 'studio_manager', 'studio_photographer', 'moderator', 'admin', 'super_admin') NOT NULL DEFAULT 'guest'");
    }
};
