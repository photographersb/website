<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_prizes', function (Blueprint $table) {
            if (!Schema::hasColumn('competition_prizes', 'award_type')) {
                $table->enum('award_type', ['global', 'district', 'people_choice', 'special'])->default('global')->after('title');
            }
            if (!Schema::hasColumn('competition_prizes', 'prize_type')) {
                $table->enum('prize_type', ['cash', 'trophy', 'gift', 'certificate', 'sponsor_product', 'featured_boost'])->default('cash')->after('award_type');
            }
            if (!Schema::hasColumn('competition_prizes', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable()->after('cash_amount');
            }
            if (!Schema::hasColumn('competition_prizes', 'prize_description')) {
                $table->text('prize_description')->nullable()->after('description');
            }
            if (!Schema::hasColumn('competition_prizes', 'sponsor_id')) {
                $table->foreignId('sponsor_id')->nullable()->constrained('sponsors')->onDelete('set null')->after('competition_id');
            }
            if (!Schema::hasColumn('competition_prizes', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('display_order');
            }
            if (!Schema::hasColumn('competition_prizes', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('sort_order');
            }
        });

        DB::statement("UPDATE competition_prizes SET amount = cash_amount WHERE amount IS NULL AND cash_amount IS NOT NULL");
    }

    public function down(): void
    {
        Schema::table('competition_prizes', function (Blueprint $table) {
            $columns = ['award_type', 'prize_type', 'amount', 'prize_description', 'sponsor_id', 'sort_order', 'is_active'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('competition_prizes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
