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
        Schema::table('users', function (Blueprint $table) {
            // Add approval system fields - CRITICAL P0 FIX
            // These fields are referenced in AuthController but were missing from schema
            
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('role')
                  ->comment('User approval workflow: pending → approved or rejected');
            
            $table->text('rejection_reason')
                  ->nullable()
                  ->after('approval_status')
                  ->comment('Reason for rejecting user approval');
            
            // Audit timestamps
            $table->timestamp('approved_at')
                  ->nullable()
                  ->after('rejection_reason')
                  ->comment('When admin approved this user');
            
            $table->unsignedBigInteger('approved_by_admin_id')
                  ->nullable()
                  ->after('approved_at')
                  ->comment('Admin who approved this user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'approval_status',
                'rejection_reason',
                'approved_at',
                'approved_by_admin_id',
            ]);
        });
    }
};
