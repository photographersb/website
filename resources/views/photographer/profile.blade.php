@extends('layouts.public')

@section('meta')
    @if($seoMeta)
        {!! \App\Services\SeoService::class !!}
        <!-- Title -->
        <title>{{ $seoMeta->meta_title }}</title>
        
        <!-- Meta Description -->
        <meta name="description" content="{{ $seoMeta->meta_description }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="{{ $seoMeta->canonical_url }}">
        
        <!-- OpenGraph Tags -->
        <meta property="og:type" content="profile">
        <meta property="og:title" content="{{ $seoMeta->meta_title }}">
        <meta property="og:description" content="{{ $seoMeta->meta_description }}">
        <meta property="og:url" content="{{ $seoMeta->canonical_url }}">
        @if($seoMeta->og_image)
            <meta property="og:image" content="{{ $seoMeta->og_image }}">
            <meta property="og:image:alt" content="{{ $seoMeta->meta_title }}">
        @endif
        <meta property="og:site_name" content="Photographer SB">
        
        <!-- Twitter Card Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seoMeta->meta_title }}">
        <meta name="twitter:description" content="{{ $seoMeta->meta_description }}">
        @if($seoMeta->og_image)
            <meta name="twitter:image" content="{{ $seoMeta->og_image }}">
        @endif
        
        <!-- Robots Meta -->
        <meta name="robots" content="{{ $seoMeta->getRobotsValue() }}">
        
        <!-- Schema.org JSON-LD -->
        @if($seoMeta->schema_json)
            <script type="application/ld+json">
                {!! json_encode($seoMeta->schema_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
            </script>
        @endif
    @endif
@endsection

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Photographer Header -->
    <div class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Profile Picture -->
                <div class="flex-shrink-0">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full overflow-hidden border-4 border-burgundy shadow-lg">
                        <img 
                            :src="user.profile_photo_url || '/placeholder-photographer.jpg'" 
                            :alt="user.name"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>
                
                <!-- Header Info -->
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $user->name }}</h1>
                        @if($isVerified)
                            <svg class="w-6 h-6 md:w-8 md:h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                    
                    <p class="text-gray-600 mb-2">@{{ user.username }}</p>
                    
                    <!-- Rating -->
                    <div class="flex items-center gap-2 mb-3">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="font-bold text-gray-700">{{ round($averageRating, 1) }}</span>
                        <span class="text-gray-600">({{ $ratingCount }} {{ Str::plural('review', $ratingCount) }})</span>
                    </div>
                    
                    <!-- Location & Category -->
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                        @if($photographer?->district)
                            <span>📍 {{ $photographer->district }}</span>
                        @endif
                        @if($photographer?->specializations)
                            <span>📸 {{ $photographer->specializations }}</span>
                        @endif
                        @if($isAvailable)
                            <span class="text-green-600 font-semibold">✓ Available Now</span>
                        @endif
                    </div>
                </div>
                
                <!-- Share Buttons -->
                <div class="flex gap-2">
                    @include('photographer.partials.share-buttons', [
                        'user' => $user,
                        'seoMeta' => $seoMeta
                    ])
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bio Section -->
    @if($user->bio)
        <div class="bg-white border-b border-gray-100 py-6">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">About</h2>
                <p class="text-gray-700 leading-relaxed">{{ $user->bio }}</p>
            </div>
        </div>
    @endif
    
    <!-- Main Content Grid -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-3 gap-8">
            <!-- Portfolio -->
            <div class="col-span-2">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Portfolio</h2>
                <div class="grid grid-cols-3 gap-4">
                    @forelse($portfolios as $portfolio)
                        <div class="relative group overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-all">
                            <img 
                                :src="portfolio.image_url" 
                                :alt="portfolio.title"
                                class="w-full h-48 object-cover group-hover:scale-105 transition-transform"
                            >
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
                                <button class="opacity-0 group-hover:opacity-100 bg-burgundy text-white px-4 py-2 rounded-lg font-semibold transition-opacity">
                                    View
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-3 text-center text-gray-500 py-8">No portfolio items yet.</p>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                @if($portfolios->hasPages())
                    <div class="mt-8">
                        {{ $portfolios->links() }}
                    </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="col-span-1">
                <!-- Packages -->
                @if($packages->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Packages</h3>
                        <div class="space-y-4">
                            @foreach($packages as $package)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-burgundy transition-colors">
                                    <h4 class="font-semibold text-gray-900">{{ $package->name }}</h4>
                                    <p class="text-lg font-bold text-burgundy mt-2">৳{{ number_format($package->price) }}</p>
                                    <p class="text-sm text-gray-600 mt-2">{{ $package->description }}</p>
                                    <button class="w-full mt-3 bg-burgundy text-white py-2 rounded-lg font-semibold hover:bg-opacity-90 transition-all">
                                        Book Now
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Contact Section -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Contact</h3>
                    <div class="space-y-3">
                        @if($user->email)
                            <a href="mailto:{{ $user->email }}" class="block text-burgundy hover:underline">
                                📧 {{ $user->email }}
                            </a>
                        @endif
                        @if($user->phone)
                            <a href="tel:{{ $user->phone }}" class="block text-burgundy hover:underline">
                                📞 {{ $user->phone }}
                            </a>
                        @endif
                        <button class="w-full bg-burgundy text-white py-2 rounded-lg font-semibold hover:bg-opacity-90 transition-all">
                            Send Message
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reviews Section -->
    <div class="bg-white border-t border-gray-100 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Reviews</h2>
            
            @if($reviews->count() > 0)
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="border-b border-gray-100 pb-6">
                            <div class="flex items-start gap-4">
                                <img 
                                    :src="review.reviewer.profile_photo_url || '/placeholder.jpg'" 
                                    :alt="review.reviewer.name"
                                    class="w-12 h-12 rounded-full object-cover"
                                >
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $review->reviewer->name }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="text-sm text-gray-500 ml-2">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700 mt-2">{{ $review->comment }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($reviews->hasPages())
                    <div class="mt-8">
                        {{ $reviews->links() }}
                    </div>
                @endif
            @else
                <p class="text-center text-gray-500 py-8">No reviews yet. Be the first to review!</p>
            @endif
        </div>
    </div>
</div>
@endsection
