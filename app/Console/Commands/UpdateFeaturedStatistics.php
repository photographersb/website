<?php

namespace App\Console\Commands;

use App\Models\FeaturedPhotographer;
use App\Models\FeaturedPhotographerPayment;
use Illuminate\Console\Command;

class UpdateFeaturedStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'featured:update-statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update featured photographer statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Calculate statistics
            $stats = [
                'total_active' => FeaturedPhotographer::active()->count(),
                'total_revenue_month' => FeaturedPhotographerPayment::completed()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->sum('amount'),
                'total_revenue_all_time' => FeaturedPhotographerPayment::completed()->sum('amount'),
                'by_package' => [
                    'starter' => FeaturedPhotographer::active()->where('package_tier', 'Starter')->count(),
                    'professional' => FeaturedPhotographer::active()->where('package_tier', 'Professional')->count(),
                    'enterprise' => FeaturedPhotographer::active()->where('package_tier', 'Enterprise')->count(),
                ],
                'by_category' => $this->getStatsByCategory(),
                'by_location' => $this->getStatsByLocation(),
                'expiring_soon' => FeaturedPhotographer::where('active', true)
                    ->whereBetween('end_date', [now(), now()->addDays(7)])
                    ->count(),
            ];

            // Cache the statistics for quick access
            cache()->put('featured_photographers_stats', $stats, now()->addHours(1));

            $this->info('✅ Featured photographer statistics updated successfully');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error updating statistics: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Get statistics by category
     */
    private function getStatsByCategory()
    {
        return FeaturedPhotographer::active()
            ->groupBy('category')
            ->select('category')
            ->selectRaw('count(*) as count')
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Get statistics by location
     */
    private function getStatsByLocation()
    {
        return FeaturedPhotographer::active()
            ->groupBy('location')
            ->select('location')
            ->selectRaw('count(*) as count')
            ->pluck('count', 'location')
            ->toArray();
    }
}
