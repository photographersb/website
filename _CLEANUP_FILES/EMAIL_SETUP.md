# 📧 Email Configuration Guide for Photographer SB

## ✅ Email Templates Created

Beautiful, responsive email templates have been created for:

1. **Email Verification** - Welcome email with verification link
2. **Password Reset** - Secure password reset link
3. **Booking Created** - Notification for photographers
4. **Booking Status Updated** - Client notifications
5. **Payment Received** - Payment confirmation

All templates feature:
- 📱 Responsive design (mobile-friendly)
- 🎨 Branded colors (Burgundy gradient)
- 📸 Professional layout
- 🔗 Social media links
- 📍 Footer with company info

---

## 🔧 Email Provider Setup

### Option 1: Gmail (Easiest for Testing)

1. **Enable 2-Step Verification** in your Google Account
2. **Generate App Password**: https://myaccount.google.com/apppasswords
3. **Update `.env` on server**:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@photographersb.com
MAIL_FROM_NAME="Photographer SB"
```

### Option 2: SendGrid (Best for Production)

1. Sign up: https://sendgrid.com (Free: 100 emails/day)
2. Create API Key
3. **Update `.env`**:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@photographersb.com
MAIL_FROM_NAME="Photographer SB"
```

### Option 3: Mailtrap (For Testing Only)

1. Sign up: https://mailtrap.io (Free)
2. **Update `.env`**:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@photographersb.com
MAIL_FROM_NAME="Photographer SB"
```

---

## 📋 Setup Steps

### 1. Update `.env` on Server

```bash
cd /home/photogra/public_html
nano .env
```

Add/update email configuration from above (choose your provider).

### 2. Clear Config Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Test Email Sending

```bash
php artisan tinker
```

Then:
```php
Mail::raw('Test email from Photographer SB', function($message) {
    $message->to('your-test-email@gmail.com')
            ->subject('Test Email');
});
exit
```

### 4. Update Frontend URL

Make sure `.env` has:
```env
FRONTEND_URL=https://photographersb.com
```

This ensures verification links point to the correct domain.

---

## 🧪 Testing Email Verification

1. **Register new user** at https://photographersb.com
2. **Check inbox** for verification email
3. **Click verification link** - should redirect to frontend
4. **Login** - should work without manual DB update

---

## 🚀 Production Recommendations

### ✅ Use SendGrid (Recommended)
- Free 100 emails/day
- Professional email delivery
- Analytics & tracking
- No Gmail security blocks

### ✅ Configure SPF/DKIM Records
Add to your domain DNS:
- SPF: Authorize email servers
- DKIM: Email authentication
- DMARC: Email policy

### ✅ Monitor Email Delivery
- Check SendGrid dashboard
- Monitor bounce rates
- Track opens/clicks

---

## 📂 Files Created

```
resources/views/emails/
├── layout.blade.php          # Base email template
├── verify-email.blade.php    # Email verification
├── reset-password.blade.php  # Password reset
└── booking-created.blade.php # Booking notification

app/Notifications/
└── VerifyEmailNotification.php  # Custom verification email
```

---

## 🎨 Customization

To customize email templates:

1. Edit files in `resources/views/emails/`
2. Update colors in `layout.blade.php` (search for `#8B1538`)
3. Add your logo URL in header section
4. Update social media links in footer

---

## 🔗 Next Steps

1. ✅ Choose email provider (Gmail/SendGrid)
2. ✅ Update `.env` with credentials
3. ✅ Clear config cache
4. ✅ Test registration → verification email
5. ✅ Upload files to production server
6. ✅ Test on live site

---

Need help? Check Laravel Mail docs: https://laravel.com/docs/11.x/mail
