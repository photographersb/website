<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class ViewActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:activity
                            {--user= : Filter by user ID}
                            {--action= : Filter by action type}
                            {--limit=50 : Number of logs to display}
                            {--export= : Export to CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View application activity logs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->option('user');
        $action = $this->option('action');
        $limit = $this->option('limit');
        $export = $this->option('export');

        $query = ActivityLog::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($action) {
            $query->where('action', $action);
        }

        $activities = $query->latest()->limit($limit)->get();

        if ($export) {
            $this->exportToCSV($activities, $export);
            $this->info("Exported " . count($activities) . " logs to {$export}");
            return 0;
        }

        if ($activities->isEmpty()) {
            $this->warn('No activity logs found');
            return 0;
        }

        $headers = ['ID', 'User', 'Action', 'Description', 'IP Address', 'Created At'];
        $rows = $activities->map(function ($activity) {
            return [
                $activity->id,
                $activity->user?->name ?? 'System',
                $activity->action,
                $activity->description,
                $activity->ip_address,
                $activity->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();

        $this->table($headers, $rows);

        return 0;
    }

    /**
     * Export logs to CSV file
     */
    private function exportToCSV($activities, string $fileName): void
    {
        $handle = fopen($fileName, 'w');

        fputcsv($handle, ['ID', 'User', 'Action', 'Description', 'IP Address', 'Created At']);

        foreach ($activities as $activity) {
            fputcsv($handle, [
                $activity->id,
                $activity->user?->name ?? 'System',
                $activity->action,
                $activity->description,
                $activity->ip_address,
                $activity->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($handle);
    }
}
