# Photographar - Bangladesh Photographers Marketplace

A comprehensive marketplace platform connecting photographers with clients across Bangladesh.

## 🎯 Project Overview

Photographar is a full-featured photography marketplace platform designed specifically for Bangladesh. It enables:
- Photographers to showcase portfolios and accept bookings
- Clients to find and hire photographers
- Event management and competitions
- Subscription-based premium features
- Integrated payment processing (SSLCommerz, bKash, Nagad)
- Trust scoring and verification system
- Reviews and rating system

## 🛠 Tech Stack

**Backend:**
- Laravel 11 (PHP 8.2+)
- MySQL 8.0
- Laravel Sanctum (API authentication)
- Redis (caching & queue)

**Frontend:**
- Vue 3
- Tailwind CSS
- Vite (build tool)
- Alpine.js

**Payments:**
- SSLCommerz
- bKash
- Nagad
- Bank Transfer

**Infrastructure:**
- AWS S3 (image storage)
- Cloudflare CDN
- SendGrid (email)
- Twilio (SMS)

## 📁 Project Structure

```
Photographar SB/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/    # API Controllers
│   │   └── Requests/           # Form validations
│   ├── Models/                 # Eloquent models
│   └── Services/               # Business logic
├── database/
│   ├── migrations/             # Database schema
│   └── seeders/                # Sample data
├── resources/
│   ├── views/                  # Blade templates
│   └── js/components/          # Vue components
├── routes/
│   └── api.php                 # API routes
├── config/                     # Configuration files
├── docs/                       # Documentation
├── api-documentation/          # API specs
└── wireframes/                 # UI/UX wireframes
```

## 🚀 Installation

### Prerequisites
- PHP 8.2+
- MySQL 8.0
- Node.js 18+
- Composer
- npm/yarn

### Steps

1. **Clone or Extract Project**
```bash
cd "Photographar SB"
```

2. **Install PHP Dependencies**
```bash
composer install
```

3. **Install NPM Dependencies**
```bash
npm install
```

4. **Configure Environment**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Edit .env File**
Set the following:
```
DB_DATABASE=photographar_db
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your_key

SSLCOMMERZ_STORE_ID=your_id
SSLCOMMERZ_STORE_PASSWORD=your_password

BKASH_MERCHANT_ID=your_id
BKASH_MERCHANT_KEY=your_key

NAGAD_MERCHANT_ID=your_id
NAGAD_MERCHANT_KEY=your_key
```

6. **Create Database**
```bash
mysql -u root -p
CREATE DATABASE photographar_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

7. **Run Migrations**
```bash
php artisan migrate --seed
```

8. **Build Frontend**
```bash
npm run build
```

9. **Start Development Server**
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 📚 API Documentation

See [API_ROUTES.md](api-documentation/API_ROUTES.md) for complete API endpoint documentation.

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication
All protected endpoints require:
```
Authorization: Bearer {token}
```

### Example: Login
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

## 🗄️ Database Schema

Core tables:
- **users** - User accounts with role-based access
- **photographers** - Photographer profiles
- **categories** - Photography categories
- **packages** - Service packages
- **inquiries** - Client booking inquiries
- **quotes** - Photographer quotes
- **bookings** - Confirmed bookings
- **reviews** - Customer reviews
- **transactions** - Payment transactions
- **events** - Photography events/workshops
- **competitions** - Photo competitions
- **subscriptions** - Premium subscriptions
- **verifications** - User verifications (NID, trade license, etc.)
- **trust_scores** - Calculated trust ratings

See [DATABASE_SCHEMA.md](database/DATABASE_SCHEMA.md) for complete schema.

## 👤 User Roles

1. **Guest** - Browsing access only
2. **Client** - Hire photographers, create bookings
3. **Photographer** - Create portfolio, accept bookings
4. **Studio Owner** - Manage studio and photographers
5. **Studio Manager** - Assistant to studio owner
6. **Studio Photographer** - Photographer working for studio
7. **Moderator** - Review content, manage disputes
8. **Admin** - System administration
9. **Super Admin** - Full system access

## 🔐 Authentication

### Registration
```bash
POST /auth/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "01712345678",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "photographer"
}
```

### Login
```bash
POST /auth/login
{
  "email": "john@example.com",
  "password": "password123"
}
```

Response:
```json
{
  "status": "success",
  "data": {
    "user": { ... },
    "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz..."
  }
}
```

## 💳 Payment Integration

### SSLCommerz (Card Payments)
```bash
POST /payments/initiate
{
  "booking_id": 1,
  "payment_method": "card",
  "amount": 5000
}
```

### bKash Mobile Wallet
```bash
POST /payments/initiate
{
  "booking_id": 1,
  "payment_method": "bkash",
  "amount": 5000
}
```

## 📋 Features

### Phase 1 (MVP - COMPLETE ✅)
- [x] User authentication (register, login, verification)
- [x] Photographer directory and search
- [x] Booking system (inquiry → quote → booking)
- [x] Payment processing (SSLCommerz, bKash, Nagad, Bank Transfer)
- [x] Reviews and ratings
- [x] Events management
- [x] Photo competitions (basic)
- [x] User dashboard
- [x] Admin panel with full CRUD
- [x] Notification system (Email + In-app)
- [x] Modern responsive design

### Phase 2 (Competition System - 50% COMPLETE 🔨)
- [x] Photo submission system (upload with metadata)
- [x] Submission gallery (grid view, filters, sort)
- [x] Admin moderation (approve/reject submissions)
- [x] Public voting system (with fraud detection)
- [x] Judge assignment system
- [x] Judge scoring dashboard (5-criteria evaluation)
- [x] Progress tracking (scoring completion)
- [ ] Winner calculation (combined votes + judge scores)
- [ ] Digital certificates (auto-generate PDF)
- [ ] Prize distribution tracking

### Phase 3 (Planned - Future)
- [ ] Advanced search and filters
- [ ] Real-time messaging system
- [ ] Portfolio analytics
- [ ] Advanced admin dashboard
- [ ] Studio management
- [ ] Multi-photographer assignments
- [ ] Invoice generation
- [ ] Competition categories
- [ ] Sponsorship system

### Phase 4 (Long Term)
- [ ] Mobile apps (iOS/Android)
- [ ] Live streaming for events
- [ ] Video portfolio support
- [ ] Advanced analytics
- [ ] AI recommendation engine
- [ ] Community features

## 🐛 Troubleshooting

### Database Connection Error
```
Check .env file DB credentials and ensure MySQL is running
mysql -u root -p -e "select 1"
```

### Migration Failed
```
Clear cache and retry:
php artisan cache:clear
php artisan config:clear
php artisan migrate
```

### Port Already in Use
```
php artisan serve --port=8001
```

## 📞 Support

- Documentation: See `/docs` folder
- API Docs: See `/api-documentation` folder
- Wireframes: See `/wireframes` folder

## 📄 License

This project is licensed under the MIT License - see LICENSE file for details.

## 👥 Team

- **Project Lead**: Your Name
- **Backend Developer**: Laravel Expert
- **Frontend Developer**: Vue 3 Specialist
- **Designer**: UI/UX Expert

---

**Last Updated**: January 27, 2026  
**Version**: 1.0.0 (Phase 1 Complete + Phase 2 50%)  
**Status**: Production Ready (Phase 1) + Active Development (Phase 2)  
**Build**: 761.99 kB (optimized)
