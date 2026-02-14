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
                <a href="{{ route('categories.index') }}" class="text-gray-500 hover:text-primary-700">Categories</a>
                <span class="text-gray-400">›</span>
                <span class="text-gray-900 font-semibold">{{ $category->name }}</span>
            </nav>
        </div>
    </div>

    {{-- Category Header --}}
    <div class="bg-white py-12 border-b border-gray-200">
        <div class="container mx-auto px-4 md:px-6 lg:px-8">
            <div class="max-w-4xl">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    {{ $category->name }} Photographers
                </h1>
                <p class="text-xl text-gray-600 mb-6">
                    {{ $photographers->total() }} verified photographers specializing in {{ strtolower($category->name) }}
                </p>
                @if($category->description)
                    <p class="text-gray-700 leading-relaxed">
                        {{ $category->description }}
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
                                <picture>
                                    <source srcset="{{ preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $photographer->user->profile_photo_url ?? '/placeholder-photographer.jpg') }}" type="image/webp">
                                    <img src="{{ $photographer->user->profile_photo_url ?? '/placeholder-photographer.jpg' }}" alt="{{ $photographer->user->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" loading="lazy">
                                </picture>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-primary-700">
                                {{ $photographer->user->name }}
                            </h3>
                            <p class="text-sm text-gray-600 mb-2">
                                📍 {{ $photographer->city ?? 'Bangladesh' }}
                            </p>
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
                <p class="text-gray-600 mb-6">There are currently no photographers in this category.</p>
                <a href="{{ route('categories.index') }}" class="inline-block px-6 py-3 bg-primary-700 text-white rounded-lg hover:bg-primary-800">
                    Browse All Categories
                </a>
            </div>
        @endif
    </div>

    {{-- Related Categories --}}
    <div class="bg-white py-12 border-t border-gray-200">
        <div class="container mx-auto px-4 md:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-6">Explore Other Categories</h2>
            <div class="flex flex-wrap gap-3">
                @php
                    $relatedCategories = \App\Models\Category::where('id', '!=', $category->id)->take(8)->get();
                @endphp
                @foreach($relatedCategories as $related)
                    <a 
                        href="{{ route('categories.show', $related->slug) }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-primary-100 text-gray-700 hover:text-primary-700 rounded-lg transition-colors font-medium"
                    >
                        {{ $related->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
