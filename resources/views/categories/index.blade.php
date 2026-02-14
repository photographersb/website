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
                    Browse by Photography Category
                </h1>
                <p class="text-xl md:text-2xl text-primary-100 mb-8">
                    Find specialized photographers for your exact needs
                </p>
            </div>
        </div>
    </div>

    {{-- Categories Grid --}}
    <div class="container mx-auto px-4 md:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($categories as $category)
                <a 
                    href="{{ route('categories.show', $category->slug) }}" 
                    class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border-2 border-gray-100 hover:border-primary-500"
                >
                    @if($category->image_url)
                        <div class="aspect-video overflow-hidden">
                            <img 
                                <picture>
                                    <source srcset="{{ preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $category->image_url) }}" type="image/webp">
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" loading="lazy">
                                </picture>
                        </div>
                    @else
                        <div class="aspect-video bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
                            <svg class="w-16 h-16 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-700 transition-colors">
                            {{ $category->name }}
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            {{ $category->description ?? 'Explore photographers specializing in ' . strtolower($category->name) }}
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-primary-700 font-semibold">
                                {{ $category->photographers_count }} photographers
                            </span>
                            <svg class="w-5 h-5 text-primary-600 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
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
                    Find Photographers by Location
                </h2>
                <p class="text-gray-600 mb-6">
                    Browse photographers in your city for convenient local services
                </p>
                <a href="{{ route('locations.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-700 text-white rounded-lg hover:bg-primary-800 transition-colors font-semibold">
                    Browse by Location
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
