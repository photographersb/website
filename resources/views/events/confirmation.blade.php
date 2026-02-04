@extends('layouts.app')

@section('title', 'Registration Confirmed - ' . $event->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-4">
        <!-- Success Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Success Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-8 py-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Registration Confirmed!</h1>
                <p class="text-green-100">Your ticket is ready. See details below.</p>
            </div>

            <!-- Content -->
            <div class="px-8 py-8">
                <!-- Event Info -->
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $event->title }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">DATE</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $event->start_datetime?->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">LOCATION</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $event->city?->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Registration Details -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Registration</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Registration Code:</span>
                            <code class="font-mono font-semibold text-gray-900">{{ $registration->registration_code }}</code>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Registered At:</span>
                            <span class="text-gray-900">{{ $registration->registered_at?->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                                @if($registration->payment_status === 'free')
                                ✓ Confirmed
                                @elseif($registration->payment_status === 'paid')
                                ✓ Paid
                                @else
                                Pending Payment
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                @php
                    $qrUrl = null;
                    if ($registration->ticket_qr_path) {
                        $qrUrl = asset('storage/' . $registration->ticket_qr_path);
                    }
                @endphp
                
                @if($qrUrl)
                <div class="bg-gray-50 rounded-lg p-8 mb-8 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Ticket QR Code</h3>
                    <img src="{{ $qrUrl }}" alt="Ticket QR Code" 
                        class="w-48 h-48 mx-auto mb-4 border-2 border-blue-200 p-2 bg-white rounded">
                    <p class="text-gray-600 text-sm">Show this QR code at the event for check-in</p>
                </div>
                @else
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 text-center">
                    <p class="text-blue-700">Your QR code is being generated...</p>
                    <p class="text-blue-600 text-sm mt-2">Refresh this page in a few moments</p>
                </div>
                @endif

                <!-- What's Next -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-8">
                    <h4 class="font-semibold text-gray-900 mb-3">What's Next?</h4>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <span class="text-amber-600 mr-2 mt-1">→</span>
                            <span>Save your registration code: <code class="font-mono font-semibold">{{ $registration->registration_code }}</code></span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-amber-600 mr-2 mt-1">→</span>
                            <span>Screenshot or print your QR code for check-in</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-amber-600 mr-2 mt-1">→</span>
                            <span>Arrive 15 minutes early to the event</span>
                        </li>
                        @if($event->certificates_enabled)
                        <li class="flex items-start">
                            <span class="text-amber-600 mr-2 mt-1">→</span>
                            <span>Attend to receive your certificate</span>
                        </li>
                        @endif
                    </ul>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4">
                    <button onclick="window.print()" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Print Ticket
                    </button>
                    <a href="{{ route('events.show', $event) }}" class="flex-1 bg-gray-200 text-gray-900 py-3 rounded-lg font-semibold hover:bg-gray-300 transition text-center">
                        Back to Event
                    </a>
                </div>

                <!-- Organizer Info -->
                <div class="mt-8 pt-8 border-t text-center text-gray-600 text-sm">
                    <p>Organized by: <strong>{{ $event->organizer?->name ?? 'Photographer SB' }}</strong></p>
                    <p class="mt-2">Questions? Contact the organizer at the event.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
