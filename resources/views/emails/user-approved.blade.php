<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #8B1538 0%, #6F112D 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; background: #8B1538; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
        .success-icon { font-size: 48px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">✓</div>
            <h1>Account Approved!</h1>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p><strong>Great news!</strong> Your account registration on <strong>Photographar SB</strong> has been approved by our admin team.</p>
            
            <p>You can now log in and access all features of the platform:</p>
            
            <ul>
                @if($user->role === 'photographer')
                <li>Create and manage your professional portfolio</li>
                <li>Accept booking requests from clients</li>
                <li>Participate in competitions and events</li>
                <li>Showcase your photography work</li>
                @elseif($user->role === 'client')
                <li>Browse and hire professional photographers</li>
                <li>Book photography services</li>
                <li>Review and rate photographers</li>
                <li>Manage your bookings</li>
                @else
                <li>Access all platform features</li>
                <li>Connect with photographers and clients</li>
                <li>Explore competitions and events</li>
                @endif
            </ul>
            
            <div style="text-align: center;">
                <a href="{{ config('app.frontend_url') ?? 'http://127.0.0.1:8000' }}/login" class="button">
                    Login to Your Account
                </a>
            </div>
            
            <p>If you have any questions or need assistance, feel free to contact our support team.</p>
            
            <p>Welcome to Photographar SB!</p>
            
            <p>Best regards,<br>
            <strong>Photographar SB Team</strong></p>
        </div>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Photographar SB. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
