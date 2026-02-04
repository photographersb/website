@props([
    'type' => 'card',
    'count' => 3,
])

@php
    $skeletonBase = 'animate-pulse bg-gray-200 rounded';
@endphp

@if($type === 'card')
    <div class="space-y-4">
        @for($i = 0; $i < $count; $i++)
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <div class="animate-pulse">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="h-3 bg-gray-200 rounded"></div>
                        <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>

@elseif($type === 'list')
    <div class="space-y-3">
        @for($i = 0; $i < $count; $i++)
            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                <div class="animate-pulse flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gray-200 rounded"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-2 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>

@elseif($type === 'table')
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @for($i = 0; $i < 4; $i++)
                        <th class="px-6 py-3">
                            <div class="h-3 bg-gray-200 rounded w-20 animate-pulse"></div>
                        </th>
                    @endfor
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @for($i = 0; $i < $count; $i++)
                    <tr>
                        @for($j = 0; $j < 4; $j++)
                            <td class="px-6 py-4">
                                <div class="h-3 bg-gray-200 rounded animate-pulse"></div>
                            </td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

@elseif($type === 'profile')
    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
        <div class="animate-pulse">
            <div class="flex items-center space-x-6 mb-6">
                <div class="w-24 h-24 bg-gray-200 rounded-full"></div>
                <div class="flex-1 space-y-3">
                    <div class="h-6 bg-gray-200 rounded w-1/2"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                </div>
            </div>
            <div class="space-y-3">
                <div class="h-4 bg-gray-200 rounded"></div>
                <div class="h-4 bg-gray-200 rounded"></div>
                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
            </div>
        </div>
    </div>

@elseif($type === 'text')
    <div class="space-y-3 animate-pulse">
        @for($i = 0; $i < $count; $i++)
            <div class="h-4 bg-gray-200 rounded"></div>
        @endfor
        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
    </div>

@else
    <div class="animate-pulse">
        <div class="h-40 bg-gray-200 rounded"></div>
    </div>
@endif
