@extends('layouts.admin')

@section('title', 'Admin Sitemap')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">📍 Admin Sitemap</h1>
        <p class="text-gray-600 text-lg">Complete navigation map of all admin pages and features</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <p class="text-gray-600 text-sm font-semibold uppercase">Total Sections</p>
            <p class="text-3xl font-bold text-blue-600">{{ count($groupedLinks) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <p class="text-gray-600 text-sm font-semibold uppercase">Total Pages</p>
            <p class="text-3xl font-bold text-green-600">{{ $totalLinks }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <p class="text-gray-600 text-sm font-semibold uppercase">Quick Access</p>
            <p class="text-3xl font-bold text-purple-600">✓</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-burgundy-600">
            <p class="text-gray-600 text-sm font-semibold uppercase">Organized</p>
            <p class="text-3xl font-bold text-burgundy-600">✓</p>
        </div>
    </div>

    <!-- Navigation Tree -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-xl font-bold text-gray-900">📂 Admin Navigation Structure</h2>
            <p class="text-sm text-gray-600 mt-1">Click any section to expand and view available pages</p>
        </div>

        <div class="divide-y divide-gray-200">
            @foreach($groupedLinks as $module => $moduleLinks)
                <div class="hover:bg-gray-50 transition-colors">
                    <!-- Module Header -->
                    <div class="px-6 py-4 cursor-pointer flex items-center justify-between" onclick="toggleModule('{{ $module }}')">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">{{ getModuleIcon($module) }}</span>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">{{ $module }}</h3>
                                <p class="text-sm text-gray-500">{{ count($moduleLinks) }} pages available</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ count($moduleLinks) }}
                            </span>
                            <svg id="icon-{{ $module }}" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Module Links -->
                    <div id="content-{{ $module }}" class="hidden bg-gray-50">
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($moduleLinks as $link)
                                    <a href="{{ $link['url'] }}" 
                                       class="block p-4 bg-white border border-gray-200 rounded-lg hover:border-burgundy-500 hover:shadow-md transition-all group">
                                        <div class="flex items-start gap-3">
                                            <span class="text-burgundy-600 mt-1">🔗</span>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-semibold text-gray-900 group-hover:text-burgundy-600 transition-colors truncate">
                                                    {{ $link['link_name'] }}
                                                </h4>
                                                <p class="text-xs text-gray-500 font-mono mt-1 truncate">{{ $link['url'] }}</p>
                                                @if($link['route_name'])
                                                    <p class="text-xs text-gray-400 mt-1 truncate">Route: {{ $link['route_name'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@php
function getModuleIcon($module) {
    $icons = [
        'Dashboard' => '📊',
        'Users' => '👥',
        'Roles' => '🔐',
        'Photographers' => '📸',
        'Bookings' => '📅',
        'Events' => '🎉',
        'Competitions' => '🏆',
        'Sponsors' => '🤝',
        'Mentors' => '👨‍🏫',
        'Judges' => '⚖️',
        'Notices' => '📢',
        'SEO' => '🔍',
        'Settings' => '⚙️',
        'System Health' => '💚',
        'Error Logs' => '📋',
    ];
    return $icons[$module] ?? '📁';
}
@endphp

<script>
function toggleModule(module) {
    const content = document.getElementById('content-' + module);
    const icon = document.getElementById('icon-' + module);
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

// Expand first module by default
document.addEventListener('DOMContentLoaded', function() {
    @if(count($groupedLinks) > 0)
        toggleModule('{{ array_key_first($groupedLinks) }}');
    @endif
});
</script>
@endsection
