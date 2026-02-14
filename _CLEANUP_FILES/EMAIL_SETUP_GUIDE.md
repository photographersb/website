# 📧 Email Configuration Guide - Photographar Platform

**Critical Setup Required Before Launch**

---

## Current Status ⚠️

The platform is currently using the `log` mail driver, which means **no emails are being sent** to users. All email notifications are logged to `storage/logs/laravel.log` instead.

**This must be fixed before production deployment.**

---

## Email Services Comparison

### 1. SendGrid (Recommended) ⭐

**Why Choose SendGrid:**
- ✅ 100 emails/day free tier
- ✅ Easy to set up
- ✅ Excellent deliverability
- ✅ Email templates support
- ✅ Analytics dashboard

**Pricing:**
- Free: 100 emails/day
- Essentials: $19.95/month (50,000 emails)
- Pro: $89.95/month (100,000 emails)

**Setup Time:** 10-15 minutes

---

### 2. Mailgun

**Why Choose Mailgun:**
- ✅ 5,000 emails/month free
- ✅ Developer-friendly API
- ✅ Good analytics
- ✅ Pay-as-you-go pricing

**Pricing:**
- Free: 5,000 emails/month (first 3 months)
- Foundation: $35/month (50,000 emails)
- Growth: $80/month (100,000 emails)

**Setup Time:** 15-20 minutes

---

### 3. AWS SES

**Why Choose AWS SES:**
- ✅ Very cheap ($0.10 per 1,000 emails)
- ✅ Highly scalable
- ✅ Great for high volume

**Pricing:**
- $0.10 per 1,000 emails
- No monthly fee

**Setup Time:** 30-45 minutes (requires AWS account setup + domain verification)

---

### 4. Mailtrap (Staging Only)

**Why Use Mailtrap:**
- ✅ Email testing environment
- ✅ See exactly what users receive
- ✅ Test without sending real emails

**Use Case:** Development and staging environments only

---

## Quick Setup: SendGrid (Recommended)

### Step 1: Create SendGrid Account
1. Go to https://signup.sendgrid.com/
2. Sign up with your email
3. Complete email verification
4. Verify your identity (required by SendGrid)

