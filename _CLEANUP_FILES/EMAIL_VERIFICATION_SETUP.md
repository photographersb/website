# 📧 Email Verification Setup Guide

## ✅ What's Been Configured

### 1. **User Model Updated**
- ✅ Added `MustVerifyEmail` interface to User model
- ✅ Users can now receive verification emails

### 2. **AuthController Enhanced**
- ✅ Sends verification email on registration
- ✅ Blocks unverified users from logging in
- ✅ Added resend verification endpoint
- ✅ Email verification with secure hash validation

### 3. **Routes Added**
- ✅ `POST /api/v1/auth/verify-email` - Verify email with token
- ✅ `POST /api/v1/auth/resend-verification` - Resend verification email

---

## 🔧 Email Provider Configuration

### **Option 1: Gmail (For Testing)**

Update `.env` on your server:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-digit-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@photographersb.com
MAIL_FROM_NAME="Photographar Bangladesh"
```

**Gmail Setup Steps:**
1. Go to: https://myaccount.google.com/security
2. Enable **2-Step Verification**
3. Go to: https://myaccount.google.com/apppasswords
4. Generate an **App Password** for "Mail"
5. Use that 16-character password in `MAIL_PASSWORD`

### **Option 2: SendGrid (Recommended for Production)**

```env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=SG.your-api-key-here
MAIL_FROM_ADDRESS=noreply@photographersb.com
MAIL_FROM_NAME="Photographar Bangladesh"
```

**SendGrid Setup:**
1. Sign up: https://sendgrid.com (Free: 100 emails/day)
2. Create API Key: Settings → API Keys → Create API Key
3. Copy API key to `.env`

### **Option 3: Mailgun**

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=photographersb.com
MAILGUN_SECRET=your-mailgun-secret
MAIL_FROM_ADDRESS=noreply@photographersb.com
```

---

## 🚀 How Email Verification Works

### **1. User Registration Flow**

```
User registers
    ↓
Account created (email_verified_at = null)
    ↓
Verification email sent automatically
    ↓
User receives email with link
    ↓
Clicks link → Email verified
    ↓
Can now log in
```

### **2. Login with Verification Check**

```javascript
// Login attempt
if (!user.hasVerifiedEmail()) {
    return {
        status: 'error',
        message: 'Please verify your email before logging in',
        code: 'EMAIL_NOT_VERIFIED'
    }
}
```

### **3. API Endpoints**

**Verify Email:**
```bash
POST /api/v1/auth/verify-email
{
    "id": 1,
    "hash": "sha1_hash_of_email"
}
```

**Resend Verification:**
```bash
POST /api/v1/auth/resend-verification
{
    "email": "user@example.com"
}
```

---

## 📝 Testing Email Verification

### **Test Locally (Log Driver)**

In local `.env`:
```env
MAIL_MAILER=log
```

Emails will be written to: `storage/logs/laravel.log`

### **Test with Mailtrap**

Free testing service: https://mailtrap.io

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

---

## 🎨 Frontend Integration (Already Done)

Your Vue Auth component already checks for email verification:

```javascript
// In Auth.vue
if (error.response?.data?.message?.includes('email is not verified')) {
    errorMessage.value = 'Please verify your email before logging in.';
}
```

---

## 🔄 Deployment Steps

### **Step 1: Update Server `.env`**

SSH into your server or use cPanel File Manager:

```bash
cd /home/photogra/public_html
nano .env
```

Add email configuration (Gmail/SendGrid/Mailgun)

### **Step 2: Upload Updated Files**

Upload these files to production:

```
app/Models/User.php
app/Http/Controllers/Api/AuthController.php
routes/api.php
```

### **Step 3: Clear Cache**

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### **Step 4: Test**

1. Register new account
2. Check email inbox
3. Click verification link
4. Try logging in before/after verification

---

## 📧 Email Template Customization

Laravel sends a default verification email. To customize:

```bash
php artisan vendor:publish --tag=laravel-notifications
```

Edit: `resources/views/vendor/notifications/email.blade.php`

---

## ⚠️ Troubleshooting

### **Emails Not Sending**

1. Check `.env` configuration
2. Check logs: `storage/logs/laravel.log`
3. Test with: `php artisan tinker`
   ```php
   Mail::raw('Test', function($msg) {
       $msg->to('test@example.com')->subject('Test');
   });
   ```

### **"Connection Refused" Error**

- Gmail: Check app password, 2FA enabled
- SendGrid: Verify API key is valid
- Check firewall allows port 587 (TLS) or 465 (SSL)

### **"Invalid Credentials"**

- Double-check username/password in `.env`
- For Gmail, ensure you're using App Password, not regular password

---

## 📊 Database Check

Verify email status in database:

```sql
SELECT id, name, email, email_verified_at FROM users;
```

Manually verify for testing:
```sql
UPDATE users SET email_verified_at = NOW() WHERE email = 'test@example.com';
```

---

## ✨ Next Steps

1. **Configure email provider** in production `.env`
2. **Upload updated files** to server
3. **Test registration** with real email
4. **Customize email template** (optional)
5. **Monitor logs** for any errors

---

**Email verification is now fully configured! 🎉**

Just add your email provider credentials to `.env` and it will work automatically.
