@extends('layouts.admin')

@section('title', 'Attendance Report - ' . $event->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
            <p class="text-gray-600 mt-1">Attendance Report</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('admin.events.attendance.export', $event) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="format" value="csv">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition">
                    Export CSV
                </button>
            </form>
            <a href="{{ route('admin.events.attendance.index', $event) }}" class="text-blue-600 hover:text-blue-700">← Back to Scanner</a>
        </div>
    </div>

    <!-- Summary -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-4 gap-4">
            <div>
                <p class="text-gray-600 text-sm">Event Date</p>
                <p class="text-lg font-semibold text-gray-900">{{ $event->start_datetime?->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Total Attendees</p>
                <p class="text-lg font-semibold text-gray-900">{{ $attendances->total() }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Attendance Rate</p>
                <p class="text-lg font-semibold text-green-600">
                    @php
                        $total = $event->registrations()->count();
                        $attended = $attendances->total();
                        $rate = $total > 0 ? round(($attended / $total) * 100, 1) : 0;
                    @endphp
                    {{ $rate }}%
                </p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Capacity</p>
                <p class="text-lg font-semibold text-gray-900">{{ $attended }} / {{ $event->capacity ?? '∞' }}</p>
            </div>
        </div>
    </div>

    <!-- Attendees Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-bold text-gray-900">Check-in History</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Registration Code</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Registered At</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Scanned At</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm font-mono text-gray-900">{{ $attendance->registration->registration_code }}</td>
                        <td class="px-6 py-3 text-sm text-gray-900">{{ $attendance->user->name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $attendance->user->email }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $attendance->registration->registered_at->format('M d, H:i:s') }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $attendance->scanned_at->format('M d, H:i:s') }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">
                            @php
                                $diff = $attendance->scanned_at->diffInMinutes($attendance->registration->registered_at);
                                if ($diff < 60) {
                                    echo "{$diff}m";
                                } else {
                                    $hours = intval($diff / 60);
                                    $mins = $diff % 60;
                                    echo "{$hours}h {$mins}m";
                                }
                            @endphp
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-600">
                            No attendees yet
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection
