<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('cities') || !Schema::hasTable('locations') || !Schema::hasTable('photographers')) {
            return;
        }

        $cityRows = DB::table('cities')
            ->select('id', 'name', 'slug', 'division', 'state', 'latitude', 'longitude', 'display_order')
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left empty to avoid destructive rollback.
    }
};
