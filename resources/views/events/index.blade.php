@extends('layouts.app')

@section('title', 'Events & Workshops')

@section('content')
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12 mb-12">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Events & Workshops</h1>
        <p class="text-xl text-blue-100">Discover and register for photography events in your city</p>
    </div>
</div>

<div class="container mx-auto px-4 pb-12">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Filters Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Filters</h3>

                <!-- Search -->
                <form method="GET" action="{{ route('events.index') }}" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Event name..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600">
                    </div>

                    <!-- City Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                        <select name="city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600">
                            <option value="">All Cities</option>
                            @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Event Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="type" value="" {{ request('type') === null ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-700">All Types</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="type" value="free" {{ request('type') === 'free' ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-700">Free</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="type" value="paid" {{ request('type') === 'paid' ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-700">Paid</span>
                            </label>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">When</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="when" value="" {{ request('when') === null ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-700">Any Time</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="when" value="upcoming" {{ request('when') === 'upcoming' ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-700">Upcoming</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="when" value="this_month" {{ request('when') === 'this_month' ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-700">This Month</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="when" value="this_week" {{ request('when') === 'this_week' ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-700">This Week</span>
                            </label>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Price</label>
                        <select name="price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600">
                            <option value="">Any Price</option>
                            <option value="free" {{ request('price') === 'free' ? 'selected' : '' }}>Free</option>
                            <option value="under_1000" {{ request('price') === 'under_1000' ? 'selected' : '' }}>Under ৳1,000</option>
                            <option value="1000_5000" {{ request('price') === '1000_5000' ? 'selected' : '' }}>৳1,000 - ৳5,000</option>
                            <option value="over_5000" {{ request('price') === 'over_5000' ? 'selected' : '' }}>Over ৳5,000</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sort By</label>
                        <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600">
                            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="soonest" {{ request('sort') === 'soonest' ? 'selected' : '' }}>Soonest First</option>
                            <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Filter
                        </button>
                        <a href="{{ route('events.index') }}" class="flex-1 bg-gray-200 text-gray-900 py-2 rounded-lg font-semibold hover:bg-gray-300 transition text-center">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Events List -->
        <div class="lg:col-span-3">
            <!-- Result Count -->
            <div class="mb-4 flex justify-between items-center">
                <p class="text-gray-600">
                    @if($events->total() > 0)
                    Showing {{ $events->count() }} of {{ $events->total() }} events
                    @else
                    No events found
                    @endif
                </p>
                <div class="flex gap-2">
                    <a href="?view=grid" class="p-2 {{ request('view', 'grid') === 'grid' ? 'bg-blue-100 text-blue-600' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM12 4a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V4zM3 13a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1v-4zM12 13a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                        </svg>
                    </a>
                    <a href="?view=list" class="p-2 {{ request('view') === 'list' ? 'bg-blue-100 text-blue-600' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- No Results -->
            @if($events->isEmpty())
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No events found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your filters or search terms</p>
                <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Clear all filters</a>
            </div>
            @else

            <!-- Grid View -->
            @if(request('view') !== 'list')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($events as $event)
                <a href="{{ route('events.show', $event) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden group">
                    <!-- Image -->
                    <div class="relative h-40 bg-gradient-to-br from-blue-400 to-blue-600 overflow-hidden">
                        @if($event->banner_image_path)
                        <img src="{{ asset('storage/' . $event->banner_image_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                        @endif
                        <!-- Type Badge -->
                        <div class="absolute top-3 right-3">
                            @if($event->event_type === 'free')
                            <span class="inline-block px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold">FREE</span>
                            @else
                            <span class="inline-block px-3 py-1 bg-blue-500 text-white rounded-full text-xs font-semibold">PAID</span>
                            @endif
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600">{{ $event->title }}</h3>
                        
                        <!-- Price -->
                        <div class="mb-3">
                            @if($event->event_type === 'free')
                            <p class="text-xl font-bold text-green-600">FREE</p>
                            @else
                            <p class="text-xl font-bold text-blue-600">৳{{ number_format($event->price, 0) }}</p>
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.5 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM12.5 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM17.5 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                </svg>
                                {{ $event->start_datetime?->format('M d, Y') }}
                            </p>
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L8.59 4.59A7 7 0 005.05 4.05zM8 16a8 8 0 100-16 8 8 0 000 16zm3.72-9.47a.75.75 0 10-1.06-1.06L9 9.94 7.28 8.22a.75.75 0 00-1.06 1.06l2 2a.75.75 0 001.06 0l3-3z" clip-rule="evenodd" />
                                </svg>
                                {{ $event->city?->name }}
                            </p>
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 7H7v6h6V7z" />
                                    <path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2V2a1 1 0 112 0v1h1a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2h1a2 2 0 01-2 2h-1v1a1 1 0 11-2 0v-1h-2v1a1 1 0 11-2 0v-1H7a2 2 0 01-2-2v-1H4a1 1 0 110-2h1V9H4a1 1 0 110-2h1V5a2 2 0 012-2h1V2zM9 5a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                </svg>
                                @php
                                    $registered = $event->registrations()->count();
                                @endphp
                                {{ $registered }} registered
                            </p>
                        </div>

                        <!-- CTA -->
                        <button class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                            View Event →
                        </button>
                    </div>
                </a>
                @endforeach
            </div>
            @else

            <!-- List View -->
            <div class="space-y-4">
                @foreach($events as $event)
                <a href="{{ route('events.show', $event) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 flex gap-4 group">
                    <!-- Thumbnail -->
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex-shrink-0 overflow-hidden">
                        @if($event->banner_image_path)
                        <img src="{{ asset('storage/' . $event->banner_image_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                        @endif
                    </div>
                    <!-- Content -->
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600">{{ $event->title }}</h3>
                            @if($event->event_type === 'free')
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">FREE</span>
                            @else
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">৳{{ number_format($event->price, 0) }}</span>
                            @endif
                        </div>
                        <p class="text-gray-600 text-sm mb-3">{{ Str::limit($event->description, 100) }}</p>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.5 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM12.5 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM17.5 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                </svg>
                                {{ $event->start_datetime?->format('M d, Y H:i') }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L8.59 4.59A7 7 0 005.05 4.05zM8 16a8 8 0 100-16 8 8 0 000 16zm3.72-9.47a.75.75 0 10-1.06-1.06L9 9.94 7.28 8.22a.75.75 0 00-1.06 1.06l2 2a.75.75 0 001.06 0l3-3z" clip-rule="evenodd" />
                                </svg>
                                {{ $event->city?->name }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                </svg>
                                @php $registered = $event->registrations()->count(); @endphp
                                {{ $registered }} registered
                            </span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif

            <!-- Pagination -->
            <div class="mt-8">
                {{ $events->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
