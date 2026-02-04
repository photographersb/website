@extends('layouts.app')

@section('title', 'My Event Registrations')

@section('content')
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Event Registrations</h1>
        <p class="text-gray-600 mt-2">Manage your event registrations and tickets</p>
    </div>

    <!-- Tabs/Filters -->
    <div class="mb-6 flex gap-2 border-b">
        <a href="{{ route('events.my-registrations') }}" 
            class="px-4 py-3 font-semibold {{ !request('status') ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600' }}">
            All ({{ $registrations->total() }})
        </a>
        <a href="{{ route('events.my-registrations', ['status' => 'upcoming']) }}" 
            class="px-4 py-3 font-semibold {{ request('status') === 'upcoming' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600' }}">
            Upcoming
        </a>
        <a href="{{ route('events.my-registrations', ['status' => 'past']) }}" 
            class="px-4 py-3 font-semibold {{ request('status') === 'past' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600' }}">
            Past
        </a>
        <a href="{{ route('events.my-registrations', ['status' => 'attended']) }}" 
            class="px-4 py-3 font-semibold {{ request('status') === 'attended' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600' }}">
            Attended
        </a>
    </div>

    <!-- No Registrations -->
    @if($registrations->isEmpty())
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No registrations yet</h3>
        <p class="text-gray-600 mb-4">You haven't registered for any events. Explore events and join the photography community!</p>
        <a href="{{ route('events.index') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
            Browse Events
        </a>
    </div>
    @else

    <!-- Registrations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($registrations as $reg)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
            <!-- Event Image -->
            <div class="relative h-32 bg-gradient-to-br from-blue-400 to-blue-600 overflow-hidden">
                @if($reg->event->banner_image_path)
                <img src="{{ asset('storage/' . $reg->event->banner_image_path) }}" alt="{{ $reg->event->title }}" class="w-full h-full object-cover">
                @endif
                <!-- Status Badge -->
                <div class="absolute top-2 right-2">
                    @if(now() > $reg->event->start_datetime)
                    <span class="inline-block px-2 py-1 bg-gray-600 text-white rounded text-xs font-semibold">PAST</span>
                    @elseif($reg->payment_status === 'unpaid')
                    <span class="inline-block px-2 py-1 bg-amber-600 text-white rounded text-xs font-semibold">PENDING PAYMENT</span>
                    @else
                    <span class="inline-block px-2 py-1 bg-green-600 text-white rounded text-xs font-semibold">CONFIRMED</span>
                    @endif
                </div>
            </div>

            <!-- Content -->
            <div class="p-4">
                <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $reg->event->title }}</h3>

                <!-- Event Date & Location -->
                <div class="space-y-1 text-sm text-gray-600 mb-4">
                    <p class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.5 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM12.5 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM17.5 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                        </svg>
                        {{ $reg->event->start_datetime?->format('M d, Y H:i') }}
                    </p>
                    <p class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L8.59 4.59A7 7 0 005.05 4.05zM8 16a8 8 0 100-16 8 8 0 000 16zm3.72-9.47a.75.75 0 10-1.06-1.06L9 9.94 7.28 8.22a.75.75 0 00-1.06 1.06l2 2a.75.75 0 001.06 0l3-3z" clip-rule="evenodd" />
                        </svg>
                        {{ $reg->event->city?->name }}
                    </p>
                </div>

                <!-- Registration Code -->
                <div class="bg-gray-50 p-2 rounded mb-4">
                    <p class="text-xs text-gray-600 mb-1">Registration Code</p>
                    <code class="font-mono font-semibold text-gray-900 text-sm">{{ $reg->registration_code }}</code>
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    <a href="{{ route('events.show', $reg->event) }}" class="block text-center bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700 transition text-sm">
                        View Event
                    </a>

                    @if($reg->payment_status === 'unpaid')
                    <a href="{{ route('registrations.payment', $reg) }}" class="block text-center bg-amber-600 text-white py-2 rounded font-semibold hover:bg-amber-700 transition text-sm">
                        Complete Payment
                    </a>
                    @elseif(now() >= $reg->event->start_datetime && $reg->attendanceLogs()->exists())
                    <a href="{{ route('registrations.ticket', $reg) }}" class="block text-center bg-green-600 text-white py-2 rounded font-semibold hover:bg-green-700 transition text-sm">
                        Download Certificate
                    </a>
                    @else
                    <a href="{{ route('registrations.confirmation', $reg) }}" class="block text-center bg-gray-600 text-white py-2 rounded font-semibold hover:bg-gray-700 transition text-sm">
                        View Ticket
                    </a>
                    @endif
                </div>

                <!-- Attended Badge -->
                @if($reg->attendanceLogs()->exists())
                <div class="mt-3 p-2 bg-green-50 rounded border border-green-200">
                    <p class="text-xs text-green-700 font-semibold">✓ Attended on {{ $reg->attendanceLogs()->latest()->first()->scanned_at?->format('M d, Y') }}</p>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $registrations->links() }}
    </div>
    @endif
</div>
@endsection
