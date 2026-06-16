# NE Card / DeFiCard — Complete API Specification v1.0

## Base URL (Production)
```
https://api.deficards.io/v1
```

## Authentication
All admin/internal endpoints use **Bearer tokens** — API key in header:
```
Authorization: Bearer <hermes_api_key>
```
User endpoints use **JWT tokens** obtained via `/auth/login`.
Web 3 wallet auth uses signed message verification.

---

## 1. AUTH ENDPOINTS

### POST /auth/register
Create a new user account.
```json
// Request
{
  "email": "user@example.com",
  "password": "SecurePass123!",
  "first_name": "John",
  "last_name": "Doe",
  "phone": "+1234567890",
  "friend_code": "REF123"     // optional referral code
}

// Response 201
{
  "id": 42,
  "email": "user@example.com",
  "token": "eyJhbGciOiJI...",
  "kyc_status": "none"
}
```

### POST /auth/login
```json
// Request
{ "email": "user@example.com", "password": "SecurePass123!" }

// Response 200
{ "token": "eyJ...", "user": { "id": 42, "email": "..." } }
```

### POST /auth/wallet
Web 3 wallet authentication (MetaMask/Phantom).
```json
// Request
{
  "wallet_address": "0x1234...",
  "signature": "0x...",          // signed message
  "message": "Sign to login to DeFiCard"
}

// Response 200
{ "token": "eyJ...", "user": { "id": 42, "wallet_address": "0x1234..." } }
```

---

## 2. USER ENDPOINTS

### GET /users/me
Get current user's profile.
```json
// Response 200
{
  "id": 42,
  "email": "user@example.com",
  "first_name": "John",
  "last_name": "Doe",
  "phone": "+1234567890",
  "kyc_status": "approved",       // none | pending | in_process | approved | rejected
  "gateway_address": "TNoF...",   // USDT deposit address
  "created_at": "2026-06-16T15:34:26Z"
}
```

### PUT /users/me/password
```json
// Request
{ "current_password": "old", "new_password": "new", "confirm_password": "new" }
// Response 200
{ "message": "Password updated" }
```

---

## 3. KYC ENDPOINTS

### POST /kyc — Upload KYC documents
```json
// Request (multipart/form-data)
{
  "first_name": "John",
  "last_name": "Doe",
  "birthday": "1990-01-15",
  "email": "user@example.com",
  "phone": "+1234567890",
  "city": "Toronto",
  "street_address": "123 Main St",
  "street_address_2": "",
  "region_state_province": "Ontario",
  "zipcode": "M5V 2T6",
  "country": "CA",
  "file1": <binary - ID front>,
  "file2": <binary - ID back or selfie>
}

// Response 201
{ "id": 99, "status": "pending", "message": "Documents received for review" }
```

### GET /kyc — Check KYC status
```json
// Response 200
{
  "id": 99,
  "status": "pending",            // pending | in_process | approved | rejected | retry
  "status_message": "Please upload a clearer photo of your ID",
  "submitted_at": "2026-06-16T15:34:26Z"
}
```

### GET /kyc/all — List all KYC submissions (ADMIN only)
```json
// Query params: ?page=1&per_page=20&status=pending&search=john
// Response 200
{
  "data": [
    {
      "id": 99,
      "user_id": 42,
      "first_name": "John",
      "last_name": "Doe",
      "email": "user@example.com",
      "status": "pending",
      "created_at": "2026-06-16T15:34:26Z",
      "file_urls": { "front": "https://...", "back": "https://..." }
    }
  ],
  "total": 150,
  "page": 1,
  "per_page": 20
}
```

### POST /kyc/{id}/approve — Auto-approve KYC (ADMIN/HERMES)
```json
// Request
{ "status_message": "KYC verified successfully" }

// Response 200
{ "message": "KYC approved", "user_id": 42 }
```
**Side effects:**
- Sets `kyc_verifications.status = 'Approved'`
- Updates `users.kyc_status = 'approved'`
- Sends welcome email to user

### POST /kyc/{id}/reject — Reject KYC with reason (ADMIN/HERMES)
```json
// Request
{ "status_message": "ID document is blurry, please re-upload" }

// Response 200
{ "message": "KYC rejected", "user_id": 42 }
```

### PUT /kyc/{id} — Update KYC status message (ADMIN)
```json
// Request
{ "status_message": "Additional verification needed" }
// Response 200
{ "message": "KYC message updated" }
```

---

## 4. CARD PURCHASE ENDPOINTS

