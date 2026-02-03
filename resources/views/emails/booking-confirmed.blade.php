@extends('emails.layout')

@section('content')
    <h1>✓ Booking Confirmed!</h1>
    
    <p>Hi <strong>{{ $client->name }}</strong>,</p>
    
    <p>Great news! Your booking with <strong>{{ $photographer->name }}</strong> has been confirmed!</p>
    
    <div class="info-box">
        <p><strong>Booking Details:</strong></p>
        <p>
            📸 <strong>Photographer:</strong> {{ $photographer->name }}<br>
            📅 <strong>Date:</strong> {{ $booking->event_date->format('M d, Y') }}<br>
            🕐 <strong>Time:</strong> {{ $booking->event_date->format('h:i A') }}<br>
            📍 <strong>Location:</strong> {{ $booking->location }}<br>
            💰 <strong>Amount:</strong> ৳ {{ number_format($booking->amount, 0) }}
        </p>
    </div>
    
    <a href="{{ $bookingUrl }}" class="button">View Booking Details</a>
    
    <div class="divider"></div>
    
    <h2 style="color: #8B1538; font-size: 18px; margin-bottom: 15px;">What's Next?</h2>
    <p>✨ Review the photographer's portfolio<br>
    🤝 Confirm event details with your photographer<br>
    💳 Complete payment before the event date<br>
    📸 Enjoy your photography session!</p>
    
    <div class="divider"></div>
    
    <p>Need to reschedule or have questions? Contact us at support@photographar.com or reply to this email.</p>
@endsection
