# Photographar API Quick Reference

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication
All protected endpoints require:
```
Authorization: Bearer {access_token}
```

Get token from login response and store in localStorage.

---

## 🔐 Authentication Endpoints

### Register
```bash
POST /auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "01712345678",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "photographer|client|studio_owner"
}

Response: {
  "status": "success",
  "message": "Registration successful. Please verify your email.",
  "data": {
    "user_id": 1,
    "email": "john@example.com"
  }
}
```

### Login
```bash
POST /auth/login
{
  "email": "john@example.com",
  "password": "password123"
}

Response: {
  "status": "success",
  "data": {
    "user": { ... },
    "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz..."
  }
}
```

### Logout (Protected)
```bash
POST /auth/logout
Authorization: Bearer {token}

Response: {
  "status": "success",
  "message": "Logged out successfully"
}
```

### Get Current User (Protected)
```bash
GET /auth/me
Authorization: Bearer {token}

Response: {
  "status": "success",
  "data": { user_object }
}
```

---

## 👥 Photographer Endpoints

### Get All Photographers
```bash
GET /photographers?page=1&category=wedding&city=dhaka&rating=4&sort=rating
Query Parameters:
  - page: Page number (default: 1)
  - category: Filter by category slug
  - city: Filter by city slug
  - rating: Minimum rating (1-5)
  - sort: relevance|rating|newest (default: relevance)

Response: {
  "status": "success",
  "data": [ ... ],
  "meta": {
    "total": 100,
    "per_page": 30,
    "current_page": 1,
    "last_page": 4
  }
}
```

### Get Single Photographer
```bash
GET /photographers/{slug}

Response: {
  "status": "success",
  "data": {
    "id": 1,
    "slug": "photographer-1",
    "user": { ... },
    "trustScore": { ... },
    "categories": [ ... ],
    "albums": [ ... ],
    "packages": [ ... ],
    "reviews": [ ... ]
  }
}
```

### Search Photographers
```bash
GET /photographers/search?q=john
Query Parameters:
  - q: Search query (min 2 chars)

Response: {
  "status": "success",
  "data": [ ... ]
}
```

---

## 📅 Booking Endpoints

### Create Inquiry (Protected)
```bash
POST /bookings/inquiry
Authorization: Bearer {token}
{
  "photographer_id": 1,
  "package_id": 5,
  "event_date": "2025-06-15",
  "event_location": "Dhaka",
  "guest_count": 50,
  "budget_min": 15000,
  "budget_max": 25000,
  "requirements": "Need drone shots"
}

Response: {
  "status": "success",
  "message": "Inquiry created successfully",
  "data": { inquiry_object }
}
```

### Get My Bookings (Protected)
```bash
GET /bookings
Authorization: Bearer {token}

Response: {
  "status": "success",
  "data": [ ... ],
  "meta": {
    "total": 10,
    "per_page": 20,
    "current_page": 1
  }
}
```

### Get Booking Details (Protected)
```bash
GET /bookings/{booking_id}
Authorization: Bearer {token}

Response: {
  "status": "success",
  "data": {
    "id": 1,
    "photographer": { ... },
    "package": { ... },
    "inquiry": { ... },
    "quote": { ... },
    "reviews": [ ... ]
  }
}
```

### Cancel Booking (Protected)
```bash
PATCH /bookings/{booking_id}/cancel
Authorization: Bearer {token}
{
  "reason": "Found another photographer"
}

Response: {
  "status": "success",
  "message": "Booking cancelled successfully",
  "data": { booking_object }
}
```

---

## ⭐ Review Endpoints

### Create Review (Protected)
```bash
POST /reviews
Authorization: Bearer {token}
{
  "booking_id": 1,
  "rating": 5,
  "professionalism_score": 9,
  "quality_score": 10,
  "communication_score": 9,
  "value_score": 8,
  "delivery_score": 10,
  "title": "Excellent photographer",
  "comment": "Amazing work, highly recommended!",
  "is_anonymous": false
}

Response: {
  "status": "success",
  "message": "Review created successfully",
  "data": { review_object }
}
```

### Get Photographer Reviews
```bash
GET /photographers/{photographer_id}/reviews?page=1

Response: {
  "status": "success",
  "data": [ ... ],
  "meta": {
    "total": 25,
    "per_page": 20
  }
}
```

---

## 🎉 Event Endpoints

