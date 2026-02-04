@extends('admin.layout')

@section('title', 'Sitemap Check Results')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('admin.sitemap.index') }}" class="text-blue-600 hover:text-blue-900 mb-3 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Check Results</h1>
            <p class="text-gray-600 mt-2">
                Ran by {{ $check->runByUser->name ?? 'Unknown' }} 
                @if($check->finished_at)
                    on {{ $check->finished_at->format('M d, Y H:i') }}
                @endif
            </p>
        </div>
        @if(\Illuminate\Support\Facades\Auth::user()->role === 'super_admin')
            <a href="{{ route('admin.sitemap.export', $check) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
        @endif
    </div>

    <!-- Results Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Total Routes</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $check->total_links }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Passed</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $check->passed }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Failed</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $check->failed }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Skipped</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $check->skipped }}</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-gray-600 text-sm">Success Rate</p>
                <div class="mt-2">
                    <div class="flex items-end gap-2">
                        <span class="text-2xl font-bold text-blue-600">{{ round($check->getSuccessRate(), 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $check->getSuccessRate() }}%"></div>
                    </div>
                </div>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Duration</p>
                <p class="text-2xl font-bold text-gray-900 mt-2">{{ $check->getDurationSeconds() ?? 'N/A' }}s</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Check Status</p>
                <div class="mt-2">
                    @if($check->isComplete())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i>Completed
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-spinner fa-spin mr-1"></i>In Progress
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters & Search</h3>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Module Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Module</label>
                <select name="module" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" @if($filters['module'] === $module) selected @endif>
                            {{ $module }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    <option value="passed" @if($filters['status'] === 'passed') selected @endif>Passed</option>
                    <option value="failed" @if($filters['status'] === 'failed') selected @endif>Failed</option>
                    <option value="skipped" @if($filters['status'] === 'skipped') selected @endif>Skipped</option>
                </select>
            </div>

            <!-- Sort By -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select name="sort_by" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="module" @if($filters['sort_by'] === 'module') selected @endif>Module (A-Z)</option>
                    <option value="response_time_desc" @if($filters['sort_by'] === 'response_time_desc') selected @endif>Slowest First</option>
                    <option value="response_time_asc" @if($filters['sort_by'] === 'response_time_asc') selected @endif>Fastest First</option>
                    <option value="status_code" @if($filters['sort_by'] === 'status_code') selected @endif>Status Code</option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="URL or route..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
        </form>
    </div>

    <!-- Failed Results Alert -->
    @if($failedResults->count() > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-red-900 mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>{{ $failedResults->count() }} Failed Tests
            </h3>
            <div class="space-y-3">
                @foreach($failedResults->take(5) as $result)
                    <div class="bg-white p-4 rounded border border-red-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $result->route_name }}</p>
                                <p class="text-sm text-gray-600">{{ $result->url }}</p>
                                <p class="text-sm text-red-600 mt-1">{{ $result->error_summary }}</p>
                            </div>
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                                HTTP {{ $result->status_code ?? 'ERROR' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($failedResults->count() > 5)
                <p class="text-sm text-gray-600 mt-4">... and {{ $failedResults->count() - 5 }} more failures</p>
            @endif
        </div>
    @endif

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detailed Results</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Module</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Route Name</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">URL</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status Code</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Response Time</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Result</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Error</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($results as $result)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $result->module }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $result->route_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                <a href="{{ $result->url }}" target="_blank" class="text-blue-600 hover:text-blue-900 break-all">
                                    {{ $result->url }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($result->status_code)
                                    <span class="font-medium {{ $result->status_code < 300 ? 'text-green-600' : ($result->status_code < 400 ? 'text-blue-600' : 'text-red-600') }}">
                                        {{ $result->status_code }}
                                    </span>
                                @else
                                    <span class="text-gray-500">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $result->response_time_ms }}ms</td>
                            <td class="px-6 py-4 text-sm">
                                @if($result->result_status === 'passed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✓ Passed
                                    </span>
                                @elseif($result->result_status === 'failed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        ✗ Failed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        ⊘ Skipped
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-red-600">
                                @if($result->error_summary)
                                    <span title="{{ $result->error_summary }}">{{ Str::limit($result->error_summary, 30) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-600">
                                No results match your filters
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($results->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $results->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