### POST /cards/purchase — Initiate a card purchase
```json
// Request
{ "card_type": "Visa" }           // "Visa" or "Mastercard"

// Response 201
{
  "payment_id": 101,
  "status": "Pending",
  "trans_address": "TNoF...",     // USDT address to send payment to
  "amount_usdt": 199.00,
  "qr_code_url": "https://api.deficards.io/qr/abc123.svg",
  "card_type": "Visa"
}
```
**Side effects:**
- Calls Heleket API to generate a USDT deposit address
- Creates `payments` record with `type='card'`, `status='Pending'`
- Generates QR code for the payment address

### GET /cards/purchases — List card purchases (ADMIN)
```json
// Query: ?page=1&per_page=20&status=Pending
// Response 200
{
  "data": [
    {
      "id": 101,
      "user": { "id": 42, "email": "user@example.com", "first_name": "John", "last_name": "Doe" },
      "card_type": "Visa",
      "status": "Pending",
      "trans_address": "TNoF...",
      "trans_amount": null,
      "created_at": "2026-06-16T15:34:26Z"
    }
  ],
  "total": 50,
  "page": 1
}
```

### POST /cards/purchases/{id}/approve — Approve card purchase (ADMIN/HERMES)
```json
// Request (optional — auto-detects from Heleket webhook if trans_amount is set)
{
  "card_holder_id": 9419,          // from NECard system
  "card_id": 9320,                 // from NECard system
  "card_number": "489512******1234"
}

// Response 200
{ "message": "Card purchase approved and activated" }
```
**Side effects:**
- Sets `payments.status = 'Approved'`
- Creates or updates `card_activations` with card_holder_id + card_id
- Sends user their card details email

### POST /cards/purchases/{id}/reject — Reject card purchase (ADMIN)
```json
// Request
{ "reason": "Insufficient payment received" }

// Response 200
{ "message": "Card purchase rejected" }
```

---

## 5. CARD MANAGEMENT ENDPOINTS

### GET /cards — List user's active cards
```json
// Response 200
{
  "cards": [
    {
      "id": 5,
      "card_type": "Visa",
      "card_holder_id": 9419,
      "card_id": 9320,
      "status": "Approved",        // Approved | Deactivated
      "balance": 1250.50,
      "last_four": "1234",
      "created_at": "2026-06-10T..."
    }
  ]
}
```

### GET /cards/{id}/balance — Get card balance (calls NECard API)
```json
// Response 200
{ "card_id": 5, "balance": 1250.50, "currency": "USD" }
```

### GET /cards/{id}/transactions — Get card transactions
```json
// Query: ?month=current&page=1
// Response 200
{
  "transactions": [
    {
      "date": "2026-06-15T14:30:00Z",
      "description": "AMAZON.COM",
      "amount": -45.99,
      "currency": "USD",
      "type": "PURCHASE",
      "status": "settled"
    }
  ]
}
```

### POST /cards/{id}/pin — Change card PIN
```json
// Request
{ "new_pin": "1234", "confirm_pin": "1234" }

// Response 200
{ "message": "PIN changed successfully" }
```
**Side effects:** Calls NECard SetPIN API.

### POST /cards/{id}/toggle — Activate/Deactivate card
```json
// Request
{ "action": "deactivate" }         // "activate" | "deactivate"

// Response 200
{ "message": "Card deactivated", "new_status": "Deactivated" }
```

### POST /cards/load — Initiate card load (user)
```json
// Request
{ "card_id": 5, "amount_usdt": 500.00 }

// Response 201
{
  "load_id": 202,
  "status": "Pending",
  "trans_address": "TNoF...",
  "qr_code_url": "https://api.deficards.io/qr/def456.svg"
}
```

### GET /cards/loads — List card loads (ADMIN)
```json
// Query: ?page=1&per_page=20&status=Pending
// Response 200: same pattern as /cards/purchases
```

### POST /cards/loads/{id}/confirm — Confirm and execute card load (ADMIN/HERMES)
```json
// Request (optional — auto-detected via Heleket webhook)
{}

// Response 200
{
  "message": "Card loaded successfully",
  "amount": 485.00,                // after fees
  "new_balance": 1735.50,
  "api_trans_id": 78452
}
```
**Side effects:**
- Calls NECard LoadCard API (visa via SP_24, mastercard via SP_37)
- Converts USDT amount to cents × 100
- Creates USDT transaction record
- Sends email notification to user

---

## 6. SUPPORT TICKET ENDPOINTS

### POST /tickets — Create a support ticket
```json
// Request (multipart — file optional)
{
  "subject": "Card not working",
  "message": "My card was declined at the store",
  "file": <optional binary>
}

// Response 201
{ "id": 55, "status": "open", "created_at": "..." }
```

### GET /tickets — List user's tickets
```json
// Response 200
{
  "tickets": [
    { "id": 55, "subject": "Card not working", "status": "open", "last_reply": "..." }
  ]
}
```

