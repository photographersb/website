<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .reason-box { background: #fee2e2; border-left: 4px solid #dc2626; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .button { display: inline-block; background: #8B1538; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
        .warning-icon { font-size: 48px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="warning-icon">✕</div>
            <h1>Account Registration Status</h1>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p>Thank you for your interest in <strong>Photographar SB</strong>. After reviewing your registration, we regret to inform you that we are unable to approve your account at this time.</p>
            
            <div class="reason-box">
                <strong>Reason:</strong><br>
                {{ $reason }}
            </div>
            
            <p><strong>What you can do:</strong></p>
            <ul>
                <li>Review the reason provided above</li>
                <li>Contact our support team if you have questions</li>
                <li>You may register again with updated information if appropriate</li>
            </ul>
            
            <div style="text-align: center;">
                <a href="{{ config('app.frontend_url') ?? 'http://127.0.0.1:8000' }}/contact" class="button">
                    Contact Support
                </a>
            </div>
            
            <p>We appreciate your understanding.</p>
            
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
