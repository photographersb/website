@extends('emails.layout')

@section('content')
    <h1>📬 New Quote Received!</h1>
    
    <p>Hi <strong>{{ $client->name }}</strong>,</p>
    
    <p>Excellent news! <strong>{{ $photographer->name }}</strong> has sent you a quote for your photography project.</p>
    
    <div class="info-box">
        <p><strong>Quote Summary:</strong></p>
        <p>
            📸 <strong>Photographer:</strong> {{ $photographer->name }}
            @if($photographer->rating)
                <span style="color: #ffc107;">★ {{ $photographer->rating }}/5</span>
            @endif
            <br>
            💰 <strong>Quote Amount:</strong> ৳ {{ number_format($quote->amount, 0) }}<br>
            📌 <strong>Service:</strong> {{ $quote->service_type ?? 'Photography' }}<br>
            ⏰ <strong>Valid Until:</strong> {{ $expiresAt->format('M d, Y') }}
        </p>
    </div>
    
    @if($quote->description)
        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 4px; margin: 20px 0;">
            <p><strong>Message from Photographer:</strong></p>
            <p style="color: #555;">{{ $quote->description }}</p>
        </div>
    @endif
    
    <a href="{{ $quoteUrl }}" class="button">View Full Quote</a>
    
    <div class="divider"></div>
    
    <h2 style="color: #8B1538; font-size: 18px; margin-bottom: 15px;">Next Steps:</h2>
    <p>🔍 Review the photographer's portfolio<br>
    💬 Message the photographer if you have questions<br>
    ✅ Accept the quote to proceed with booking<br>
    ⏳ Quote expires on {{ $expiresAt->format('M d, Y') }}</p>
    
    <div class="divider"></div>
    
    <p>Have questions about this quote? Contact the photographer directly or reach out to us at support@photographar.com.</p>
@endsection