### GET /tickets/all — List all tickets (ADMIN)
```json
// Query: ?page=1&per_page=20&status=open
// Response 200
{
  "data": [
    {
      "id": 55,
      "user": { "id": 42, "email": "user@example.com", "name": "John Doe" },
      "subject": "Card not working",
      "message": "My card was declined...",
      "status": "open",
      "created_at": "2026-06-16T15:34:26Z"
    }
  ],
  "total": 30
}
```

### POST /tickets/{id}/reply — Reply to ticket (ADMIN/HERMES)
```json
// Request
{ "message": "I've checked your card and it appears active. Can you try again?" }

// Response 200
{ "message": "Reply sent", "ticket_id": 55 }
```

### POST /tickets/{id}/close — Close a ticket (ADMIN)
```json
// Response 200
{ "message": "Ticket closed" }
```

---

## 7. ADMIN / SYSTEM ENDPOINTS

### GET /admin/dashboard — Dashboard stats
```json
// Response 200
{
  "total_users": 1250,
  "kyc_pending": 23,
  "card_purchases_pending": 8,
  "card_activations_total": 450,
  "card_loads_pending": 3,
  "support_tickets_open": 12,
  "today_volume_usdt": 45230.50
}
```

### GET /admin/reports/kyc — KYC report
```json
// Query: ?range=weekly
// Response 200
{ "total": 150, "approved": 120, "rejected": 20, "pending": 10 }
```

### GET /admin/reports/transactions — Transaction report
```json
// Query: ?range=weekly
// Response 200
{ "total_volume": 125000.00, "total_fees": 2500.00, "transaction_count": 340 }
```

### GET /admin/health — System health check
```json
// Response 200
{
  "status": "healthy",
  "database": "ok",
  "necard_api_visa": "ok",
  "necard_api_mastercard": "ok",
  "heleket_gateway": "ok",
  "email_service": "ok",
  "uptime_seconds": 86400
}
```

### POST /admin/backup — Trigger database backup
```json
// Response 200
{ "message": "Backup initiated", "file": "backup_2026-06-16.sql.gz" }
```

---

## 8. WEBHOOKS (Heleket Payment Gateway)

### POST /webhooks/heleket/payment — Payment notification from Heleket
```json
// Request (from Heleket)
{
  "trans_address": "TNoF...",
  "amount": 199.50,
  "tx_id": "abc123def456",
  "currency": "USDT",
  "status": "confirmed",
  "confirmations": 12
}

// Response 200
{ "received": true }
```
**Business logic (auto-handled by Hermes):**
1. Look up `payments` record by `trans_address`
2. If `type=card` and amount ≥ threshold → auto-approve card purchase
3. If `type=load` or `type=USDT` → auto-confirm card load
4. Send notification email to user

---

## 9. NOTIFICATIONS (Email)

### POST /notifications/email — Send transactional email
```json
// Request (ADMIN/HERMES only)
{
  "to": "user@example.com",
  "template": "kyc_approved",      // kyc_approved | kyc_rejected | card_activated | 
                                    // card_loaded | welcome | ticket_reply
  "data": {
    "name": "John",
    "card_type": "Visa",
    "amount": 500.00
  }
}

// Response 200
{ "message": "Email sent" }
```

---

## DATABASE SCHEMA REFERENCE

