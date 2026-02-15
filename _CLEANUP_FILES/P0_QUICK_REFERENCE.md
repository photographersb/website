# P0 Implementation - Quick Reference

## 🚀 Quick Start

### For Users

**Photographers - Submit Verification:**
1. Navigate to `/verification` (click "Verification Center" in menu)
2. Click "Submit Verification Request"
3. Select verification type (Phone, Email, National ID, etc.)
4. Upload supporting documents (optional, max 5 files, 10MB each)
5. Click "Submit Request"
6. Wait for admin approval

**Users - Send Booking Messages:**
1. Go to `/bookings` and click a booking
2. Navigate to "Messages" tab or `/bookings/{id}/messages`
3. Type message in composer
4. Attach files (optional, max 5 files, 10MB each)
5. Click "Send Message"
6. Click attachments to preview documents

**View Verification Status:**
1. Go to `/verification` as a photographer
2. See approved/pending/expired verifications
3. Click "Renew" for expired verifications (if approved)

### For Admins

**Approve Photographer Verification:**
1. Go to Admin Dashboard → Verifications
2. Look for "Pending Verification Requests (P0)" section
3. Click "Approve" button next to pending request
4. Photographer is now verified (appears on profile)

**Reject Verification:**
1. In pending requests section, click "Reject"
2. Reason is recorded in system
3. Photographer receives notification

---

## 🔌 API Endpoints Quick Reference

### Booking Messages (Authenticated Users)

```bash
# List messages
GET /api/v1/bookings/{booking_id}/messages

# Send message with attachments
POST /api/v1/bookings/{booking_id}/messages
Content-Type: multipart/form-data
{
  "message": "Your message text",
  "attachments[]": [file1, file2, ...]  // max 5, 10MB each
}

# Get single message
GET /api/v1/bookings/{booking_id}/messages/{message_id}

# Mark as read
POST /api/v1/bookings/{booking_id}/messages/{message_id}/read

# Mark all as read
POST /api/v1/bookings/{booking_id}/messages/mark-all-read

# Delete message
DELETE /api/v1/bookings/{booking_id}/messages/{message_id}
```

### Verification (Photographers)

```bash
# Get verification status
GET /api/v1/verifications/status/{photographer_id}

# Submit verification request
POST /api/v1/verifications/submit
Content-Type: multipart/form-data
{
  "request_type": "nid",  // phone, email, nid, business_license, studio_certification
  "submitted_documents[]": [file1, file2, ...]  // max 5, 10MB each
}

# Renew expired verification
POST /api/v1/verifications/renew
{
  "verification_type": "nid"
}
```

### Verification (Admin Only)

```bash
# List pending verification requests
GET /api/v1/verifications/pending

# Approve verification request
POST /api/v1/verifications/{verification_request_id}/approve

# Reject verification request
POST /api/v1/verifications/{verification_request_id}/reject
{
  "reason": "Documents incomplete"
}

# Revoke verification (revoke approved verification)
POST /api/v1/verifications/{photographer_id}/verifications/{verification_id}/revoke
{
  "reason": "Expired"
}

# Get photographer verification history
GET /api/v1/verifications/{photographer_id}/history
```

---

## 📁 File Structure

```
app/Http/Controllers/Api/
  ├── BookingMessageController.php        (6 endpoints)
  ├── VerificationController.php          (9 endpoints)
  └── SitemapController.php               (2 endpoints)

app/Models/
  ├── BookingMessage.php                  (relationships to Booking, User)
  ├── BookingStatusLog.php                (status tracking)
  ├── UserVerification.php                (verification records)
  ├── VerificationRequest.php             (pending requests)
  └── SEOMetadata.php                     (SEO data)

resources/js/Pages/
  ├── BookingMessages.vue                 (messaging interface)
  ├── VerificationCenter.vue              (photographer verification)
  ├── PublicVerification.vue              (public verification badge)
  └── Admin/Verifications/Index.vue       (admin approval UI)

routes/
  └── api.php                             (26 P0 routes)
```

---

## 🔒 Security Features

- **Rate Limiting**: 5 requests/60s for submit, 10 requests/60s for admin actions
- **Authorization**: Model binding with Photographer and User checks
- **File Upload**: Max 5 files, 10MB each, image/PDF only
- **Access Control**: Admin-only endpoints use role middleware
- **Input Validation**: All requests validated before processing

