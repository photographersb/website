@extends('emails.layout')

@section('content')
    <h1>🎉 Congratulations! You're a Winner!</h1>
    
    <p>Hi <strong>{{ $photographer->name }}</strong>,</p>
    
    <p>Excellent news! Your outstanding photography has been selected as a winner in the <strong>{{ $competition->title }}</strong> competition!</p>
    
    <div class="info-box" style="background: linear-gradient(135deg, #8B1538 0%, #6F112D 100%); border-left: none; color: white; text-align: center; padding: 30px;">
        <p style="font-size: 36px; margin: 0 0 10px 0;">
            @if($rank == 1)
                🥇
            @elseif($rank == 2)
                🥈
            @elseif($rank == 3)
                🥉
            @else
                ⭐
            @endif
        </p>
        <p style="font-size: 22px; margin: 0 0 5px 0; font-weight: bold;">
            @if($rank == 1)
                1st Place - Grand Winner
            @elseif($rank == 2)
                2nd Place Winner
            @elseif($rank == 3)
                3rd Place Winner
            @else
                Honorable Mention
            @endif
        </p>
        <p style="margin: 0; font-size: 14px;">{{ $awardType }}</p>
    </div>
    
    <div class="info-box">
        <p><strong>Award Details:</strong></p>
        <p>
            🏆 <strong>Rank:</strong> 
            @if($rank == 1)
                🥇 1st Place
            @elseif($rank == 2)
                🥈 2nd Place
            @elseif($rank == 3)
                🥉 3rd Place
            @else
                ⭐ Honorable Mention
            @endif
            <br>
            🎨 <strong>Competition:</strong> {{ $competition->title }}<br>
            💰 <strong>Prize Amount:</strong> ৳ {{ number_format($prizeAmount, 0) }}<br>
            ✓ <strong>Status:</strong> Verified & Approved
        </p>
    </div>
    
    <a href="{{ $certificateUrl }}" class="button">Download Your Certificate</a>
    
    <div style="text-align: center; margin-top: 15px;">
        <a href="{{ $leaderboardUrl }}" style="color: #8B1538; text-decoration: none; font-weight: 600;">View Leaderboard →</a>
    </div>
    
    <div class="divider"></div>
    
    <h2 style="color: #8B1538; font-size: 18px; margin-bottom: 15px;">🎊 What Happens Next?</h2>
    <p>✓ Your certificate is ready to download and share<br>
    ✓ Prize (৳ {{ number_format($prizeAmount, 0) }}) will be transferred to your account<br>
    ✓ You'll be featured on our leaderboard and winners page<br>
    ✓ Prize transfer typically completes within 3-5 business days</p>
    
    <div class="divider"></div>
    
    <h2 style="color: #8B1538; font-size: 18px; margin-bottom: 15px;">📢 Share Your Win!</h2>
    <p>Celebrate your achievement! Download your certificate and share it on your social media to showcase your incredible talent. Tag us on <a href="https://instagram.com/photographersb" style="color: #8B1538;">Instagram</a> and <a href="https://facebook.com/photographersb" style="color: #8B1538;">Facebook</a>!</p>
    
    <div class="divider"></div>
    
    <p>Thank you for being part of the Photographar community. Keep creating amazing work! 📸</p>
@endsection
