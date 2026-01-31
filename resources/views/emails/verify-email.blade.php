@extends('emails.layout')

@section('content')
    <h1>🎉 Welcome to Photographer SB!</h1>
    
    <p>Hi <strong>{{ $user->name }}</strong>,</p>
    
    <p>Thank you for joining Bangladesh's premier photography marketplace! We're excited to have you as part of our community.</p>
    
    <div class="info-box">
        <p><strong>Please verify your email address to activate your account.</strong></p>
    </div>
    
    <p>Click the button below to verify your email and start exploring:</p>
    
    <a href="{{ $verificationUrl }}" class="button">Verify Email Address</a>
    
    <p style="font-size: 14px; color: #666;">Or copy and paste this link into your browser:<br>
    <span style="color: #8B1538;">{{ $verificationUrl }}</span></p>
    
    <div class="divider"></div>
    
    <h2 style="color: #8B1538; font-size: 20px; margin-bottom: 15px;">What's Next?</h2>
    
    @if($user->role === 'photographer' || $user->role === 'studio_owner')
        <p>✨ <strong>Complete your profile</strong> - Add your portfolio, services, and pricing</p>
        <p>📸 <strong>Upload your best work</strong> - Showcase your photography skills</p>
        <p>💼 <strong>Start receiving bookings</strong> - Connect with clients looking for photographers</p>
    @else
        <p>🔍 <strong>Browse photographers</strong> - Discover talented photographers in your area</p>
        <p>📅 <strong>Book your photographer</strong> - Send inquiries and get quotes</p>
        <p>⭐ <strong>Leave reviews</strong> - Help others find great photographers</p>
    @endif
    
    <div class="divider"></div>
    
    <p>Need help getting started? Check out our <a href="{{ config('app.url') }}/help" style="color: #8B1538;">Help Center</a> or <a href="{{ config('app.url') }}/contact" style="color: #8B1538;">contact our support team</a>.</p>
    
    <p>Best regards,<br>
    <strong>The Photographer SB Team</strong></p>
@endsection
