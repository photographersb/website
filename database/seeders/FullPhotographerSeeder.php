<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Photographer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FullPhotographerSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👤 Seeding a full photographer profile...');

        $email = 'full.photographer@example.com';
        $username = 'full-photographer';

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'uuid' => Str::uuid(),
                'name' => 'Full Profile Photographer',
                'username' => $username,
                'phone' => '01799999999',
                'password' => Hash::make('password'),
                'role' => 'photographer',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'approval_status' => 'approved',
                'approved_at' => now(),
                'rejection_reason' => null,
                'last_login_at' => now()->subDays(2),
            ]
        );

        [$cityId, $cityName] = $this->resolveCity();

        $categories = Category::query()->orderBy('display_order')->limit(4)->get();
        $specializations = $categories->pluck('name')->filter()->values()->all();

        $photographer = Photographer::updateOrCreate(
            ['user_id' => $user->id],
            [
                'slug' => $username,
                'bio' => 'Luxury wedding and lifestyle photographer with 12+ years of experience across Bangladesh.',
                'location' => $cityName,
                'city_id' => $cityId,
                'experience_years' => 12,
                'specializations' => $specializations,
                'service_area_radius' => 80,
                'average_rating' => 4.9,
                'rating_count' => 48,
                'total_bookings' => 120,
                'completed_bookings' => 114,
                'is_verified' => true,
                'verification_type' => 'phone_email_id',
                'verified_at' => now(),
                'is_featured' => true,
                'featured_until' => now()->addMonths(2),
                'profile_completeness' => 95,
                'response_time_avg' => 6.5,
                'facebook_url' => 'https://facebook.com/fullphotographer',
                'instagram_url' => 'https://instagram.com/fullphotographer',
                'twitter_url' => 'https://twitter.com/fullphotographer',
                'linkedin_url' => 'https://linkedin.com/in/fullphotographer',
                'youtube_url' => 'https://youtube.com/@fullphotographer',
                'website_url' => 'https://fullphotographer.example.com',
                'pexels_url' => 'https://www.pexels.com/@fullphotographer',
                'bkash_number' => '+8801928889888',
                'nagad_number' => '+8801812345678',
                'rocket_number' => '+8801912345678',
                'phone_number' => '01712345678',
                'accept_tips' => true,
                'tip_message' => 'If you loved the work, tips keep the lenses rolling. Thank you!',
                'is_available' => true,
                'response_time_preference' => 'under_1_hour',
                'booking_lead_time' => 7,
                'profile_picture' => 'profiles/full-photographer.jpg',
            ]
        );

        if ($categories->isNotEmpty()) {
            $photographer->categories()->sync($categories->pluck('id')->all());
        }

        $this->command->info('✅ Full photographer profile seeded.');
        $this->command->line("Email: {$email}");
        $this->command->line('Password: password');
    }

    private function resolveCity(): array
    {
        $table = $this->cityTable();

        if (!$table) {
            return [null, null];
        }

        $row = DB::table($table)->orderBy('id')->first();
        if (!$row) {
            return [null, null];
        }

        return [$row->id, $row->name ?? null];
    }

    private function cityTable(): ?string
    {
        $fk = DB::selectOne(
            "SELECT referenced_table_name AS table_name
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE table_schema = DATABASE()
               AND table_name = 'photographers'
               AND column_name = 'city_id'
               AND referenced_table_name IS NOT NULL
             LIMIT 1"
        );

        if ($fk && isset($fk->table_name)) {
            return $fk->table_name;
        }

        if (DB::getSchemaBuilder()->hasTable('locations')) {
            return 'locations';
        }

        if (DB::getSchemaBuilder()->hasTable('cities')) {
            return 'cities';
        }

        return null;
    }
}
