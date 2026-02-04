@extends('layouts.admin')

@section('title', 'Error Center Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">🚨 Error Center</h1>
            <p class="mt-2 text-gray-600">Monitor and manage system errors in real-time</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            {{-- Errors Today --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Errors Today</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['errors_today'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Open Errors --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Open Errors</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['open_errors'] ?? 0 }}</p>
                    </div>
                    <div class="bg-red-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- P0 Errors --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">P0 Critical</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['p0_errors'] ?? 0 }}</p>
                    </div>
                    <div class="bg-orange-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6v-2m0 0v-2" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Muted Errors --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-gray-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Muted</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['muted_errors'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.858 4a3 3 0 00-3 3v10a3 3 0 003 3h12a3 3 0 003-3V7a3 3 0 00-3-3H6.858z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow mb-8 p-6">
            <form method="GET" action="{{ route('admin.error-center.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    {{-- Search --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Message, URL, Route..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>

                    {{-- Severity --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
                        <select 
                            name="severity" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Severity</option>
                            @foreach($severities as $severity)
                                <option value="{{ $severity }}" @selected(($filters['severity'] ?? '') === $severity)>
                                    {{ $severity }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select 
                            name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            <option value="open" @selected(($filters['status'] ?? '') === 'open')>Open</option>
                            <option value="resolved" @selected(($filters['status'] ?? '') === 'resolved')>Resolved</option>
                            <option value="muted" @selected(($filters['status'] ?? '') === 'muted')>Muted</option>
                        </select>
                    </div>

                    {{-- Environment --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Environment</label>
                        <select 
                            name="environment" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Environments</option>
                            @foreach($environments as $env)
                                <option value="{{ $env }}" @selected(($filters['environment'] ?? '') === $env)>
                                    {{ ucfirst($env) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Code</label>
                        <select 
                            name="status_code" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Codes</option>
                            @foreach($statusCodes as $code)
                                <option value="{{ $code }}" @selected(($filters['status_code'] ?? '') == $code)>
                                    {{ $code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
                    >
                        Apply Filters
                    </button>
                    <a 
                        href="{{ route('admin.error-center.index') }}" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-400"
                    >
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Errors Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Severity</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Message</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Route</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Code</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Occurrences</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Last Seen</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($errors as $error)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                {{-- Severity Badge --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($error->severity === 'P0') bg-red-100 text-red-800
                                        @elseif($error->severity === 'P1') bg-orange-100 text-orange-800
                                        @elseif($error->severity === 'P2') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ $error->severity }}
                                    </span>
                                </td>

                                {{-- Message --}}
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.error-center.show', $error) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                        {{ Str::limit($error->message, 50) }}
                                    </a>
                                </td>

                                {{-- Route --}}
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $error->route_name ?? 'N/A' }}
                                </td>

                                {{-- Status Code --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($error->status_code)
                                        <span class="font-mono font-semibold
                                            @if($error->status_code >= 500) text-red-600
                                            @elseif($error->status_code >= 400) text-orange-600
                                            @else text-green-600
                                            @endif
                                        ">
                                            {{ $error->status_code }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Occurrences --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ $error->occurrences }}×
                                </td>

                                {{-- Last Seen --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span title="{{ $error->last_seen_at }}">
                                        {{ $error->last_seen_at->diffForHumans() }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($error->is_muted)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
                                            🔇 Muted
                                        </span>
                                    @elseif($error->is_resolved)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                                            ✅ Resolved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                                            ⚠️ Open
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm space-x-2">
                                    <a 
                                        href="{{ route('admin.error-center.show', $error) }}" 
                                        class="text-blue-600 hover:text-blue-900 font-medium"
                                    >
                                        View
                                    </a>
                                    
                                    @if(!$error->is_resolved && !$error->is_muted)
                                        <button 
                                            onclick="resolveError({{ $error->id }})"
                                            class="text-green-600 hover:text-green-900 font-medium"
                                            title="Mark as Resolved"
                                        >
                                            ✓
                                        </button>
                                        
                                        <button 
                                            onclick="muteError({{ $error->id }})"
                                            class="text-gray-600 hover:text-gray-900 font-medium"
                                            title="Mute Similar Errors"
                                        >
                                            🔇
                                        </button>
                                    @elseif($error->is_resolved)
                                        <button 
                                            onclick="reopenError({{ $error->id }})"
                                            class="text-orange-600 hover:text-orange-900 font-medium"
                                            title="Reopen"
                                        >
                                            ↺
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    No errors found. Great job! 🎉
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $errors->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    function resolveError(id) {
        if (confirm('Resolve this error?')) {
            fetch(`/api/v1/admin/system-health/errors/${id}/resolve`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${document.querySelector('meta[name="api-token"]')?.content || ''}`,
                    'Content-Type': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to resolve error');
                }
            })
            .catch(e => alert('Error: ' + e.message));
        }
    }

    function reopenError(id) {
        if (confirm('Reopen this error?')) {
            fetch(`/api/v1/admin/system-health/errors/${id}/reopen`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${document.querySelector('meta[name="api-token"]')?.content || ''}`,
                    'Content-Type': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to reopen error');
                }
            })
            .catch(e => alert('Error: ' + e.message));
        }
    }

    function muteError(id) {
        if (confirm('Mute all errors with this signature?')) {
            fetch(`/api/v1/admin/system-health/errors/${id}/mute`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${document.querySelector('meta[name="api-token"]')?.content || ''}`,
                    'Content-Type': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to mute error');
                }
            })
            .catch(e => alert('Error: ' + e.message));
        }
    }
</script>
@endsection