### Get All Events
```bash
GET /events?page=1&city=dhaka&from_date=2025-01-01&to_date=2025-12-31
Query Parameters:
  - city: Filter by city slug
  - from_date: Start date (YYYY-MM-DD)
  - to_date: End date (YYYY-MM-DD)

Response: {
  "status": "success",
  "data": [ ... ],
  "meta": { ... }
}
```

### Get Event Details
```bash
GET /events/{event_slug}

Response: {
  "status": "success",
  "data": { event_object }
}
```

### RSVP to Event (Protected)
```bash
POST /events/{event_id}/rsvp
Authorization: Bearer {token}
{
  "rsvp_status": "going|maybe|not_going"
}

Response: {
  "status": "success",
  "message": "RSVP updated",
  "data": { rsvp_object }
}
```

---

## 🏆 Competition Endpoints

### Get All Competitions
```bash
GET /competitions?page=1&status=active

Response: {
  "status": "success",
  "data": [ ... ],
  "meta": { ... }
}
```

### Get Competition Details
```bash
GET /competitions/{competition_slug}

Response: {
  "status": "success",
  "data": { competition_object }
}
```

### Submit Photo to Competition (Protected)
```bash
POST /competitions/{competition_id}/submit
Authorization: Bearer {token}
{
  "image_url": "https://...",
  "title": "Beautiful sunset",
  "description": "Taken in Cox's Bazar",
  "location": "Cox's Bazar",
  "date_taken": "2025-01-15",
  "camera_make": "Canon",
  "camera_model": "EOS 5D Mark IV",
  "hashtags": "#sunset #landscape"
}

Response: {
  "status": "success",
  "message": "Photo submitted successfully",
  "data": { submission_object }
}
```

### Vote on Submission (Protected)
```bash
POST /competition-submissions/{submission_id}/vote
Authorization: Bearer {token}

Response: {
  "status": "success",
  "message": "Vote recorded",
  "data": { vote_object }
}
```

### Get Competition Leaderboard
```bash
GET /competitions/{competition_id}/leaderboard?page=1

Response: {
  "status": "success",
  "data": [ ... ],
  "meta": { ... }
}
```

---

## 💳 Payment Endpoints

### Initiate Payment (Protected)
```bash
POST /payments/initiate
Authorization: Bearer {token}
{
  "booking_id": 1,
  "payment_method": "card|bkash|nagad|bank_transfer",
  "amount": 15000
}

Response: {
  "status": "success",
  "payment_url": "https://...",
  "data": { payment_data }
}
```

### Payment Callback
```bash
POST /payments/callback
{
  "tran_id": "...",
  "status": "VALID"
}
```

---

## 👨‍💼 Admin Endpoints (Admin Only)

### Get Dashboard
```bash
GET /admin/dashboard
Authorization: Bearer {admin_token}

Response: {
  "status": "success",
  "data": {
    "total_users": 150,
    "total_photographers": 50,
    "active_bookings": 25,
    "total_revenue": 500000,
    "recent_activity": [ ... ]
  }
}
```

### Get All Users
```bash
GET /admin/users?page=1&role=photographer&search=john
Authorization: Bearer {admin_token}

Response: {
  "status": "success",
  "data": [ ... ],
  "meta": { ... }
}
```

### Suspend User
```bash
POST /admin/users/{user_id}/suspend
Authorization: Bearer {admin_token}
{
  "reason": "Violation of terms"
}
```

### Approve Verification
```bash
POST /admin/verifications/{verification_id}/approve
Authorization: Bearer {admin_token}

Response: {
  "status": "success",
  "message": "Verification approved"
}
```

---

## ⚠️ Error Responses

All errors follow this format:

```json
{
  "status": "error",
  "message": "Error description",
  "errors": {
    "field": ["Error message"]
  }
}
```

### Common Status Codes
- 200 - Success
- 201 - Created
- 400 - Bad Request
- 401 - Unauthorized
- 403 - Forbidden
- 404 - Not Found
- 422 - Validation Error
- 500 - Server Error

---

## 🔄 Pagination

All list endpoints support pagination:

```bash
GET /endpoint?page=2&per_page=50

Response includes:
{
  "meta": {
    "total": 100,
    "per_page": 50,
    "current_page": 2,
    "last_page": 2
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": "...",
    "next": "..."
  }
}
```

---

## 📱 Response Format

All responses follow this structure:

```json
{
  "status": "success|error",
  "message": "Optional message",
  "data": { ... },
  "meta": { ... }
}
```

---

**Last Updated**: January 2025
**API Version**: 1.0
**Status**: Beta
