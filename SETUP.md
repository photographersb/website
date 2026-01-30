# Photographar Setup Instructions

## Quick Start Guide

### Prerequisites
- XAMPP (PHP 8.2+, MySQL 8.0, Apache)
- Node.js 18+ & npm
- Composer
- Git

### Installation Steps

#### 1. Database Setup

Open MySQL command line:
```bash
mysql -u root -p
```

Create database:
```sql
CREATE DATABASE photographar_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### 2. Project Configuration

Navigate to project:
```bash
cd c:\xampp\htdocs\"Photographar SB"
```

Copy environment file:
```bash
copy .env.example .env
```

OR copy the .env file I created in the project root.

#### 3. Laravel Setup

Generate app key:
```bash
php artisan key:generate
```

Install PHP dependencies:
```bash
composer install
```

Run database migrations:
```bash
php artisan migrate --seed
```

#### 4. Frontend Setup

Install npm packages:
```bash
npm install
```

Build frontend assets:
```bash
npm run build
```

For development with watch mode:
```bash
npm run dev
```

#### 5. Start Application

Open two terminals:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Frontend Watch (Optional):**
```bash
npm run dev
```

Visit: `http://localhost:8000`

### Default Credentials

**Admin Account:**
- Email: `admin@photographar.com`
- Password: `password123`
- Role: Super Admin

**Test Photographer:**
- Email: `photographer@photographar.com`
- Password: `password123`
- Role: Photographer

**Test Client:**
- Email: `client@photographar.com`
- Password: `password123`
- Role: Client

### API Testing

Use Postman or Thunder Client to test API:

**Base URL:** `http://localhost:8000/api/v1`

**Register:**
```
POST /auth/register
Content-Type: application/json

{
  "name": "New User",
  "email": "user@example.com",
  "phone": "01712345678",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "photographer"
}
```

**Login:**
```
POST /auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

### File Structure Breakdown

```
app/
├── Http/Controllers/Api/
│   ├── AuthController.php       ← User authentication
│   ├── PhotographerController.php ← Photographer listing
│   ├── BookingController.php    ← Booking management
│   ├── ReviewController.php     ← Reviews & ratings
│   ├── EventController.php      ← Events & RSVPs
│   ├── CompetitionController.php ← Photo competitions
│   ├── PaymentController.php    ← Payment processing
│   ├── AdminController.php      ← Admin functions
│   └── PortfolioController.php  ← Portfolio management
├── Models/
│   ├── User.php                 ← User model
│   ├── Photographer.php         ← Photographer profile
│   ├── Booking.php              ← Booking records
│   ├── Review.php               ← Customer reviews
│   ├── Competition.php          ← Photo competitions
│   └── [18 more models]
└── Services/
    ├── TrustScoreService.php    ← Calculate trust scores
    └── FraudDetectionService.php ← Detect voting fraud

database/
├── migrations/
│   ├── 2025_01_01_000001_create_users_table.php
│   ├── 2025_01_01_000002_create_photographers_table.php
│   └── [23 more migrations]
└── seeders/
    └── DatabaseSeeder.php

resources/
├── js/
│   └── components/
│       ├── PhotographerSearch.vue    ← Search photographers
│       ├── PhotographerProfile.vue   ← View profile
│       ├── BookingForm.vue           ← Create booking
│       ├── Auth.vue                  ← Login/Register
│       └── AdminDashboard.vue        ← Admin panel

routes/
└── api.php                      ← All API routes
```

### Configuration Files

Key configuration files to customize:

**.env**
- Database credentials
- Mail settings
- Payment gateway keys
- API keys (Sendgrid, Twilio, AWS)

**config/app.php**
- Application name
- Timezone
- Locale

**config/database.php**
- Database connection
- MySQL settings

**config/auth.php**
- Authentication guards
- User roles & permissions

### Troubleshooting

#### Connection Refused
```bash
# Make sure XAMPP MySQL is running
# Or start manually:
mysql -u root -p
```

#### Composer Error
```bash
# Clear cache and retry
composer clear-cache
composer install
```

#### Migration Failed
```bash
# Rollback and retry
php artisan migrate:rollback
php artisan migrate
```

#### Port 8000 In Use
```bash
php artisan serve --port=8001
```

#### NPM Modules Error
```bash
rm -rf node_modules package-lock.json
npm install
```

### Performance Optimization

For production:

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Build assets for production
npm run build
```

### Security Checklist

Before production:
- [ ] Change APP_KEY in .env
- [ ] Set APP_DEBUG=false
- [ ] Configure HTTPS
- [ ] Set strong database passwords
- [ ] Enable CORS properly
- [ ] Set up rate limiting
- [ ] Configure mail properly
- [ ] Add SSL certificates
- [ ] Set up firewalls
- [ ] Enable 2FA for admin

### Next Steps

1. **Test API endpoints** using Postman
2. **Create test data** via seeders
3. **Configure payment gateways** (SSLCommerz, bKash, Nagad)
4. **Set up email** (SendGrid)
5. **Configure SMS** (Twilio)
6. **Deploy to server**

### Useful Commands

```bash
# Create new migration
php artisan make:migration create_table_name

# Create new model
php artisan make:model ModelName

# Create new controller
php artisan make:controller Api/ControllerName

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Run tests
php artisan test

# Start tinker shell
php artisan tinker

# Generate API documentation
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
```

### Support & Documentation

- Laravel Docs: https://laravel.com/docs
- Vue 3 Docs: https://vuejs.org
- API Docs: See `/docs` folder
- Database Schema: See `/database` folder

---

**Setup Complete!** 🎉

Your Photographar marketplace platform is ready to develop.

