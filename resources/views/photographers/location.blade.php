@extends('layouts.app')

@section('head')
    <x-seo-head :seoMeta="$seoMeta" />
@endsection

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumbs -->
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <!-- Hero Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    Photographers in {{ $location->name }}
                </h1>
                <p class="text-lg text-gray-600">
                    Find professional photographers in {{ $location->name }}, Bangladesh
                </p>
                <div class="mt-4 text-sm text-gray-600">
                    <span>{{ $photographers->total() }} photographers available</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($photographers->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($photographers as $photographer)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                        <!-- Profile Picture -->
                        <div class="h-48 bg-gray-200 overflow-hidden">
                            @if($photographer->profile_picture)
                                <img src="{{ $photographer->profile_picture }}" alt="{{ $photographer->user->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-purple-500">
                                    <span class="text-white text-4xl font-bold">{{ substr($photographer->user->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="p-4">
                            <a href="{{ route('photographer.profile.public', ['username' => $photographer->user->username]) }}">
                                <h3 class="font-bold text-gray-900 hover:text-blue-600">{{ $photographer->user->name }}</h3>
                            </a>

                            <p class="text-sm text-gray-600 mt-1">@{{ $photographer->user->username }}</p>

                            @if($photographer->specializations)
                                <p class="text-xs text-gray-500 mt-2">
                                    {{ implode(', ', array_slice($photographer->specializations, 0, 2)) }}
                                </p>
                            @endif

                            <!-- Rating -->
                            @if($photographer->average_rating)
                                <div class="mt-2 flex items-center gap-1">
                                    <div class="flex">
                                        @for($i = 0; $i < 5; $i++)
                                            @if($i < floor($photographer->average_rating))
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-600">({{ $photographer->rating_count }})</span>
                                </div>
                            @endif

                            <!-- CTA Button -->
                            <a href="{{ route('photographer.profile.public', ['username' => $photographer->user->username]) }}" class="mt-4 block w-full text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition text-sm font-medium">
                                View Profile
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($photographers->hasPages())
                <div class="mt-12">
                    {{ $photographers->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <p class="text-gray-600 text-lg">No photographers found in {{ $location->name }}.</p>
            </div>
        @endif
    </div>
</div>
@endsection
