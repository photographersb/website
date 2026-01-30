# Database Schema - Photographer SB (MySQL 8.0)

## CORE TABLES

### Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255),
    role ENUM('client', 'photographer', 'studio_owner', 'studio_manager', 'studio_photographer', 'moderator', 'admin', 'super_admin') DEFAULT 'client',
    avatar_url VARCHAR(500),
    bio TEXT,
    is_verified BOOLEAN DEFAULT FALSE,
    email_verified_at TIMESTAMP NULL,
    phone_verified_at TIMESTAMP NULL,
    phone_otp VARCHAR(6),
    phone_otp_expires_at TIMESTAMP NULL,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    last_login_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_suspended BOOLEAN DEFAULT FALSE,
    suspended_reason TEXT,
    suspended_until TIMESTAMP NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_created_at (created_at),
    INDEX idx_is_active (is_active)
);
```

### Photographers Table
```sql
CREATE TABLE photographers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    business_name VARCHAR(255),
    slug VARCHAR(255) UNIQUE NOT NULL,
    bio TEXT,
    experience_years INT,
    specializations JSON,
    service_area_radius INT DEFAULT 50,
    service_area_unit ENUM('km', 'miles') DEFAULT 'km',
    cover_image_url VARCHAR(500),
    response_time VARCHAR(100),
    total_bookings INT DEFAULT 0,
    total_reviews INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    is_verified BOOLEAN DEFAULT FALSE,
    verification_type ENUM('phone', 'email', 'nid', 'trade_license'),
    verification_document_url VARCHAR(500),
    verified_at TIMESTAMP NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    featured_until TIMESTAMP NULL,
    profile_completeness INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_slug (slug),
    INDEX idx_is_verified (is_verified),
    INDEX idx_average_rating (average_rating),
    INDEX idx_is_featured (is_featured)
);
```

### Studios Table
```sql
CREATE TABLE studios (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    owner_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    bio TEXT,
    description TEXT,
    website VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    city_id INT,
    cover_image_url VARCHAR(500),
    logo_url VARCHAR(500),
    establishment_year INT,
    total_team_members INT DEFAULT 1,
    is_verified BOOLEAN DEFAULT FALSE,
    verification_document_url VARCHAR(500),
    verified_at TIMESTAMP NULL,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    featured_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_slug (slug),
    INDEX idx_owner_id (owner_id),
    INDEX idx_is_verified (is_verified)
);
```

### Studio Members Table
```sql
CREATE TABLE studio_members (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    studio_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    role ENUM('owner', 'manager', 'photographer', 'assistant') DEFAULT 'photographer',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (studio_id) REFERENCES studios(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY studio_user (studio_id, user_id),
    INDEX idx_studio_id (studio_id),
    INDEX idx_user_id (user_id)
);
```

### Cities Table
```sql
CREATE TABLE cities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    country_id INT,
    division_id INT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    photographer_count INT DEFAULT 0,
    
    INDEX idx_slug (slug),
    INDEX idx_photographer_count (photographer_count)
);
```

### Categories Table
```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(50),
    description TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    photographer_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active),
    INDEX idx_display_order (display_order)
);
```

### Tags Table
```sql
CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    usage_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_usage_count (usage_count)
);
```

---

## PORTFOLIO MANAGEMENT TABLES

### Albums Table
```sql
CREATE TABLE albums (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    photographer_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    cover_photo_url VARCHAR(500),
    category_id INT,
    is_public BOOLEAN DEFAULT TRUE,
    view_count INT DEFAULT 0,
    download_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    INDEX idx_photographer_id (photographer_id),
    INDEX idx_slug (slug),
    UNIQUE KEY photo_slug (photographer_id, slug)
);
```

### Photos Table
```sql
CREATE TABLE photos (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    album_id BIGINT UNSIGNED NOT NULL,
    photographer_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255),
    description TEXT,
    image_url VARCHAR(500) NOT NULL,
    thumbnail_url VARCHAR(500),
    image_path VARCHAR(500),
    file_size INT,
    width INT,
    height INT,
    camera_make VARCHAR(100),
    camera_model VARCHAR(100),
    camera_settings JSON,
    location VARCHAR(255),
    date_taken DATE,
    display_order INT,
    view_count INT DEFAULT 0,
    download_count INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE CASCADE,
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    INDEX idx_album_id (album_id),
    INDEX idx_photographer_id (photographer_id),
    INDEX idx_display_order (display_order),
    INDEX idx_is_featured (is_featured)
);
```

### Videos Table
```sql
CREATE TABLE videos (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    photographer_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    video_url VARCHAR(500),
    thumbnail_url VARCHAR(500),
    duration INT,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    INDEX idx_photographer_id (photographer_id)
);
```

---

## PACKAGES & PRICING TABLES

### Packages Table
```sql
CREATE TABLE packages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    photographer_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    base_price DECIMAL(10,2) NOT NULL,
    currency CHAR(3) DEFAULT 'BDT',
    duration_unit ENUM('hours', 'days', 'events') DEFAULT 'hours',
    duration_value INT,
    includes JSON,
    excludes JSON,
    add_ons JSON,
    travel_cost_type ENUM('per_km', 'fixed', 'none') DEFAULT 'none',
    travel_cost_value DECIMAL(10,2),
    advance_booking_days INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    view_count INT DEFAULT 0,
    inquiry_count INT DEFAULT 0,
    booking_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    INDEX idx_photographer_id (photographer_id),
    INDEX idx_is_active (is_active)
);
```

### Availability Table
```sql
CREATE TABLE availabilities (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    photographer_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    is_available BOOLEAN DEFAULT TRUE,
    status ENUM('available', 'booked', 'blocked') DEFAULT 'available',
    note VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    UNIQUE KEY photo_date (photographer_id, date),
    INDEX idx_date (date),
    INDEX idx_status (status)
);
```

---

## INQUIRY & BOOKING TABLES

### Inquiries Table
```sql
CREATE TABLE inquiries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    photographer_id BIGINT UNSIGNED NOT NULL,
    package_id BIGINT UNSIGNED,
    event_type VARCHAR(100),
    event_date DATE NOT NULL,
    event_location VARCHAR(500),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    guest_count INT,
    budget_min DECIMAL(10,2),
    budget_max DECIMAL(10,2),
    requirements TEXT,
    attachments JSON,
    status ENUM('new', 'viewed', 'responded', 'quote_sent', 'accepted', 'rejected', 'expired') DEFAULT 'new',
    response_message TEXT,
    responded_at TIMESTAMP NULL,
    viewed_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE SET NULL,
    INDEX idx_photographer_id (photographer_id),
    INDEX idx_client_id (client_id),
    INDEX idx_status (status),
    INDEX idx_event_date (event_date)
);
```

### Quotes Table
```sql
CREATE TABLE quotes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    inquiry_id BIGINT UNSIGNED NOT NULL,
    photographer_id BIGINT UNSIGNED NOT NULL,
    package_id BIGINT UNSIGNED,
    base_price DECIMAL(10,2) NOT NULL,
    add_ons DECIMAL(10,2) DEFAULT 0,
    travel_cost DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    discount_reason VARCHAR(255),
    tax_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    currency CHAR(3) DEFAULT 'BDT',
    items JSON,
    terms TEXT,
    cancellation_policy TEXT,
    payment_terms VARCHAR(100),
    deposit_percentage INT DEFAULT 0,
    validity_days INT DEFAULT 7,
    status ENUM('draft', 'sent', 'viewed', 'accepted', 'rejected', 'expired') DEFAULT 'draft',
    sent_at TIMESTAMP NULL,
    viewed_at TIMESTAMP NULL,
    accepted_at TIMESTAMP NULL,
    rejected_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (inquiry_id) REFERENCES inquiries(id) ON DELETE CASCADE,
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE SET NULL,
    INDEX idx_photographer_id (photographer_id),
    INDEX idx_status (status)
);
```

### Bookings Table
```sql
CREATE TABLE bookings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    inquiry_id BIGINT UNSIGNED,
    quote_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    photographer_id BIGINT UNSIGNED NOT NULL,
    package_id BIGINT UNSIGNED NOT NULL,
    event_date DATE NOT NULL,
    event_start_time TIME,
    event_end_time TIME,
    event_location VARCHAR(500),
    total_amount DECIMAL(10,2) NOT NULL,
    currency CHAR(3) DEFAULT 'BDT',
    status ENUM('confirmed', 'pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'confirmed',
    payment_status ENUM('pending', 'partial', 'completed', 'refunded') DEFAULT 'pending',
    deposit_amount DECIMAL(10,2),
    remaining_amount DECIMAL(10,2),
    cancellation_reason TEXT,
    cancelled_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    reminder_sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (inquiry_id) REFERENCES inquiries(id) ON DELETE SET NULL,
    FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE RESTRICT,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE RESTRICT,
    INDEX idx_client_id (client_id),
    INDEX idx_photographer_id (photographer_id),
    INDEX idx_event_date (event_date),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status)
);
```

---

## REVIEWS & RATINGS TABLES

### Reviews Table
```sql
CREATE TABLE reviews (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    booking_id BIGINT UNSIGNED NOT NULL,
    reviewer_id BIGINT UNSIGNED NOT NULL,
    photographer_id BIGINT UNSIGNED NOT NULL,
    rating INT DEFAULT 5,
    professionalism_score INT,
    quality_score INT,
    communication_score INT,
    value_score INT,
    delivery_score INT,
    title VARCHAR(255),
    comment TEXT NOT NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    is_verified_purchase BOOLEAN DEFAULT TRUE,
    photo_urls JSON,
    helpful_count INT DEFAULT 0,
    unhelpful_count INT DEFAULT 0,
    status ENUM('pending', 'published', 'flagged', 'hidden', 'rejected') DEFAULT 'pending',
    flag_reason VARCHAR(255),
    moderation_notes TEXT,
    approved_at TIMESTAMP NULL,
    rejected_at TIMESTAMP NULL,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    INDEX idx_photographer_id (photographer_id),
    INDEX idx_status (status),
    INDEX idx_rating (rating),
    INDEX idx_created_at (created_at)
);
```

### Review Replies Table
```sql
CREATE TABLE review_replies (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    review_id BIGINT UNSIGNED NOT NULL,
    photographer_id BIGINT UNSIGNED NOT NULL,
    reply_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    INDEX idx_review_id (review_id),
    INDEX idx_photographer_id (photographer_id)
);
```

---

## VERIFICATION & TRUST TABLES

### Verifications Table
```sql
CREATE TABLE verifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    verification_type ENUM('phone', 'email', 'nid', 'trade_license', 'identity') NOT NULL,
    verification_status ENUM('pending', 'verified', 'rejected', 'expired') DEFAULT 'pending',
    phone_number VARCHAR(20),
    email_address VARCHAR(255),
    document_type VARCHAR(50),
    document_url VARCHAR(500),
    document_number VARCHAR(100),
    verified_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    rejected_reason TEXT,
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_verification_status (verification_status)
);
```

### Trust Scores Table
```sql
CREATE TABLE trust_scores (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    photographer_id BIGINT UNSIGNED NOT NULL,
    phone_verified TINYINT DEFAULT 0,
    email_verified TINYINT DEFAULT 0,
    id_verified TINYINT DEFAULT 0,
    review_count INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0,
    booking_completion_rate DECIMAL(5,2) DEFAULT 0,
    response_time_avg INT DEFAULT 0,
    profile_completeness INT DEFAULT 0,
    overall_score INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    UNIQUE KEY photo_trust (photographer_id)
);
```

---

## MONETIZATION TABLES

### Subscription Plans Table
```sql
CREATE TABLE subscription_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    tier ENUM('free', 'premium', 'pro', 'enterprise') DEFAULT 'free',
    price DECIMAL(10,2) DEFAULT 0,
    currency CHAR(3) DEFAULT 'BDT',
    billing_period ENUM('monthly', 'yearly') DEFAULT 'monthly',
    discount_yearly INT DEFAULT 0,
    features JSON,
    max_photos INT DEFAULT 20,
    max_videos INT DEFAULT 0,
    max_albums INT DEFAULT 5,
    featured_listings INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tier (tier),
    INDEX idx_is_active (is_active)
);
```

### Subscriptions Table
```sql
CREATE TABLE subscriptions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    plan_id INT NOT NULL,
    status ENUM('active', 'cancelled', 'expired', 'paused') DEFAULT 'active',
    start_date DATE NOT NULL,
    end_date DATE,
    current_period_start DATE,
    current_period_end DATE,
    auto_renew BOOLEAN DEFAULT TRUE,
    cancel_reason TEXT,
    cancelled_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
);
```

### Transactions Table
```sql
CREATE TABLE transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    transaction_type ENUM('subscription', 'featured', 'boost', 'competition_entry', 'event_ticket', 'refund', 'payout') NOT NULL,
    reference_id BIGINT UNSIGNED,
    reference_table VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    currency CHAR(3) DEFAULT 'BDT',
    payment_method ENUM('card', 'bkash', 'nagad', 'bank_transfer', 'paypal') NOT NULL,
    gateway_reference VARCHAR(255),
    status ENUM('pending', 'completed', 'failed', 'cancelled', 'refunded') DEFAULT 'pending',
    description VARCHAR(500),
    commission_amount DECIMAL(10,2) DEFAULT 0,
    platform_fee DECIMAL(10,2) DEFAULT 0,
    net_amount DECIMAL(10,2),
    payment_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_payment_date (payment_date)
);
```

### Featured Listings Table
```sql
CREATE TABLE featured_listings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    photographer_id BIGINT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    duration_days INT,
    price DECIMAL(10,2),
    transaction_id BIGINT UNSIGNED,
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    INDEX idx_photographer_id (photographer_id),
    INDEX idx_status (status)
);
```

---

## CLIENT FEATURES TABLES

### Favorites Table
```sql
CREATE TABLE favorites (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    client_id BIGINT UNSIGNED NOT NULL,
    photographer_id BIGINT UNSIGNED NOT NULL,
    folder_id BIGINT UNSIGNED,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    UNIQUE KEY client_photo (client_id, photographer_id),
    INDEX idx_client_id (client_id)
);
```

### Saved Searches Table
```sql
CREATE TABLE saved_searches (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    client_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255),
    filters JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_client_id (client_id)
);
```

---

## EVENT MODULE TABLES

### Events Table
```sql
CREATE TABLE events (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    organizer_id BIGINT UNSIGNED NOT NULL,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description LONGTEXT,
    cover_image_url VARCHAR(500),
    banner_image_url VARCHAR(500),
    event_date DATETIME NOT NULL,
    event_end_date DATETIME,
    start_time TIME,
    end_time TIME,
    all_day_event BOOLEAN DEFAULT FALSE,
    duration_minutes INT,
    location VARCHAR(255),
    address TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    city_id INT,
    max_attendees INT,
    require_registration BOOLEAN DEFAULT FALSE,
    is_ticketed BOOLEAN DEFAULT FALSE,
    ticket_price DECIMAL(10,2),
    is_paid BOOLEAN DEFAULT FALSE,
    status ENUM('draft', 'published', 'completed', 'cancelled') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,
    featured_until TIMESTAMP NULL,
    view_count INT DEFAULT 0,
    rsvp_count INT DEFAULT 0,
    gallery_published BOOLEAN DEFAULT FALSE,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    INDEX idx_slug (slug),
    INDEX idx_event_date (event_date),
    INDEX idx_status (status)
);
```

### Event RSVPs Table
```sql
CREATE TABLE event_rsvps (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    event_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    rsvp_status ENUM('going', 'maybe', 'not_going') DEFAULT 'going',
    responded_at TIMESTAMP,
    check_in_at TIMESTAMP NULL,
    ticket_purchased BOOLEAN DEFAULT FALSE,
    special_requirements TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY event_user (event_id, user_id),
    INDEX idx_event_id (event_id)
);
```

### Event Gallery Table
```sql
CREATE TABLE event_gallery (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    event_id BIGINT UNSIGNED NOT NULL,
    uploader_id BIGINT UNSIGNED NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    thumbnail_url VARCHAR(500),
    caption VARCHAR(500),
    display_order INT,
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    like_count INT DEFAULT 0,
    can_download BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (uploader_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_event_id (event_id),
    INDEX idx_display_order (display_order)
);
```

---

## COMPETITION MODULE TABLES

### Competitions Table
```sql
CREATE TABLE competitions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    admin_id BIGINT UNSIGNED NOT NULL,
    organizer_id BIGINT UNSIGNED,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description LONGTEXT,
    theme VARCHAR(255),
    hero_image_url VARCHAR(500),
    banner_image_url VARCHAR(500),
    tagline VARCHAR(255),
    submission_deadline DATETIME NOT NULL,
    voting_start_at DATETIME,
    voting_end_at DATETIME,
    judging_start_at DATETIME,
    judging_end_at DATETIME,
    results_announcement_date DATETIME,
    status ENUM('draft', 'active', 'judging', 'completed', 'cancelled') DEFAULT 'draft',
    allow_public_voting BOOLEAN DEFAULT TRUE,
    allow_judge_scoring BOOLEAN DEFAULT TRUE,
    allow_watermark BOOLEAN DEFAULT FALSE,
    require_watermark BOOLEAN DEFAULT FALSE,
    participation_fee DECIMAL(10,2) DEFAULT 0,
    is_paid_competition BOOLEAN DEFAULT FALSE,
    max_submissions_per_user INT DEFAULT 3,
    min_submissions_to_proceed INT DEFAULT 10,
    total_prize_pool DECIMAL(10,2) DEFAULT 0,
    number_of_winners INT DEFAULT 3,
    is_public BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    featured_until TIMESTAMP NULL,
    total_submissions INT DEFAULT 0,
    total_votes INT DEFAULT 0,
    results_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (admin_id) REFERENCES users(id),
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status)
);
```

### Competition Submissions Table
```sql
CREATE TABLE competition_submissions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    competition_id BIGINT UNSIGNED NOT NULL,
    category_id INT,
    photographer_id BIGINT UNSIGNED NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    thumbnail_url VARCHAR(500),
    title VARCHAR(255),
    description TEXT,
    location VARCHAR(255),
    date_taken DATE,
    camera_make VARCHAR(100),
    camera_model VARCHAR(100),
    camera_settings JSON,
    hashtags VARCHAR(500),
    is_watermarked BOOLEAN DEFAULT FALSE,
    status ENUM('pending_review', 'approved', 'rejected', 'disqualified') DEFAULT 'pending_review',
    view_count INT DEFAULT 0,
    vote_count INT DEFAULT 0,
    judge_score DECIMAL(5,2),
    final_score DECIMAL(5,2),
    ranking INT,
    is_winner BOOLEAN DEFAULT FALSE,
    winner_position VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    INDEX idx_competition_id (competition_id),
    INDEX idx_photographer_id (photographer_id),
    INDEX idx_status (status)
);
```

### Competition Votes Table
```sql
CREATE TABLE competition_votes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    submission_id BIGINT UNSIGNED NOT NULL,
    voter_id BIGINT UNSIGNED NOT NULL,
    competition_id BIGINT UNSIGNED NOT NULL,
    vote_value INT DEFAULT 1,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(50),
    device_fingerprint VARCHAR(255),
    is_verified BOOLEAN DEFAULT FALSE,
    is_valid BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (submission_id) REFERENCES competition_submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (voter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    UNIQUE KEY vote_unique (submission_id, voter_id),
    INDEX idx_competition_id (competition_id),
    INDEX idx_is_valid (is_valid)
);
```

### Judges Table
```sql
CREATE TABLE judges (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    competition_id BIGINT UNSIGNED NOT NULL,
    expertise_level VARCHAR(50),
    bio TEXT,
    credentials TEXT,
    is_verified BOOLEAN DEFAULT FALSE,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    UNIQUE KEY judge_unique (user_id, competition_id)
);
```

### Judge Scores Table
```sql
CREATE TABLE judge_scores (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    judge_id BIGINT UNSIGNED NOT NULL,
    submission_id BIGINT UNSIGNED NOT NULL,
    competition_id BIGINT UNSIGNED NOT NULL,
    composition_score DECIMAL(5,2),
    technical_skill_score DECIMAL(5,2),
    originality_score DECIMAL(5,2),
    emotion_score DECIMAL(5,2),
    overall_score DECIMAL(5,2),
    judge_notes TEXT,
    scored_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (judge_id) REFERENCES judges(id) ON DELETE CASCADE,
    FOREIGN KEY (submission_id) REFERENCES competition_submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    UNIQUE KEY judge_submission (judge_id, submission_id)
);
```

### Competition Winners Table
```sql
CREATE TABLE competition_winners (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    competition_id BIGINT UNSIGNED NOT NULL,
    submission_id BIGINT UNSIGNED NOT NULL,
    photographer_id BIGINT UNSIGNED NOT NULL,
    winner_position INT,
    prize_amount DECIMAL(10,2),
    prize_description TEXT,
    final_score DECIMAL(5,2),
    public_votes_percentage DECIMAL(5,2),
    judge_score_percentage DECIMAL(5,2),
    certificate_id BIGINT UNSIGNED,
    certificate_issued_at TIMESTAMP NULL,
    payment_status ENUM('pending', 'completed', 'refunded') DEFAULT 'pending',
    payment_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    FOREIGN KEY (submission_id) REFERENCES competition_submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    INDEX idx_competition_id (competition_id)
);
```

### Certificates Table
```sql
CREATE TABLE certificates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    winner_id BIGINT UNSIGNED NOT NULL,
    photographer_id BIGINT UNSIGNED NOT NULL,
    competition_id BIGINT UNSIGNED NOT NULL,
    certificate_number VARCHAR(50) UNIQUE NOT NULL,
    award_title VARCHAR(255),
    award_date DATE,
    certificate_pdf_url VARCHAR(500),
    certificate_image_url VARCHAR(500),
    is_downloaded BOOLEAN DEFAULT FALSE,
    download_count INT DEFAULT 0,
    shared_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (winner_id) REFERENCES competition_winners(id) ON DELETE CASCADE,
    FOREIGN KEY (photographer_id) REFERENCES photographers(id) ON DELETE CASCADE,
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    INDEX idx_photographer_id (photographer_id)
);
```

---

## LOGGING & AUDIT TABLES

### Audit Logs Table
```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED,
    action VARCHAR(100) NOT NULL,
    model_type VARCHAR(100),
    model_id BIGINT UNSIGNED,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(50),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_action (action),
    INDEX idx_model_type (model_type),
    INDEX idx_created_at (created_at)
);
```

### API Logs Table
```sql
CREATE TABLE api_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED,
    endpoint VARCHAR(255) NOT NULL,
    method VARCHAR(10),
    status_code INT,
    response_time_ms INT,
    ip_address VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_endpoint (endpoint),
    INDEX idx_created_at (created_at)
);
```

---

## INDEXES SUMMARY

Critical indexes for query performance:
- `users`: email, role, is_active, created_at
- `photographers`: is_verified, average_rating, is_featured, slug
- `inquiries`: photographer_id, client_id, status, event_date
- `bookings`: client_id, photographer_id, event_date, status
- `reviews`: photographer_id, status, rating, created_at
- `competitions`: slug, status
- `competition_submissions`: competition_id, photographer_id, status
- `events`: event_date, status, is_featured
- `transactions`: user_id, status, payment_date
- `audit_logs`: action, model_type, created_at

