@extends('layouts.app')

@section('title', $event->title . ' - Events')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header with Banner -->
    <div class="relative">
        @if($event->banner_image)
        <div class="h-96 bg-gradient-to-b from-gray-200 to-gray-100 relative overflow-hidden">
            <img src="{{ asset('storage/' . $event->banner_image) }}" alt="{{ $event->title }}" 
                class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        </div>
        @else
        <div class="h-64 bg-gradient-to-r from-burgundy-600 to-burgundy-700"></div>
        @endif

        <!-- Content Overlay -->
        <div class="max-w-7xl mx-auto px-4 -mt-20 relative z-10">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $event->title }}</h1>
                        <p class="text-gray-600 text-lg">{{ $event->venue_name ?? 'Venue TBD' }}</p>
                    </div>
                    <div class="text-right">
                        @if($event->event_type === 'free')
                        <div class="text-3xl font-bold text-green-600 mb-2">FREE</div>
                        @else
                        <div class="text-3xl font-bold text-burgundy-600 mb-2">৳ {{ number_format($event->price ?? 0, 0) }}</div>
                        @endif
                        <span class="inline-block px-3 py-1 bg-{{ $event->event_type === 'free' ? 'green' : 'burgundy' }}-100 text-{{ $event->event_type === 'free' ? 'green' : 'burgundy' }}-700 rounded-full text-sm font-medium">
                            {{ ucfirst($event->event_type) }} Event
                        </span>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6 pt-6 border-t">
                    <div>
                        <p class="text-gray-500 text-sm">START DATE</p>
                        <p class="text-lg font-semibold">{{ $event->start_datetime?->format('M d, Y') }}</p>
                        <p class="text-gray-600 text-sm">{{ $event->start_datetime?->format('H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">LOCATION</p>
                        <p class="text-lg font-semibold">{{ $event->city?->name }}</p>
                        <p class="text-gray-600 text-sm">{{ $event->venue_address ?? 'Address TBD' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">CAPACITY</p>
                        <p class="text-lg font-semibold">{{ $registeredCount }}/{{ $event->capacity ?? '∞' }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-burgundy-600 h-2 rounded-full" style="width: {{ $capacityPercent }}%"></div>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">REGISTRATION</p>
                        @if($event->registration_deadline)
                        <p class="text-lg font-semibold">{{ $event->registration_deadline->format('M d') }}</p>
                        <p class="text-gray-600 text-sm">{{ $regDaysLeft }} days left</p>
                        @else
                        <p class="text-lg font-semibold text-green-600">Open</p>
                        <p class="text-gray-600 text-sm">No deadline</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Details -->
            <div class="lg:col-span-2">
                <!-- Description -->
                <div class="bg-white rounded-lg shadow p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">About This Event</h2>
                    <div class="prose prose-lg max-w-none">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>

                <!-- Requirements -->
                @if($event->requirements)
                <div class="bg-white rounded-lg shadow p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Requirements</h2>
                    <div class="prose prose-lg max-w-none">
                        {!! nl2br(e($event->requirements)) !!}
                    </div>
                </div>
                @endif

                <!-- Mentors -->
                @if($event->mentors->count() > 0)
                <div class="bg-white rounded-lg shadow p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Mentors</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($event->mentors as $mentor)
                        <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $mentor->name }}</h3>
                                @if($mentor->email)
                                <p class="text-sm text-gray-600">{{ $mentor->email }}</p>
                                @endif
                                @if($mentor->specialization)
                                <p class="text-sm text-burgundy-600 mt-1">{{ $mentor->specialization }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Refund Policy -->
                @if($event->refund_policy)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-2">Refund Policy</h3>
                    <p class="text-gray-700 text-sm">{{ $event->refund_policy }}</p>
                </div>
                @endif
            </div>

            <!-- Right Column - Registration Card -->
            <div class="lg:col-span-1">
                <!-- Registration Card -->
                <div class="sticky top-4">
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Register Now</h3>

                        @if($capacityFull)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <p class="text-red-700 text-sm"><strong>Event Full</strong> - This event has reached capacity and registration is closed.</p>
                        </div>
                        @elseif($registrationClosed)
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                            <p class="text-orange-700 text-sm"><strong>Registration Closed</strong> - The registration deadline has passed.</p>
                        </div>
                        @elseif($alreadyRegistered)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <p class="text-green-700 text-sm"><strong>✓ You're Registered!</strong> Your registration code is <code class="font-mono text-sm">{{ $userRegistration->registration_code }}</code></p>
                        </div>
                        @else
                        <!-- Registration Form -->
                        <form method="POST" action="{{ route('events.register', $event) }}" class="space-y-4">
                            @csrf
                            
                            @if(!auth()->check())
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-blue-700 text-sm">You need to <a href="{{ route('login') }}" class="font-semibold underline">sign in</a> to register.</p>
                            </div>
                            @endif

                            @if($event->event_type === 'paid' && auth()->check())
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-gray-600 text-sm mb-2">Ticket Price:</p>
                                <p class="text-2xl font-bold text-burgundy-600">৳ {{ number_format($event->price ?? 0, 0) }}</p>
                            </div>
                            @endif

                            @if(auth()->check() && !$capacityFull && !$registrationClosed)
                            <button type="submit" class="w-full bg-burgundy-600 text-white py-3 rounded-lg font-semibold hover:bg-burgundy-700 transition">
                                @if($event->event_type === 'paid')
                                Proceed to Payment
                                @else
                                Confirm Registration
                                @endif
                            </button>
                            @endif
                        </form>
                        @endif

                        <div class="mt-6 pt-6 border-t border-gray-200 space-y-3 text-sm text-gray-600">
                            <div class="flex items-start">
                                <span class="text-burgundy-600 mr-2">✓</span>
                                <span>{{ $capacityFull ? 'Event at full capacity' : $registeredCount . ' people registered' }}</span>
                            </div>
                            @if($event->certificates_enabled)
                            <div class="flex items-start">
                                <span class="text-burgundy-600 mr-2">✓</span>
                                <span>Attendees receive certificate</span>
                            </div>
                            @endif
                            @if($event->registration_deadline)
                            <div class="flex items-start">
                                <span class="text-burgundy-600 mr-2">✓</span>
                                <span>Register by {{ $event->registration_deadline->format('M d, Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Share Section -->
                    <div class="bg-white rounded-lg shadow p-6 mt-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Share Event</h4>
                        <div class="flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                                target="_blank" class="flex-1 bg-blue-600 text-white py-2 rounded text-center text-sm hover:bg-blue-700">
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($event->title) }}" 
                                target="_blank" class="flex-1 bg-blue-400 text-white py-2 rounded text-center text-sm hover:bg-blue-500">
                                Twitter
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
