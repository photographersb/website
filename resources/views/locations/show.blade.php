@extends('layouts.public')

@section('head')
    <x-seo :meta="$seoMeta" />
@endsection

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Breadcrumbs --}}
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 md:px-6 lg:px-8 py-4">
            <nav class="flex items-center gap-2 text-sm">
                <a href="/" class="text-gray-500 hover:text-primary-700">Home</a>
                <span class="text-gray-400">›</span>
                <a href="{{ route('locations.index') }}" class="text-gray-500 hover:text-primary-700">Locations</a>
                <span class="text-gray-400">›</span>
                <span class="text-gray-900 font-semibold">{{ $city->name }}</span>
            </nav>
        </div>
    </div>

    {{-- Location Header --}}
    <div class="bg-white py-12 border-b border-gray-200">
        <div class="container mx-auto px-4 md:px-6 lg:px-8">
            <div class="max-w-4xl">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-10 h-10 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900">
                        Photographers in {{ $city->name }}
                    </h1>
                </div>
                <p class="text-xl text-gray-600 mb-6">
                    {{ $photographers->total() }} verified local photographers ready to capture your moments
                </p>
                @if($city->description)
                    <p class="text-gray-700 leading-relaxed">
                        {{ $city->description }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Photographers Grid --}}
    <div class="container mx-auto px-4 md:px-6 lg:px-8 py-12">
        @if($photographers->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
                @foreach($photographers as $photographer)
                    <a 
                        href="{{ url('/@' . $photographer->user->username) }}" 
                        class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden"
                    >
                        <div class="aspect-square overflow-hidden bg-gray-100">
                            <img 
                                src="{{ $photographer->user->profile_photo_url ?? '/placeholder-photographer.jpg' }}" 
                                alt="{{ $photographer->user->name }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                            >
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-primary-700">
                                {{ $photographer->user->name }}
                            </h3>
                            @if($photographer->specializations)
                                <p class="text-sm text-gray-600 mb-2">
                                    📸 {{ is_array($photographer->specializations) ? implode(', ', $photographer->specializations) : $photographer->specializations }}
                                </p>
                            @endif
                            @if($photographer->averageRating)
                                <div class="flex items-center gap-1 text-sm">
                                    <span class="text-yellow-400">★</span>
                                    <span class="font-semibold">{{ number_format($photographer->averageRating, 1) }}</span>
                                    <span class="text-gray-500">({{ $photographer->reviews()->count() }})</span>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            {{ $photographers->links() }}
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No photographers found</h3>
                <p class="text-gray-600 mb-6">There are currently no photographers in {{ $city->name }}.</p>
                <a href="{{ route('locations.index') }}" class="inline-block px-6 py-3 bg-primary-700 text-white rounded-lg hover:bg-primary-800">
                    Browse All Locations
                </a>
            </div>
        @endif
    </div>

    {{-- Nearby Locations --}}
    <div class="bg-white py-12 border-t border-gray-200">
        <div class="container mx-auto px-4 md:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-6">Nearby Cities</h2>
            <div class="flex flex-wrap gap-3">
                @php
                    $nearbyLocations = \App\Models\City::where('id', '!=', $city->id)->take(8)->get();
                @endphp
                @foreach($nearbyLocations as $nearby)
                    <a 
                        href="{{ route('locations.show', $nearby->slug) }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-primary-100 text-gray-700 hover:text-primary-700 rounded-lg transition-colors font-medium"
                    >
                        📍 {{ $nearby->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
