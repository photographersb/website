@extends('emails.layout')

@section('content')
    <h1>🔐 Reset Your Password</h1>
    
    <p>Hi <strong>{{ $user->name }}</strong>,</p>
    
    <p>We received a request to reset your password for your Photographer SB account.</p>
    
    <p>Click the button below to create a new password:</p>
    
    <a href="{{ $resetUrl }}" class="button">Reset Password</a>
    
    <p style="font-size: 14px; color: #666;">Or copy and paste this link into your browser:<br>
    <span style="color: #8B1538;">{{ $resetUrl }}</span></p>
    
    <div class="info-box">
        <p><strong>⏰ This link will expire in 60 minutes.</strong></p>
    </div>
    
    <div class="divider"></div>
    
    <p><strong>Didn't request a password reset?</strong></p>
    <p>No worries! Your account is secure. Simply ignore this email and no changes will be made.</p>
    
    <p>Best regards,<br>
    <strong>The Photographer SB Team</strong></p>
@endsection
