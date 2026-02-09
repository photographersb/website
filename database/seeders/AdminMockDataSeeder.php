<?php

namespace Database\Seeders;

use App\Models\Judge;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Hashtag;
use App\Models\Notification;
use App\Models\ErrorLog;
use App\Models\ShareFrame;
use Illuminate\Database\Seeder;

class AdminMockDataSeeder extends Seeder
{
    public function run()
    {
        // Create mock error logs
        ErrorLog::factory(25)->create();
        
        // Create mock share frames
        ShareFrame::factory(15)->create();
        
        $this->command->info('Admin mock data seeded successfully!');
    }
}
