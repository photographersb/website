@extends('emails.layout')

@section('content')
    <h1>📄 Your Invoice is Ready</h1>
    
    <p>Hi <strong>{{ $recipientName }}</strong>,</p>
    
    <p>Your invoice for the photography service booking has been generated and attached to this email.</p>
    
    <div class="info-box">
        <p><strong>Invoice Summary:</strong></p>
        <p>
            📅 <strong>Event Date:</strong> {{ $booking->event_date->format('M d, Y') }}<br>
            💰 <strong>Total Amount:</strong> ৳ {{ number_format($booking->total_amount, 0) }}<br>
            📊 <strong>Payment Status:</strong> {{ ucfirst($booking->payment_status) }}<br>
            🔗 <strong>Booking Reference:</strong> {{ $booking->uuid }}
        </p>
    </div>
    
    <div class="divider"></div>
    
    <h2 style="color: #8B1538; font-size: 18px; margin-bottom: 15px;">Invoice Details</h2>
    <p>
        ✓ Invoice file attached as PDF<br>
        ✓ Please keep this for your records<br>
        @if(!isset($isPrintable) || !$isPrintable)
            ✓ Payment must be completed by the event date<br>
        @else
            ✓ Print this invoice for your records<br>
            ✓ Share with your team if needed<br>
        @endif
        ✓ VAT and taxes are included
    </p>
    
    <div class="divider"></div>
    
    <h2 style="color: #8B1538; font-size: 18px; margin-bottom: 15px;">Payment Methods</h2>
    <p>
        🏦 <strong>bKash:</strong> Available for instant payment<br>
        💳 <strong>Nagad:</strong> Quick and secure<br>
        🏪 <strong>Bank Transfer:</strong> For large amounts<br>
        💰 <strong>Other Options:</strong> Contact us for alternatives
    </p>
    
    <div class="divider"></div>
    
    @if(isset($isPrintable) && $isPrintable)
        <p><strong>Important Note:</strong> As the photographer, please ensure payment is received from the client before the event date. Contact the client if payment is not received within 5 days.</p>
    @else
        <p>If you have any questions about the invoice or payment, please don't hesitate to contact us at support@photographar.com.</p>
    @endif
    
    <div class="divider"></div>
    
    <p>Thank you for choosing Photographar! We look forward to your event.</p>
@endsection