```sql
-- Users table
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255),
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    middle_name VARCHAR(100),
    phone VARCHAR(20),
    wallet_address VARCHAR(255) NULL,     -- Web 3 wallet
    gateway_address VARCHAR(255) NULL,    -- USDT deposit address
    card_holder_id INT NULL,
    card_id INT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- KYC verifications
CREATE TABLE kyc_verifications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    birthday VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(20),
    city VARCHAR(255),
    street_address VARCHAR(255),
    street_address_2 VARCHAR(255),
    region_state_province VARCHAR(255),
    zipcode VARCHAR(255),
    country VARCHAR(255),
    file1 VARCHAR(255),                  -- ID document path
    file2 VARCHAR(255),                  -- Selfie or second doc
    status VARCHAR(50) DEFAULT 'Pending', -- Pending | In Process | Approved | Rejected | Retry
    status_message TEXT NULL,
    mastercard_kyc_url TEXT NULL,
    cardholder_kyc_file_id VARCHAR(255) NULL,
    user_id FOREIGN KEY REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Payments (card purchases, card loads, USDT deposits)
CREATE TABLE payments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    type VARCHAR(50) NOT NULL,           -- 'card' | 'load' | 'USDT' | 'kyc'
    card_type VARCHAR(50) NULL,          -- 'Visa' | 'Mastercard'
    card_holder_id INT NULL,
    name VARCHAR(255),
    file VARCHAR(255),
    text TEXT,
    status VARCHAR(50) DEFAULT 'Pending', -- Pending | Approved | In Process | Rejected
    trans_address VARCHAR(255) NULL,     -- Heleket deposit address
    trans_amount DECIMAL(16,2) NULL,
    trans_fee DECIMAL(16,2) NULL,
    trans_id VARCHAR(255) NULL,
    trans_loaded BOOLEAN DEFAULT FALSE,
    tx_id VARCHAR(255) NULL,
    api_trans_id INT NULL,
    api_status INT NULL,
    api_response TEXT NULL,
    user_id FOREIGN KEY REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Card activations (issued cards)
CREATE TABLE card_activations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    card_holder_id INT NULL,             -- NECard system ID
    card_id INT NULL,                    -- NECard system ID
    card_type VARCHAR(50),               -- 'Visa' | 'Mastercard'
    number VARCHAR(255),
    kit_number VARCHAR(255),
    status VARCHAR(50) DEFAULT 'Pending', -- Pending | Approved | Deactivated
    user_id FOREIGN KEY REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Support tickets
CREATE TABLE support_tickets (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    subject VARCHAR(255),
    message TEXT,
    status VARCHAR(50) DEFAULT 'open',    -- open | closed
    user_id FOREIGN KEY REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Ticket replies
CREATE TABLE ticket_replies (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    ticket_id BIGINT NOT NULL,
    user_id BIGINT NULL,                 -- null = system/admin reply
    message TEXT NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    ticket_id FOREIGN KEY REFERENCES support_tickets(id),
    created_at TIMESTAMP
);

-- User progress tracking
CREATE TABLE user_progress (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    progress_status VARCHAR(100),
    details TEXT,
    user_id FOREIGN KEY REFERENCES users(id),
    created_at TIMESTAMP
);
```

---

## COMPLETE ENDPOINT MAP

```
AUTH
  POST   /auth/register
  POST   /auth/login
  POST   /auth/wallet

USERS
  GET    /users/me
  PUT    /users/me
  PUT    /users/me/password

KYC
  POST   /kyc
  GET    /kyc
  GET    /kyc/all                        ADMIN
  POST   /kyc/{id}/approve               ADMIN/HERMES
  POST   /kyc/{id}/reject                ADMIN/HERMES
  PUT    /kyc/{id}                       ADMIN

CARDS — Purchase
  POST   /cards/purchase
  GET    /cards/purchases                ADMIN
  POST   /cards/purchases/{id}/approve   ADMIN/HERMES
  POST   /cards/purchases/{id}/reject    ADMIN

CARDS — Management
  GET    /cards
  GET    /cards/{id}/balance
  GET    /cards/{id}/transactions
  POST   /cards/{id}/pin
  POST   /cards/{id}/toggle

CARDS — Loading
  POST   /cards/load
  GET    /cards/loads                    ADMIN
  POST   /cards/loads/{id}/confirm       ADMIN/HERMES

TICKETS
  POST   /tickets
  GET    /tickets
  GET    /tickets/all                    ADMIN
  POST   /tickets/{id}/reply             ADMIN/HERMES
  POST   /tickets/{id}/close             ADMIN

ADMIN
  GET    /admin/dashboard
  GET    /admin/reports/kyc
  GET    /admin/reports/transactions
  GET    /admin/reports/cards
  GET    /admin/health
  POST   /admin/backup

WEBHOOKS
  POST   /webhooks/heleket/payment       (from Heleket)

NOTIFICATIONS
  POST   /notifications/email            ADMIN/HERMES
```

---

## ENVIRONMENT VARIABLES

```bash
# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=necard
DB_USERNAME=root
DB_PASSWORD=secret

# App
APP_URL=https://deficards.io
APP_ENV=production
APP_DEBUG=false

# NECard Visa API (ServiceProvider_24)
NECARD_VISA_API_URL=https://api.necard.com/
NECARD_VISA_USER=username
NECARD_VISA_PASSWORD=password
NECARD_VISA_ACCOUNT_ID=200
NECARD_VISA_WALLET_ID=311

# NECard Mastercard API (ServiceProvider_37)
NECARD_MC_API_URL=https://api.necard-mc.com/
NECARD_MC_USER=username
NECARD_MC_PASSWORD=password
NECARD_MC_ACCOUNT_ID=200
NECARD_MC_WALLET_ID=311

# Heleket Payment Gateway
HELEKET_API_URL=https://api.heleket.com/
HELEKET_API_KEY=key
HELEKET_CARD_FEE=199.00
HELEKET_WEBHOOK_SECRET=secret

# Hermes Orchestrator
HERMES_API_KEY=api_key_hermes_uses
HERMES_TELEGRAM_BOT=bot_token

# Email (SendGrid or SMTP)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxxxxxx
MAIL_FROM_ADDRESS=support@deficards.io
MAIL_FROM_NAME="DeFiCard Support"
```
