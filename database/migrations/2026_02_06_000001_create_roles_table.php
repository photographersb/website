<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('key')->unique();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_system')->default(false);
            $table->string('icon')->nullable();
            $table->string('color_class')->nullable();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            [
                'name' => 'Super Admin',
                'key' => 'super_admin',
                'description' => 'Full system access with all permissions',
                'permissions' => json_encode(['*']),
                'is_system' => true,
                'icon' => '👑',
                'color_class' => 'bg-purple-100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin',
                'key' => 'admin',
                'description' => 'Administrative access with most permissions',
                'permissions' => json_encode([
                    'manage_users',
                    'manage_photographers',
                    'manage_events',
                    'manage_competitions',
                    'manage_reviews',
                    'manage_bookings',
                    'view_analytics',
                    'manage_settings',
                    'manage_roles'
                ]),
                'is_system' => true,
                'icon' => '⚡',
                'color_class' => 'bg-blue-100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Moderator',
                'key' => 'moderator',
                'description' => 'Content moderation and user management',
                'permissions' => json_encode([
                    'manage_reviews',
                    'manage_submissions',
                    'moderate_content',
                    'verify_photographers'
                ]),
                'is_system' => true,
                'icon' => '🛡️',
                'color_class' => 'bg-green-100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Photographer',
                'key' => 'photographer',
                'description' => 'Professional photographer account',
                'permissions' => json_encode([
                    'create_portfolio',
                    'manage_bookings',
                    'submit_competitions',
                    'respond_reviews'
                ]),
                'is_system' => true,
                'icon' => '📸',
                'color_class' => 'bg-pink-100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'User',
                'key' => 'client',
                'description' => 'Standard user account',
                'permissions' => json_encode([
                    'book_photographer',
                    'leave_review',
                    'view_events',
                    'vote_competitions'
                ]),
                'is_system' => true,
                'icon' => '👤',
                'color_class' => 'bg-gray-100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
