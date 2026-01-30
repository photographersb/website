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
        Schema::table('competition_submissions', function (Blueprint $table) {
            // Add image_path if doesn't exist
            if (!Schema::hasColumn('competition_submissions', 'image_path')) {
                $table->string('image_path')->after('photographer_id');
            }
            
            // Add rejection_reason if doesn't exist
            if (!Schema::hasColumn('competition_submissions', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
            
            // Add deleted_at for soft deletes if doesn't exist
            if (!Schema::hasColumn('competition_submissions', 'deleted_at')) {
                $table->softDeletes();
            }
            
            // Make title required if it's nullable
            $table->string('title')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('competition_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('competition_submissions', 'image_path')) {
                $table->dropColumn('image_path');
            }
            if (Schema::hasColumn('competition_submissions', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
            if (Schema::hasColumn('competition_submissions', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
