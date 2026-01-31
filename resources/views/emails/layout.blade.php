<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Photographer SB' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; padding: 20px; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .email-header { background: linear-gradient(135deg, #8B1538 0%, #6F112D 100%); padding: 40px 30px; text-align: center; }
        .logo { color: #ffffff; font-size: 28px; font-weight: bold; margin-bottom: 10px; }
        .tagline { color: #f0f0f0; font-size: 14px; }
        .email-body { padding: 40px 30px; color: #333333; line-height: 1.6; }
        .email-body h1 { color: #8B1538; font-size: 24px; margin-bottom: 20px; }
        .email-body p { margin-bottom: 15px; font-size: 16px; }
        .button { display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #8B1538 0%, #6F112D 100%); color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; transition: transform 0.2s; }
        .button:hover { transform: translateY(-2px); }
        .info-box { background-color: #f9f9f9; border-left: 4px solid #8B1538; padding: 15px 20px; margin: 20px 0; border-radius: 4px; }
        .divider { height: 1px; background: linear-gradient(to right, transparent, #e0e0e0, transparent); margin: 30px 0; }
        .email-footer { background-color: #f9f9f9; padding: 30px; text-align: center; color: #666666; font-size: 13px; }
        .social-links { margin: 15px 0; }
        .social-links a { display: inline-block; margin: 0 10px; color: #8B1538; text-decoration: none; }
        .footer-links { margin-top: 15px; }
        .footer-links a { color: #8B1538; text-decoration: none; margin: 0 10px; }
        .footer-note { margin-top: 15px; color: #999999; font-size: 12px; }
        @media only screen and (max-width: 600px) {
            .email-body { padding: 30px 20px; }
            .email-header { padding: 30px 20px; }
            .button { display: block; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">📸 Photographer SB</div>
            <div class="tagline">Bangladesh's Premier Photography Marketplace</div>
        </div>

        <!-- Body -->
        <div class="email-body">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="social-links">
                <strong>Connect with us:</strong><br>
                <a href="https://facebook.com/photographersb">Facebook</a> |
                <a href="https://instagram.com/photographersb">Instagram</a> |
                <a href="https://twitter.com/photographersb">Twitter</a>
            </div>
            
            <div class="divider"></div>
            
            <div class="footer-links">
                <a href="{{ config('app.url') }}">Home</a> |
                <a href="{{ config('app.url') }}/about">About Us</a> |
                <a href="{{ config('app.url') }}/help">Help Center</a> |
                <a href="{{ config('app.url') }}/contact">Contact</a>
            </div>
            
            <div class="footer-note">
                <p>© {{ date('Y') }} Photographer SB. All rights reserved.</p>
                <p>A project by <a href="https://somogrobangladesh.com" style="color: #8B1538;">Somogro Bangladesh</a></p>
                <p style="margin-top: 10px;">You received this email because you registered at Photographer SB.</p>
            </div>
        </div>
    </div>
</body>
</html>
