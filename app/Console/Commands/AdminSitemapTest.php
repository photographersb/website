<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AdminSitemapService;
use Illuminate\Console\Command;

class AdminSitemapTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:sitemap-test {--user-id=1 : The user ID to run the test as}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Run comprehensive admin sitemap link tests and store results';

    /**
     * Create a new command instance.
     */
    public function __construct(protected AdminSitemapService $service)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            // Get user
            $userId = $this->option('user-id');
            $user = User::findOrFail($userId);

            if (!$user->hasRole(['admin', 'super_admin'])) {
                $this->error("User {$userId} is not an admin");
                return self::FAILURE;
            }

            $this->info('Starting Admin Sitemap Test...');
            $this->newLine();

            $bar = $this->output->createProgressBar(1);
            $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% | %message%');

            $this->info('Gathering sitemap links...');
            $links = $this->service->getSitemapLinks();
            $this->line("Found <info>" . count($links) . "</info> links to test");
            $this->newLine();

            // Run tests
            $this->info('Running link tests (this may take a while)...');
            $bar->setMessage('Testing links...');
            $check = $this->service->runLinkTests($user);
            $bar->advance();
            $bar->finish();
            $this->newLine(2);

            // Show results
            $this->displayResults($check);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Display test results
     */
    private function displayResults($check): void
    {
        $this->info('===== SITEMAP TEST RESULTS =====');
        $this->newLine();

        $this->line("Total Links Tested: <info>{$check->total_links}</info>");
        $this->line("Passed: <fg=green>{$check->passed_links}</> (" . $check->getPassedPercentage() . "%)");
        $this->line("Failed: <fg=red>{$check->failed_links}</>");
        $this->line("Skipped: <fg=yellow>{$check->skipped_links}</>");
        $this->line("Duration: <info>" . number_format($check->getDurationSeconds(), 2) . "s</>");
        $this->newLine();

        // Show failed links
        if ($check->failed_links > 0) {
            $this->warn('Failed Links:');
            $failedResults = $check->results()
                ->where('result_status', 'failed')
                ->orderBy('module')
                ->orderBy('url')
                ->get();

            foreach ($failedResults as $result) {
                $this->line("  <fg=red>✗</> {$result->module}: {$result->url}");
                $this->line("    Status: {$result->status_code} | Error: {$result->error_summary}");
                $this->line("    Fix: {$result->getRecommendedFix()}");
            }
            $this->newLine();
        }

        // Show summary by module
        $this->info('Summary by Module:');
        $moduleSummary = $check->results()
            ->select('module')
            ->distinct()
            ->get()
            ->map(function ($item) use ($check) {
                $moduleResults = $check->results()->where('module', $item->module)->get();
                return [
                    'module' => $item->module,
                    'passed' => $moduleResults->where('result_status', 'passed')->count(),
                    'failed' => $moduleResults->where('result_status', 'failed')->count(),
                    'skipped' => $moduleResults->where('result_status', 'skipped')->count(),
                ];
            });

        foreach ($moduleSummary as $summary) {
            $status = $summary['failed'] > 0 ? '<fg=red>⚠</>' : '<fg=green>✓</>';
            $this->line(sprintf(
                "  %s %s: %d passed, %d failed, %d skipped",
                $status,
                $summary['module'],
                $summary['passed'],
                $summary['failed'],
                $summary['skipped']
            ));
        }

        $this->newLine();
        $this->info("Check ID: {$check->id}");
        $this->info('View results in Admin Panel: /admin/sitemap/checks/' . $check->id);
    }
}
