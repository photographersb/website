@extends('layouts.admin')

@section('title', 'Mobile QR Scanner - ' . $event->title)

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-600 to-blue-800 py-4">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-6 text-white">
            <h1 class="text-2xl font-bold">{{ $event->title }}</h1>
            <p class="text-blue-100 text-sm">Check-in & Attendance</p>
        </div>

        <!-- Stats Card -->
        <div class="grid grid-cols-3 gap-2 mb-6">
            <div class="bg-white bg-opacity-20 backdrop-blur rounded-lg p-3 text-white text-center">
                <p class="text-2xl font-bold">{{ $stats['attended_count'] }}</p>
                <p class="text-xs text-blue-100">Attended</p>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur rounded-lg p-3 text-white text-center">
                <p class="text-2xl font-bold">{{ $stats['total_registered'] }}</p>
                <p class="text-xs text-blue-100">Registered</p>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur rounded-lg p-3 text-white text-center">
                <p class="text-2xl font-bold">{{ $stats['attendance_rate'] }}%</p>
                <p class="text-xs text-blue-100">Rate</p>
            </div>
        </div>

        <!-- Camera Preview or Input -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Scan QR Code</h2>
            
            <!-- Scanner Input (for mobile camera) -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Camera Input</label>
                <video id="scanner" class="w-full border-2 border-gray-300 rounded mb-4" style="max-height: 300px;"></video>
                <button onclick="toggleCamera()" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition mb-4">
                    <span id="cameraBtn">📷 Start Camera</span>
                </button>
            </div>

            <!-- Manual Input Fallback -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Or Enter Code Manually</label>
                <input type="text" id="manualInput" placeholder="REG-XXXXXXXX" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600"
                    autocomplete="off" autocapitalize="on">
            </div>

            <button onclick="checkIn()" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition text-lg">
                ✓ Check In
            </button>

            <!-- Result Message -->
            <div id="scanResult" class="mt-4 hidden p-4 rounded-lg"></div>
        </div>

        <!-- Recent Check-ins -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Check-ins</h2>
            <div id="recentList" class="space-y-2 max-h-96 overflow-y-auto">
                <p class="text-gray-600 text-center py-4">No check-ins yet</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
let cameraActive = false;
let stream = null;

async function toggleCamera() {
    const btn = document.getElementById('cameraBtn');
    
    if (cameraActive) {
        // Stop camera
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        cameraActive = false;
        btn.textContent = '📷 Start Camera';
        document.getElementById('scanner').style.display = 'none';
    } else {
        // Start camera
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' } 
            });
            const video = document.getElementById('scanner');
            video.srcObject = stream;
            video.style.display = 'block';
            cameraActive = true;
            btn.textContent = '⏹ Stop Camera';
            
            // Start scanning
            scanQRCode();
        } catch (err) {
            alert('Camera access denied or not available');
        }
    }
}

function scanQRCode() {
    if (!cameraActive) return;

    const video = document.getElementById('scanner');
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    ctx.drawImage(video, 0, 0);
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const code = jsQR(imageData.data, imageData.width, imageData.height);

    if (code) {
        // QR code found
        const qrData = code.data;
        document.getElementById('manualInput').value = qrData;
        checkIn();
        stream.getTracks().forEach(track => track.stop());
        cameraActive = false;
        document.getElementById('scanner').style.display = 'none';
        document.getElementById('cameraBtn').textContent = '📷 Start Camera';
    } else {
        // Keep scanning
        setTimeout(scanQRCode, 300);
    }
}

function checkIn() {
    const code = document.getElementById('manualInput').value.trim();
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
            resultDiv.classList.add('bg-green-50', 'border-green-200', 'text-green-700', 'border');
            resultDiv.innerHTML = `<p class="font-bold text-lg">✓ Check-in successful!</p><p class="text-sm">${data.user}</p>`;
            
            // Add to recent list
            const recentList = document.getElementById('recentList');
            if (recentList.querySelector('.text-gray-600')) {
                recentList.innerHTML = '';
            }
            const item = document.createElement('div');
            item.className = 'flex justify-between items-center p-3 bg-green-50 rounded border border-green-200';
            item.innerHTML = `<span class="font-semibold text-gray-900">${data.user}</span><span class="text-xs text-gray-600">${new Date().toLocaleTimeString()}</span>`;
            recentList.prepend(item);
            
            // Update stats
            location.reload();
        } else {
            resultDiv.classList.add('bg-red-50', 'border-red-200', 'text-red-700', 'border');
            resultDiv.innerHTML = `<p class="font-bold">✗ ${data.message}</p>`;
        }

        document.getElementById('manualInput').value = '';
        document.getElementById('manualInput').focus();
    })
    .catch(err => console.error(err));
}

// Focus on input when page loads
document.getElementById('manualInput').focus();

// Allow Enter key to check in
document.getElementById('manualInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') checkIn();
});
</script>

<style>
    video {
        transform: scaleX(-1);
        -webkit-transform: scaleX(-1);
    }
</style>
@endsection
