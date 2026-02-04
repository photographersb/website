@extends('layouts.public')

@section('head')
    <x-seo :meta="$seoMeta" />
@endsection

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Hero Section --}}
    <div class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white py-16 md:py-24">
        <div class="container mx-auto px-4 md:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                    Find Local Photographers
                </h1>
                <p class="text-xl md:text-2xl text-primary-100 mb-8">
                    Browse photographers in your city across Bangladesh
                </p>
            </div>
        </div>
    </div>

    {{-- Locations Grid --}}
    <div class="container mx-auto px-4 md:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($locations as $location)
                <a 
                    href="{{ route('locations.show', $location->slug) }}" 
                    class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border-2 border-gray-100 hover:border-primary-500"
                >
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 group-hover:text-primary-700 transition-colors">
                                {{ $location->name }}
                            </h3>
                            @if($location->district && $location->district != $location->name)
                                <p class="text-sm text-gray-500">{{ $location->district }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-primary-700 font-semibold">
                            {{ $location->photographers_count }} photographers
                        </span>
                        <svg class="w-5 h-5 text-primary-600 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Internal Linking Section --}}
    <div class="bg-white py-12 border-t border-gray-200">
        <div class="container mx-auto px-4 md:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl md:text-3xl font-bold mb-4">
                    Find Photographers by Category
                </h2>
                <p class="text-gray-600 mb-6">
                    Explore photographers based on their photography specializations
                </p>
                <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-700 text-white rounded-lg hover:bg-primary-800 transition-colors font-semibold">
                    Browse by Category
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
