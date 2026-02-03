<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSitemapCheck;
use App\Services\AdminSitemapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminSitemapController extends Controller
{
    protected AdminSitemapService $service;

    public function __construct(AdminSitemapService $service)
    {
        $this->service = $service;
        // Remove middleware from constructor - will be applied in routes
    }

    /**
     * Display admin sitemap - Visual Navigation Map
     */
    public function index(Request $request)
    {
        $links = $this->service->getSitemapLinks();

        // Group by module
        $groupedLinks = collect($links)->groupBy('module')->toArray();

        return view('admin.sitemap.index', [
            'links' => $links,
            'groupedLinks' => $groupedLinks,
            'totalLinks' => count($links),
        ]);
    }

    /**
     * Start link test
     */
    public function startTest(Request $request)
    {
        try {
            // Get authenticated user or create a system user
            $user = auth()->user() ?? \App\Models\User::first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No user available to run test. Please create a user first.'
                ], 403);
            }
            
            $check = $this->service->runLinkTests($user);

            return response()->json([
                'success' => true,
                'message' => 'Link test completed',
                'check_id' => $check->id,
                'results' => [
                    'total' => $check->total_links,
                    'passed' => $check->passed_links,
                    'failed' => $check->failed_links,
                    'skipped' => $check->skipped_links,
                    'duration' => $check->getDurationSeconds(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error running test: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View specific check results
     */
    public function viewCheck(AdminSitemapCheck $check, Request $request)
    {
        $module = $request->query('module');
        $status = $request->query('status');
        $statusCode = $request->query('status_code');
        $search = $request->query('search');

        $results = $this->service->getCheckResults($check, $module, $status, $statusCode, $search);

        // Get unique modules and statuses for filters
        $modules = $check->results()->distinct()->pluck('module')->sort()->toArray();
        $statuses = $check->results()->distinct()->pluck('result_status')->sort()->toArray();
        $statusCodes = $check->results()
            ->whereNotNull('status_code')
            ->distinct()
            ->pluck('status_code')
            ->sort()
            ->toArray();

        return view('admin.sitemap.check-results', [
            'check' => $check,
            'results' => $results,
            'modules' => $modules,
            'statuses' => $statuses,
            'statusCodes' => $statusCodes,
            'currentFilters' => [
                'module' => $module,
                'status' => $status,
                'status_code' => $statusCode,
                'search' => $search,
            ]
        ]);
    }

    /**
     * View check statistics
     */
    public function checkStats(AdminSitemapCheck $check)
    {
        $results = $check->results()
            ->select('module', 'result_status')
            ->get()
            ->groupBy('module')
            ->map(function ($group) {
                return [
                    'passed' => $group->where('result_status', 'passed')->count(),
                    'failed' => $group->where('result_status', 'failed')->count(),
                    'skipped' => $group->where('result_status', 'skipped')->count(),
                    'total' => $group->count(),
                ];
            });

        return response()->json([
            'check' => [
                'id' => $check->id,
                'started_at' => $check->started_at,
                'finished_at' => $check->finished_at,
                'duration_seconds' => $check->getDurationSeconds(),
                'passed_percentage' => $check->getPassedPercentage(),
                'totals' => [
                    'total' => $check->total_links,
                    'passed' => $check->passed_links,
                    'failed' => $check->failed_links,
                    'skipped' => $check->skipped_links,
                ]
            ],
            'by_module' => $results
        ]);
    }

    /**
     * Export results as CSV
     */
    public function exportCsv(AdminSitemapCheck $check)
    {
        $results = $check->results()->orderBy('module')->orderBy('url')->get();

        $csvContent = "Module,Link Name,URL,Status,Status Code,Response Time (ms),Error Summary\n";

        foreach ($results as $result) {
            $statusCode = $result->status_code ?? 'N/A';
            $errorSummary = str_replace('"', '""', $result->error_summary ?? '');

            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%d,\"%s\"\n",
                $result->module,
                str_replace(',', ' ', $this->getLinkName($result->url)),
                $result->url,
                $result->result_status,
                $statusCode,
                $result->response_time_ms,
                $errorSummary
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=sitemap-check-' . $check->id . '.csv');
    }

    /**
     * Delete a check
     */
    public function deleteCheck(AdminSitemapCheck $check)
    {
        $check->results()->delete();
        $check->delete();

        return back()->with('success', 'Sitemap check deleted successfully');
    }

    /**
     * Get link name from URL
     */
    private function getLinkName(string $url): string
    {
        $name = str_replace('admin/', '', $url);
        $name = str_replace(['/', '_', '-'], ' ', $name);
        return ucwords($name);
    }
}
