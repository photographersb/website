@extends('emails.layout')

@section('content')
    <h1>📅 New Booking Received!</h1>
    
    <p>Hi <strong>{{ $photographer->user->name }}</strong>,</p>
    
    <p>Great news! You have received a new booking request.</p>
    
    <div class="info-box">
        <p><strong>Booking Details:</strong></p>
        <p><strong>Client:</strong> {{ $booking->client->name }}</p>
        <p><strong>Event Type:</strong> {{ $booking->event_type }}</p>
        <p><strong>Date:</strong> {{ $booking->event_date->format('F d, Y') }}</p>
        <p><strong>Time:</strong> {{ $booking->event_time }}</p>
        <p><strong>Location:</strong> {{ $booking->event_location }}</p>
        <p><strong>Package:</strong> {{ $booking->package->name ?? 'Custom' }}</p>
        <p><strong>Amount:</strong> ৳{{ number_format($booking->total_amount, 2) }}</p>
    </div>
    
    <p>Please review the booking details and respond to the client as soon as possible.</p>
    
    <a href="{{ config('app.url') }}/photographer/bookings/{{ $booking->id }}" class="button">View Booking</a>
    
    <div class="divider"></div>
    
    <p><strong>Quick Actions:</strong></p>
    <p>✅ Confirm the booking if you're available</p>
    <p>📧 Contact the client to discuss details</p>
    <p>📝 Update booking status in your dashboard</p>
    
    <p>Best regards,<br>
    <strong>The Photographer SB Team</strong></p>
@endsection
