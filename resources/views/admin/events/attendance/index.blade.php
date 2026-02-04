@extends('layouts.admin')

@section('title', 'Event Attendance - ' . $event->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
            <p class="text-gray-600 mt-1">Check-in & Attendance</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:text-blue-700">← Back to Events</a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm mb-1">Total Registered</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_registered'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm mb-1">Attended</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['attended_count'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm mb-1">Attendance Rate</p>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['attendance_rate'] }}%</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm mb-1">Pending</p>
            <p class="text-3xl font-bold text-amber-600">{{ $stats['total_registered'] - $stats['attended_count'] }}</p>
        </div>
    </div>

    <!-- QR Scanner Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">QR Scanner</h2>
                <p class="text-gray-600 text-sm mb-4">Scan registration QR codes to mark attendance</p>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Registration Code</label>
                    <input type="text" id="scanInput" placeholder="REG-XXXXXXXX" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600"
                        autocomplete="off">
                </div>

                <button onclick="scanCode()" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Check-in
                </button>

                <!-- Result Message -->
                <div id="scanResult" class="mt-4 hidden"></div>
            </div>
        </div>

        <!-- Recent Attendees -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Check-ins</h2>
                <div id="recentList" class="space-y-2 max-h-96 overflow-y-auto">
                    <p class="text-gray-600 text-center py-4">No check-ins yet</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Registrations List -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">All Registrations</h2>
            <a href="{{ route('admin.events.attendance.report', $event) }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                View Full Report →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Registered</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($registrations as $reg)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm font-mono text-gray-900">{{ $reg->registration_code }}</td>
                        <td class="px-6 py-3 text-sm text-gray-900">{{ $reg->user?->name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $reg->user?->email }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $reg->registered_at?->format('M d, H:i') }}</td>
                        <td class="px-6 py-3 text-sm">
                            @if($reg->attendanceLogs()->exists())
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">✓ Attended</span>
                            @else
                            <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t">
            {{ $registrations->links() }}
        </div>
    </div>
</div>

<script>
function scanCode() {
    const code = document.getElementById('scanInput').value.trim();
    if (!code) return;

    fetch('{{ route("admin.events.attendance.scan", $event) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ qr_code: code }),
    })
    .then(r => r.json())
    .then(data => {
        const resultDiv = document.getElementById('scanResult');
        resultDiv.classList.remove('hidden', 'bg-red-50', 'border-red-200', 'text-red-700', 'bg-green-50', 'border-green-200', 'text-green-700');

        if (data.success) {
            resultDiv.classList.add('bg-green-50', 'border-green-200', 'text-green-700', 'border', 'rounded', 'p-3');
            resultDiv.innerHTML = `<p><strong>✓ Check-in successful!</strong></p><p>${data.user}</p>`;
            
            // Add to recent list
            const recentList = document.getElementById('recentList');
            if (recentList.querySelector('.text-gray-600')) {
                recentList.innerHTML = '';
            }
            const item = document.createElement('div');
            item.className = 'flex justify-between items-center p-3 bg-green-50 rounded';
            item.innerHTML = `<span>${data.user}</span><span class="text-xs text-gray-600">${new Date().toLocaleTimeString()}</span>`;
            recentList.prepend(item);
        } else {
            resultDiv.classList.add('bg-red-50', 'border-red-200', 'text-red-700', 'border', 'rounded', 'p-3');
            resultDiv.innerHTML = `<p><strong>✗ ${data.message}</strong></p>`;
        }

        document.getElementById('scanInput').value = '';
        document.getElementById('scanInput').focus();
    })
    .catch(err => console.error(err));
}

// Focus on input when page loads
document.getElementById('scanInput').focus();

// Allow Enter key to scan
document.getElementById('scanInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') scanCode();
});
</script>
@endsection