### Step 2: Create API Key
1. Go to Settings → API Keys
2. Click "Create API Key"
3. Name: `Photographar Production`
4. Permissions: Select "Full Access" (or "Mail Send" for security)
5. Click "Create & View"
6. **Copy the API key** (you won't see it again!)

### Step 3: Verify Sender Identity
1. Go to Settings → Sender Authentication
2. Choose "Single Sender Verification" (easiest for small projects)
3. Add email: `noreply@photographar.com` (or your domain)
4. Complete verification form
5. Check your email for verification link
6. Click verification link

### Step 4: Update .env File

Open `c:\xampp\htdocs\Photographar SB\.env` and update:

```env
# Change from:
MAIL_MAILER=log

# To:
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=YOUR_SENDGRID_API_KEY_HERE
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@photographar.com"
MAIL_FROM_NAME="Photographar Bangladesh"
```

### Step 5: Test Email Configuration

Run this command in terminal:

```bash
cd "c:\xampp\htdocs\Photographar SB"
php artisan tinker
```

Then in tinker:

```php
Mail::raw('Test email from Photographar!', function($message) {
    $message->to('your-email@example.com')->subject('Test Email');
});
```

Check your inbox. If you receive the email, configuration is successful! ✅

### Step 6: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## Alternative: Mailgun Setup

### Step 1: Create Mailgun Account
1. Go to https://signup.mailgun.com/
2. Sign up with your email
3. Complete email verification

### Step 2: Get API Credentials
1. Go to Settings → API Keys
2. Copy your "Private API key"
3. Go to Sending → Domains
4. Note your sandbox domain (or add custom domain)

### Step 3: Update .env File

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@YOUR_MAILGUN_DOMAIN
MAIL_PASSWORD=YOUR_MAILGUN_SMTP_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@photographar.com"
MAIL_FROM_NAME="Photographar Bangladesh"
```

---

## Alternative: AWS SES Setup

### Step 1: Create AWS Account
1. Go to https://aws.amazon.com/ses/
2. Sign up (requires credit card)

### Step 2: Verify Email/Domain
1. Go to SES Console → Verified Identities
2. Click "Create Identity"
3. Choose "Email address" or "Domain"
4. Complete verification

### Step 3: Create SMTP Credentials
1. Go to SMTP Settings
2. Click "Create SMTP Credentials"
3. Download credentials CSV file

### Step 4: Request Production Access
1. AWS SES starts in "Sandbox Mode" (limited)
2. Go to "Account Dashboard"
3. Click "Request production access"
4. Fill out form (takes 24-48 hours for approval)

### Step 5: Update .env File

```env
MAIL_MAILER=smtp
MAIL_HOST=email-smtp.ap-southeast-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=YOUR_AWS_SES_SMTP_USERNAME
MAIL_PASSWORD=YOUR_AWS_SES_SMTP_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@photographar.com"
MAIL_FROM_NAME="Photographar Bangladesh"
```

---

## Email Templates Already Built ✅

The platform has 4 email notification classes ready:

### 1. BookingCreated
**Sent to:** Client and Photographer  
**When:** New booking inquiry created  
**Contains:** Booking details, photographer info, action buttons

### 2. BookingStatusUpdated
**Sent to:** Client  
**When:** Booking status changes (confirmed, completed, cancelled)  
**Contains:** Status update, next steps, action buttons

### 3. PaymentReceived
**Sent to:** Client and Photographer  
**When:** Payment successfully processed  
**Contains:** Transaction details, receipt, invoice link

### 4. ReviewRequest
**Sent to:** Client  
**When:** 24 hours after booking completion  
**Contains:** Review form link, rating options

---

## Testing Checklist

After configuring email service, test all 4 notification types:

### Test 1: Booking Email
```bash
# Create a test booking via the UI
# Check if both client and photographer receive emails
```

### Test 2: Status Update Email
```bash
# Update booking status in admin panel
# Check if client receives status update email
```

### Test 3: Payment Email
```bash
# Complete a test payment (use sandbox mode)
# Check if payment confirmation email is sent
```

### Test 4: Review Request Email
```bash
# Mark booking as completed
# Wait 24 hours OR manually trigger:
php artisan tinker
```

```php
$booking = \App\Models\Booking::find(1);
$booking->user->notify(new \App\Notifications\ReviewRequest($booking));
```

---

## Production Checklist

Before launching:

- [ ] Email service configured (SendGrid/Mailgun/SES)
- [ ] API key added to `.env`
- [ ] Sender email verified
- [ ] Test email sent successfully
- [ ] All 4 notification types tested
- [ ] Cache cleared (`php artisan config:clear`)
- [ ] `.env` backed up securely
- [ ] Email deliverability checked (not landing in spam)
- [ ] Monitoring setup (track bounce rate, delivery rate)

---

## Troubleshooting

### Emails Not Being Sent

**Check 1:** Verify `.env` configuration
```bash
php artisan config:clear
php artisan tinker
```

```php
echo config('mail.mailer');  // Should show "smtp"
echo config('mail.host');     // Should show your SMTP host
```

**Check 2:** Check Laravel logs
```bash
tail -f storage/logs/laravel.log
```

**Check 3:** Test SMTP connection
```bash
php artisan tinker
```

```php
use Illuminate\Support\Facades\Mail;
Mail::raw('Test', function($message) {
    $message->to('your-email@example.com')->subject('Test');
});
```

---

### Emails Going to Spam

**Fix 1:** Verify sender domain  
- Add SPF record to DNS
- Add DKIM record to DNS
- Complete domain authentication in SendGrid/Mailgun

**Fix 2:** Improve email content  
- Add plain text version
- Avoid spam trigger words
- Include unsubscribe link
- Use reputable email service

**Fix 3:** Warm up sender reputation  
- Start with small volume
- Gradually increase send rate
- Monitor bounce/complaint rates

---

## Cost Estimates

### SendGrid
- **Free tier:** 100 emails/day = 3,000/month
- **If you exceed:** $19.95/month for 50,000 emails
- **Typical cost for 10,000 bookings/month:** $19.95-39.95/month

### Mailgun
- **Free tier:** 5,000 emails/month (first 3 months)
- **After free period:** $35/month for 50,000 emails
- **Pay-as-you-go option available**

### AWS SES
- **Always:** $0.10 per 1,000 emails
- **10,000 emails/month:** $1.00/month
- **100,000 emails/month:** $10.00/month
- **Cheapest for high volume**

---

## Recommendation for Photographar

**For Launch (0-1,000 users):**
→ Use **SendGrid Free** (100 emails/day = 3,000/month)

**For Growth (1,000-10,000 users):**
→ Upgrade to **SendGrid Essentials** ($19.95/month)

**For Scale (10,000+ users):**
→ Switch to **AWS SES** (much cheaper at scale)

---

## Support

If you encounter issues:

1. **SendGrid Support:** https://support.sendgrid.com/
2. **Mailgun Support:** https://help.mailgun.com/
3. **AWS SES Support:** https://docs.aws.amazon.com/ses/

**Laravel Mail Documentation:** https://laravel.com/docs/11.x/mail

---

**Last Updated:** January 27, 2026  
**Platform:** Photographar Bangladesh  
**Version:** 1.0
