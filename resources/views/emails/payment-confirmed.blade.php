@extends('emails.layout')

@section('content')
    <h1>✓ Payment Confirmed!</h1>
    
    <p>Hi <strong>{{ $transaction->user->name }}</strong>,</p>
    
    <p>Thank you! Your payment has been successfully processed. Here's your receipt:</p>
    
    <div class="info-box">
        <p><strong>Transaction Receipt:</strong></p>
        <p>
            Transaction ID: <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">{{ $transactionId }}</code><br>
            💰 <strong>Amount:</strong> ৳ {{ number_format($amount, 0) }}<br>
            🏦 <strong>Method:</strong> {{ ucfirst($paymentMethod) }}<br>
            📅 <strong>Date:</strong> {{ $transaction->created_at->format('M d, Y h:i A') }}<br>
            ✓ <strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">Completed</span>
        </p>
    </div>
    
    <a href="{{ $transactionUrl }}" class="button">View Transaction Details</a>
    
    <div class="divider"></div>
    
    <h2 style="color: #8B1538; font-size: 18px; margin-bottom: 15px;">Payment Summary</h2>
    <p>
        @if($transaction->description)
            {{ $transaction->description }}<br>
        @endif
        💰 Amount: ৳ {{ number_format($amount, 0) }}<br>
        📋 Reference: {{ $transactionId }}
    </p>
    
    <div class="divider"></div>
    
    <h2 style="color: #8B1538; font-size: 18px; margin-bottom: 15px;">Important Information</h2>
    <p>✓ Keep this receipt for your records<br>
    ✓ You'll receive a separate booking confirmation<br>
    ✓ Refunds are processed within 5-7 business days</p>
    
    <div class="divider"></div>
    
    <p>If you don't recognize this transaction, please contact us immediately at support@photographar.com.</p>
@endsection
