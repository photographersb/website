@extends('layouts.admin')

@section('title', 'Sitemap Check Results')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.sitemap') }}" class="text-burgundy-600 hover:text-burgundy-700 mb-2 inline-block">← Back to Sitemap</a>
            <h1 class="text-3xl font-bold text-gray-900">Check #{{ $check->id }} Results</h1>
            <p class="text-gray-600 mt-1">Started by {{ $check->user->name }} on {{ $check->started_at->format('M d, Y H:i') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.sitemap.export', $check->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                📥 Export CSV
            </a>
            <form method="DELETE" action="{{ route('admin.sitemap.delete', $check->id) }}" style="display: inline;">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="return confirm('Delete this check?')">
                    🗑️ Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Links</p>
            <p class="text-3xl font-bold">{{ $check->total_links }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Passed</p>
            <p class="text-3xl font-bold text-green-600">{{ $check->passed_links }}</p>
            <p class="text-xs text-gray-600 mt-1">{{ $check->getPassedPercentage() }}%</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Failed</p>
            <p class="text-3xl font-bold text-red-600">{{ $check->failed_links }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Skipped</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $check->skipped_links }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-1">Module</label>
                <select name="module" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ $currentFilters['module'] === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Status</option>
                    @foreach($statuses as $stat)
                        <option value="{{ $stat }}" {{ $currentFilters['status'] === $stat ? 'selected' : '' }}>
                            {{ ucfirst($stat) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-1">HTTP Status</label>
                <select name="status_code" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Status Codes</option>
                    @foreach($statusCodes as $code)
                        <option value="{{ $code }}" {{ $currentFilters['status_code'] == $code ? 'selected' : '' }}>
                            {{ $code }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-900 mb-1">Search</label>
                <input type="text" name="search" placeholder="Search URL or route name..." value="{{ $currentFilters['search'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>

            <button type="submit" class="px-4 py-2 bg-burgundy-600 text-white rounded-lg hover:bg-burgundy-700">
                Filter
            </button>

            <a href="{{ route('admin.sitemap.check', $check->id) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Clear
            </a>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Module</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">URL</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Route Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">HTTP</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Time (ms)</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Error Summary</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $result)
                    <tr class="border-t hover:bg-gray-50" onclick="toggleDetails({{ $result->id }})">
                        <td class="px-4 py-3 text-sm font-medium">{{ $result->module }}</td>
                        <td class="px-4 py-3 text-sm font-mono text-xs bg-gray-50 rounded">
                            <code>{{ $result->url }}</code>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 truncate" title="{{ $result->route_name }}">
                            {{ $result->route_name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $result->getBadgeClass() }}">
                                {{ ucfirst($result->result_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($result->status_code)
                                <span class="
                                    @if($result->status_code >= 200 && $result->status_code < 300) text-green-600 @endif
                                    @if($result->status_code >= 400) text-red-600 @endif
                                    @if($result->status_code >= 300 && $result->status_code < 400) text-blue-600 @endif
                                    font-semibold
                                ">
                                    {{ $result->status_code }}
                                </span>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $result->response_time_ms }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($result->error_summary)
                                <span class="text-red-600 truncate inline-block max-w-xs" title="{{ $result->error_summary }}">
                                    {{ $result->error_summary }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>

                    <!-- Details Row (Hidden by default) -->
                    <tr id="details-{{ $result->id }}" style="display: none;" class="bg-gray-50">
                        <td colspan="7" class="px-4 py-4">
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Recommended Fix:</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $result->getRecommendedFix() }}</p>
                                </div>
                                @if($result->error_details)
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Error Details:</p>
                                        <pre class="text-xs bg-white border border-gray-300 rounded p-2 mt-1 overflow-auto max-h-32 text-gray-600">{{ $result->error_details }}</pre>
                                    </div>
                                @endif
                                @if($result->has_blank_body)
                                    <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                        <p class="text-sm text-yellow-800">⚠️ Response body is blank - page may not be rendering correctly</p>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                            No results found matching your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($results->hasPages())
        <div class="mt-6">
            {{ $results->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<script>
    function toggleDetails(resultId) {
        const details = document.getElementById('details-' + resultId);
        details.style.display = details.style.display === 'none' ? 'table-row' : 'none';
    }
</script>
@endsection
