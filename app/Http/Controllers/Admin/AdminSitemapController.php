<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSitemapCheck;
use App\Models\AdminSitemapCheckResult;
use App\Services\AdminSitemapTestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSitemapController extends Controller
{
    private AdminSitemapTestService $testService;

    public function __construct(AdminSitemapTestService $testService)
    {
        $this->testService = $testService;
        $this->middleware('auth');
        $this->middleware('role:admin,super_admin');
    }

    /**
     * Show sitemap dashboard
     */
    public function index()
    {
        $recentChecks = AdminSitemapCheck::with('runByUser', 'results')
            ->latest()
            ->limit(5)
            ->get();

        $latestCheck = AdminSitemapCheck::with('results')
            ->latest()
            ->first();

        $stats = [];
        if ($latestCheck) {
            $stats = [
                'total_routes' => $latestCheck->total_links,
                'passed' => $latestCheck->passed,
                'failed' => $latestCheck->failed,
                'skipped' => $latestCheck->skipped,
                'success_rate' => round($latestCheck->getSuccessRate(), 1),
                'last_scan_at' => $latestCheck->isComplete() ? $latestCheck->finished_at : null,
                'duration' => $latestCheck->getDurationSeconds(),
            ];
        }

        $adminRoutes = $this->testService->getAdminRoutes();

        return view('admin.sitemap.index', [
            'stats' => $stats,
            'latestCheck' => $latestCheck,
            'recentChecks' => $recentChecks,
            'adminRoutes' => $adminRoutes,
        ]);
    }

    /**
     * Run sitemap test
     */
    public function runTest(Request $request)
    {
        try {
            $user = Auth::user();
            $check = $this->testService->startCheck($user->id);
            $this->testService->runAllTests($check, $user);

            return response()->json([
                'success' => true,
                'check_id' => $check->id,
                'message' => 'Sitemap test completed successfully',
            ]);

        } catch (\Exception $e) {
            \Log::error('AdminSitemap test error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error running sitemap test: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all checks
     */
    public function getChecks(Request $request)
    {
        $checks = AdminSitemapCheck::with('runByUser')
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $checks->items(),
            'pagination' => [
                'current_page' => $checks->currentPage(),
                'per_page' => $checks->perPage(),
                'total' => $checks->total(),
                'last_page' => $checks->lastPage(),
            ],
        ]);
    }

    /**
     * Show single check details
     */
    public function show(AdminSitemapCheck $check)
    {
        $check->load('runByUser', 'results');

        $moduleFilter = request('module');
        $statusFilter = request('status');
        $searchQuery = request('search');
        $sortBy = request('sort_by', 'module');

        $results = $check->results();

        if ($moduleFilter) {
            $results = $results->where('module', $moduleFilter);
        }

        if ($statusFilter) {
            $results = $results->where('result_status', $statusFilter);
        }

        if ($searchQuery) {
            $results = $results->where('url', 'like', "%{$searchQuery}%")
                ->orWhere('route_name', 'like', "%{$searchQuery}%");
        }

        $results = match($sortBy) {
            'response_time_asc' => $results->orderBy('response_time_ms', 'asc'),
            'response_time_desc' => $results->orderBy('response_time_ms', 'desc'),
            'status_code' => $results->orderBy('status_code', 'desc'),
            'module' => $results->orderBy('module', 'asc'),
            default => $results->orderBy('module', 'asc'),
        };

        $results = $results->paginate(25);

        $modules = AdminSitemapCheckResult::where('check_id', $check->id)
            ->distinct()
            ->pluck('module')
            ->sort();

        $failedResults = $check->results()
            ->where('result_status', 'failed')
            ->limit(10)
            ->get();

        return view('admin.sitemap.show', [
            'check' => $check,
            'results' => $results,
            'modules' => $modules,
            'failedResults' => $failedResults,
            'filters' => [
                'module' => $moduleFilter,
                'status' => $statusFilter,
                'search' => $searchQuery,
                'sort_by' => $sortBy,
            ],
        ]);
    }

    /**
     * Export check results as CSV
     */
    public function export(AdminSitemapCheck $check)
    {
        if (Auth::user()->role !== 'super_admin') {
            abort(403);
        }

        $results = $check->results()
            ->orderBy('module')
            ->orderBy('route_name')
            ->get();

        $csv = "Module,Route Name,URL,Method,Status Code,Response Time (ms),Result Status,Error Summary\n";

        foreach ($results as $result) {
            $errorSummary = str_replace('"', '""', $result->error_summary ?? '');
            $csv .= sprintf(
                "\"%s\",\"%s\",\"%s\",\"%s\",%s,%d,\"%s\",\"%s\"\n",
                $result->module,
                $result->route_name,
                $result->url,
                $result->method,
                $result->status_code ?? 'null',
                $result->response_time_ms,
                $result->result_status,
                $errorSummary
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"sitemap-check-{$check->id}.csv\"",
        ]);
    }

    /**
     * Delete a check
     */
    public function destroy(AdminSitemapCheck $check)
    {
        if (Auth::id() !== $check->run_by_user_id && Auth::user()->role !== 'super_admin') {
            abort(403);
        }

        $check->results()->delete();
        $check->delete();

        return response()->json([
            'success' => true,
            'message' => 'Check deleted successfully',
        ]);
    }
}
