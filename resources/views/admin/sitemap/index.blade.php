@extends('admin.layout')

@section('title', 'Admin Sitemap & Link Tester')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Admin Sitemap & Link Tester</h1>
            <p class="text-gray-600 mt-2">Monitor all admin routes and test them for broken links</p>
        </div>
        <button onclick="runSitemapTest()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
            <i class="fas fa-play mr-2"></i>Run Link Test
        </button>
    </div>

    <!-- Stats Cards -->
    @if($latestCheck)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Routes</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_routes'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-sitemap text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Passed</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['passed'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $stats['success_rate'] ?? 0 }}% success</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Failed</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['failed'] ?? 0 }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-times text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Skipped</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['skipped'] ?? 0 }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-minus text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last Scan Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <p class="text-sm text-gray-700">
                <strong>Last scan:</strong> 
                @if($stats['last_scan_at'])
                    {{ $stats['last_scan_at']->diffForHumans() }}
                    @if($stats['duration'])
                        (took {{ $stats['duration'] }}s)
                    @endif
                @else
                    No scans performed yet
                @endif
            </p>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8 text-center">
            <i class="fas fa-info-circle text-yellow-600 text-2xl mb-3"></i>
            <p class="text-gray-700">No sitemap tests have been run yet. Click "Run Link Test" to get started.</p>
        </div>
    @endif

    <!-- Recent Checks -->
    @if($recentChecks->count() > 0)
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recent Scans</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Date</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Run By</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Routes Tested</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Results</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentChecks as $check)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $check->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $check->runByUser->name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    {{ $check->total_links }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex gap-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ {{ $check->passed }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ✗ {{ $check->failed }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ⊘ {{ $check->skipped }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($check->isComplete())
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            In Progress
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('admin.sitemap.show', $check) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Admin Routes Overview -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Admin Routes to Test ({{ count($adminRoutes) }})</h2>
        </div>
        <div class="p-6">
            @if(count($adminRoutes) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($adminRoutes as $route)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="font-semibold text-gray-900">{{ $route['route_name'] }}</h3>
                                <span class="text-xs bg-gray-200 text-gray-800 px-2 py-1 rounded">
                                    {{ $route['module'] }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2 break-all">{{ $route['uri'] }}</p>
                            <p class="text-xs text-gray-500">{{ $route['controller'] }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 text-center py-8">No admin routes found</p>
            @endif
        </div>
    </div>
</div>

<script>
function runSitemapTest() {
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Running...';

    fetch('{{ route("admin.sitemap.run-test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("admin.sitemap.show", "") }}/' + data.check_id;
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play mr-2"></i>Run Link Test';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error running sitemap test');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-play mr-2"></i>Run Link Test';
    });
}
</script>
@endsection