---

## 📊 Data Models

### BookingMessage
```
- id: integer (primary)
- booking_id: foreign key to Booking
- sender_id: foreign key to User
- message: text
- is_read: boolean
- deleted_from_sender: boolean
- attachments: JSON array {filename, path, size}
- created_at, updated_at: timestamps
```

### UserVerification
```
- id: integer (primary)
- user_id: foreign key to User
- verification_type: enum (phone, email, nid, business_license, studio_certification)
- verification_status: enum (pending, approved, rejected, revoked)
- verified_at: timestamp (when approved)
- expires_at: timestamp (expiration date)
- notes: text
- created_at, updated_at: timestamps
```

### VerificationRequest
```
- id: integer (primary)
- user_id: foreign key to User
- request_type: enum
- status: enum (pending, approved, rejected)
- submitted_documents: JSON array {filename, path, size}
- reason: text
- created_at, updated_at: timestamps
```

---

## 🧪 Testing Scenarios

### Test 1: Complete Booking Message Flow
1. User A sends message with attachment to User B
2. Message appears in both users' message list
3. User B receives notification
4. User B clicks attachment to preview
5. User B replies with message
6. Message thread shows both messages

**Expected Result**: Messages exchanged, previews work, both users see full conversation

### Test 2: Verification Submission & Approval
1. Photographer navigates to `/verification`
2. Submits verification with National ID document
3. Admin sees pending request in dashboard
4. Admin approves verification
5. Photographer sees "approved" status
6. Photographer profile shows verified badge

**Expected Result**: Photographer marked as verified, badge appears on profile

### Test 3: Verification Renewal
1. Photographer has approved verification with expiry in past
2. Status shows "expired" with red badge
3. Photographer clicks "Renew" button
4. Renewal request submitted
5. Admin sees renewal request as pending
6. Admin approves renewal

**Expected Result**: Verification renewed, new expiry date set

### Test 4: Message Search & Filter
1. Open booking with 50+ messages
2. Type "contract" in search box
3. Only messages containing "contract" appear
4. Select "Other messages" in sender filter
5. Only non-user messages appear
6. Click "Clear" button
7. All messages appear again

**Expected Result**: Filters work independently and combined

---

## 🐛 Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Document preview shows blank | Check file is image (.jpg, .png, .gif, .webp) or PDF icon appears |
| Attachment won't upload | Check file size (<10MB) and count (<5 files) |
| Verification not renewing | Ensure verification is "approved" and past expiry date |
| Messages not showing | Verify booking_id is correct and user has access to booking |
| Admin doesn't see pending requests | Verify user is admin/super_admin role |

---

## 📝 API Response Examples

### Success Response (Booking Messages)
```json
{
  "success": true,
  "message": "Messages retrieved successfully",
  "data": [
    {
      "id": 1,
      "booking_id": 123,
      "sender_id": 456,
      "message": "Hello, what's your availability?",
      "is_read": true,
      "attachments": [
        {
          "filename": "contract.pdf",
          "path": "bookings/123/contract.pdf",
          "size": 256000
        }
      ],
      "created_at": "2026-02-03T10:30:00Z"
    }
  ]
}
```

### Success Response (Verification Status)
```json
{
  "success": true,
  "message": "Verification status retrieved",
  "data": {
    "verifications": [
      {
        "type": "nid",
        "status": "approved",
        "verified_at": "2026-01-15T08:00:00Z",
        "expires_at": "2027-01-15T08:00:00Z",
        "is_expired": false
      }
    ],
    "pending_requests": 1
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Unauthorized",
  "errors": null
}
```

---

## 🚀 Deployment Notes

1. **Database Migrations**: All migrations run automatically on `php artisan migrate`
2. **Storage**: Attachments stored in `storage/app/public` (symlinked to `public/storage`)
3. **Queue Jobs**: Notifications can be queued for better performance
4. **Caching**: Verification status can be cached for improved performance
5. **Environment Variables**: Ensure `APP_ENV=production` and `APP_DEBUG=false`

---

## 📞 Support

- Check individual controller files for method documentation
- Review model relationships in model files
- See routes/api.php for route documentation
- Check Vue components for frontend implementation details

---

**Last Updated**: February 3, 2026
**Version**: P0 v1.0
**Status**: Production Ready ✅
