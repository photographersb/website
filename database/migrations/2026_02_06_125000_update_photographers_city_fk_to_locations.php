<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('photographers')) {
            return;
        }

        if (Schema::hasTable('cities') && Schema::hasTable('locations')) {
            $cityRows = DB::table('cities')
                ->select('id', 'name', 'slug', 'display_order')
                ->get();

            foreach ($cityRows as $city) {
                $location = DB::table('locations')
                    ->where('slug', $city->slug)
                    ->orWhere('name', $city->name)
                    ->first();

                if (!$location) {
                    $locationId = DB::table('locations')->insertGetId([
                        'name' => $city->name,
                        'slug' => $city->slug,
                        'type' => 'district',
                        'parent_id' => null,
                        'is_active' => true,
                        'seo_title' => null,
                        'seo_description' => null,
                        'sort_order' => $city->display_order ?? 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $locationId = $location->id;
                }

                DB::table('photographers')
                    ->where('city_id', $city->id)
                    ->update(['city_id' => $locationId]);
            }
        }

        if (!Schema::hasTable('locations')) {
            return;
        }

        $databaseName = DB::getDatabaseName();
        $existingForeignKey = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'photographers')
            ->where('COLUMN_NAME', 'city_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        Schema::table('photographers', function (Blueprint $table) use ($existingForeignKey) {
            if (Schema::hasColumn('photographers', 'city_id')) {
                if ($existingForeignKey) {
                    $table->dropForeign($existingForeignKey);
                }
                $table->foreign('city_id')
                    ->references('id')
                    ->on('locations')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('photographers')) {
            return;
        }

        if (!Schema::hasTable('cities')) {
            return;
        }

        $databaseName = DB::getDatabaseName();
        $existingForeignKey = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'photographers')
            ->where('COLUMN_NAME', 'city_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        Schema::table('photographers', function (Blueprint $table) use ($existingForeignKey) {
            if (Schema::hasColumn('photographers', 'city_id')) {
                if ($existingForeignKey) {
                    $table->dropForeign($existingForeignKey);
                }
                $table->foreign('city_id')
                    ->references('id')
                    ->on('cities')
                    ->nullOnDelete();
            }
        });
    }
};
