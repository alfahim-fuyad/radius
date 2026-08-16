# RADIUS — Hyperlocal Secondhand Marketplace

<p align="center">

**Discover Locally. Trade Securely. Build Trust.**

A secure hyperlocal secondhand marketplace built with **PHP, MySQL, Python/FastAPI, and explainable AI-based fraud-risk analysis.**

</p>

---

## 📌 Overview

**RADIUS** is a CSE479 academic mini-project that implements a secure, location-aware secondhand marketplace for buying, selling, chatting, trading, reviewing, and discovering products nearby.

The system combines a traditional **PHP + MySQL marketplace** with a dedicated **Python/FastAPI AI service** that analyzes listings and generates an explainable **0–100 fraud-risk score**.

> ⚠️ **Important:** The trust layer produces risk signals, not proof that a user or listing is fraudulent. Suspicious/high-risk listings are routed to human moderation.

---

## ✨ Features

### 🛍️ Marketplace

- Guest browse, search, and filter
- Category-based marketplace
- Nearby-distance discovery
- Listing creation, editing, and deletion
- Product condition and seller pricing
- Secure local image uploads
- Buyer and seller profiles
- Listing status management
- Product reporting
- Reviews and ratings

### 🔐 Authentication & Authorization

- Registration / Login / Logout
- PHP session-based authentication
- `password_hash()` / `password_verify()`
- Role-based authorization
- Admin access control
- CSRF protection
- PDO prepared statements
- Output escaping with `htmlspecialchars()`

### 💬 Chat & Trading

- Buyer-seller private chat
- Lightweight 3-second AJAX polling
- Trade request system
- Trade state management

```text
Requested
   │
   ├── Accepted ──► Completed
   │
   ├── Rejected
   │
   └── Cancelled
```

### 🛡️ Trust & Safety

- Trust Radar visualization
- Explainable fraud-risk analysis
- Suspicious listing detection
- User/listing reporting
- Admin fraud queue
- Detailed risk panels
- Human moderation
- Graceful AI-service failure
- Admin retry for failed analysis

---

## 🤖 Explainable Fraud-Risk Analysis

The AI service exposes:

```text
POST /analyze-listing
```

and returns an explainable **0–100 risk score** based on five major components.

| Component | Weight | Analysis |
|---|---:|---|
| 🖼️ Image Similarity | 25% | pHash + Hamming distance |
| 💰 Price Anomaly | 25% | RandomForestRegressor |
| 👤 Seller Risk | 20% | Account/reputation/trade signals |
| 📝 Text Risk | 20% | TF-IDF + MultinomialNB |
| 🏷️ Policy / Brand Risk | 10% | Prohibited phrases & brand mismatch |

### Risk Bands

| Score | Risk Level |
|---:|---|
| `0–29` | 🟢 Safe |
| `30–49` | 🟡 Low Risk |
| `50–69` | 🟠 Suspicious |
| `70–100` | 🔴 High Risk |

---

## 🔍 Risk Components

### 🖼️ Image Similarity — 25%

Uses perceptual hashing (**pHash**) and Hamming distance to identify potentially reused or highly similar product images.

```text
Uploaded Image
      │
      ▼
    pHash
      │
      ▼
Hamming Distance
      │
      ├── ≤ 5   → High similarity
      ├── 6–10  → Medium similarity
      └── > 10  → Lower similarity
```

### 💰 Price Anomaly — 25%

A `RandomForestRegressor` estimates an expected market price and compares it with the seller's listed price.

```text
Listed Price
     │
     ▼
Expected Market Price
     │
     ▼
Price Difference
     │
     ▼
Price Risk Signal
```

### 👤 Seller Risk — 20%

Seller-related signals include:

- Account age
- Previous reports
- Removed/suspicious listings
- Completed trades
- Review history
- Seller activity

### 📝 Text Risk — 20%

Listing text is analyzed using:

- TF-IDF
- Multinomial Naive Bayes
- Reused-text detection

### 🏷️ Policy / Brand Risk — 10%

Checks for potentially suspicious:

- Prohibited/off-platform phrases
- Policy-related patterns
- Recognizable brand mismatch

---

# 📍 Hyperlocal Discovery

RADIUS is designed around **nearby secondhand trading**.

Listings can store:

- Latitude
- Longitude
- Location information
- Distance from the current user

Distance is calculated using the **Haversine formula**.

```text
User Location
      │
      ▼
Listing Latitude / Longitude
      │
      ▼
Haversine Formula
      │
      ▼
Distance in KM
      │
      ▼
Nearby Listings
```

---

# 🏗️ System Architecture

```text
                         ┌──────────────────┐
                         │       USER       │
                         │    Web Browser   │
                         └────────┬─────────┘
                                  │
                                  ▼
                         ┌──────────────────┐
                         │      RENDER      │
                         │   RADIUS Web App │
                         └───────┬──────────┘
                                 │
                ┌────────────────┼────────────────┐
                │                │                │
                ▼                ▼                ▼
        ┌──────────────┐  ┌──────────────┐  ┌──────────────┐
        │ Aiven MySQL  │  │   FastAPI    │  │   SerpAPI    │
        │   Database   │  │  AI Service  │  │ External API │
        └──────────────┘  └───────┬──────┘  └──────────────┘
                                  │
                                  ▼
                         ┌──────────────────┐
                         │  Risk Analysis   │
                         │                  │
                         │ Price            │
                         │ Image            │
                         │ Text             │
                         │ Seller           │
                         │ Policy / Brand   │
                         └──────────────────┘
```

---

# 🔄 Development & Deployment Flow

```text
                    👨‍💻 Developer
                          │
                          ▼
                    ┌───────────┐
                    │  VS Code  │
                    └─────┬─────┘
                          │
                          │ Git
                          ▼
                    ┌───────────┐
                    │  GitHub   │
                    └─────┬─────┘
                          │
                          │ Deploy
                          ▼
                    ┌───────────┐
                    │  Render   │
                    └─────┬─────┘
                          │
             ┌────────────┼────────────┐
             │            │            │
             ▼            ▼            ▼
       ┌──────────┐ ┌──────────┐ ┌──────────┐
       │  Aiven   │ │ FastAPI  │ │ SerpAPI  │
       │  MySQL   │ │ AI       │ │ External │
       │ Database │ │ Service  │ │   API    │
       └──────────┘ └──────────┘ └──────────┘
```

---

# 🔗 Application & AI Service Flow

```text
User Creates Listing
        │
        ▼
PHP Application
        │
        ├───────────────► Aiven MySQL
        │
        ▼
FastAPI AI Service
        │
        ├── Image Analysis
        ├── Price Analysis
        ├── Seller Analysis
        ├── Text Analysis
        └── Policy / Brand Analysis
                │
                ▼
          Risk Score 0–100
                │
                ▼
          JSON Response
                │
                ▼
        PHP Application
                │
                ▼
          Trust Radar
```

If the AI service is unavailable, the listing can still persist and an administrator can retry the analysis later.

---

# 🔎 SerpAPI Integration

SerpAPI can be used as an external search API for product and market-related information.

```text
RADIUS
   │
   │ API Request
   ▼
SerpAPI
   │
   ▼
Search / Product Information
   │
   ▼
RADIUS
   │
   ▼
Market / Price Reference
```

Environment variable:

```text
SERPAPI_KEY=your_api_key
```

> ⚠️ Never commit your actual API key to GitHub.

---

# 🧰 Required Stack

### Main Application

- PHP 8+
- HTML5
- CSS3
- Vanilla JavaScript
- MySQL
- PHP Sessions
- PDO

### AI Service

- Python 3
- FastAPI
- pandas
- scikit-learn
- Pillow
- ImageHash

### Development & Deployment

- Visual Studio Code
- Git
- GitHub
- Render
- Aiven MySQL
- XAMPP

> This project does **not** require React, Vite, Node.js, Express, Socket.io, or Supabase.

---

# 📁 Main Project Structure

```text
RADIUS/
│
├── admin/                  # Moderation pages
├── api/                    # Chat, trade & fraud endpoints
├── assets/                 # CSS, JavaScript & frontend assets
├── config/                 # Application & PDO configuration
├── includes/               # Auth, CSRF & shared helpers
├── uploads/                # Listing/profile images
│
├── ai_service/             # FastAPI AI service
│   ├── training/
│   └── requirements.txt
│
├── index.php               # Home page
├── listings.php            # Marketplace
├── listing.php             # Listing details
├── create-listing.php      # Listing creation
├── messages.php            # Messaging
├── chat.php                # Chat interface
├── trade-requests.php      # Trade management
├── trust-radar.php         # Trust visualization
├── database.sql            # Database schema
├── seed.php                # Demo data
├── run.sh                  # Run services
└── README.md
```

---

# 🗄️ Database

RADIUS uses **MySQL** for persistent marketplace data.

Main entities:

```text
users
listings
listing_images
conversations
messages
trade_requests
reviews
reports
fraud_predictions
price_data
```

---

# 🚀 Local Setup

## 1. Create Database

```bash
mysql -u root -p < database.sql
```

## 2. Configure Environment Variables

```bash
export DB_HOST=127.0.0.1
export DB_PORT=3306
export DB_NAME=radius
export DB_USER=root
export DB_PASSWORD='your-password'
export AI_SERVICE_URL=http://127.0.0.1:8001
```

## 3. Install AI Dependencies

```bash
python3 -m pip install -r ai_service/requirements.txt
```

## 4. Generate Academic Datasets

Optional:

```bash
python3 ai_service/training/generate_datasets.py 15000 20000
```

## 5. Seed Demo Data

```bash
php seed.php
```

## 6. Run Both Services

```bash
bash run.sh
```

## 7. Open the Application

```text
http://localhost:3000
```

FastAPI health check:

```text
http://127.0.0.1:8001/health
```

FastAPI documentation:

```text
http://127.0.0.1:8001/docs
```

---

4. Import the database schema:

```bash
mysql ... < database.sql
```

5. Seed demo data:

```bash
php seed.php
```

6. Click **Run**.

`.replit` invokes:

```bash
bash run.sh
```

FastAPI uses local port `8001`, while PHP uses Replit's `$PORT`.

---

# ☁️ Production Deployment

Production workflow:

```text
VS Code
   │
   ▼
Git
   │
   ▼
GitHub
   │
   ▼
Render
   │
   ├── RADIUS Application
   │
   └── FastAPI AI Service
          │
          ├── Aiven MySQL
          │
          └── SerpAPI
```

### Git Workflow

```bash
git add .
git commit -m "Update RADIUS"
git push origin main
```

Render can then build and deploy the latest version from GitHub.

### Production Environment Variables

```text
DB_HOST
DB_PORT
DB_NAME
DB_USER
DB_PASSWORD
AI_SERVICE_URL
SERPAPI_KEY
```

> ⚠️ Keep all production credentials and API keys inside the deployment platform's environment/secret settings.

---

# 👤 Demo Accounts

After running:

```bash
php seed.php
```

all demo accounts use:

```text
Password: RadiusDemo123!
```

| Role | Email |
|---|---|
| Admin | `admin@radius.test` |
| Seller | `seller@radius.test` |
| Buyer | `buyer@radius.test` |
| User | `nadia@radius.test` |

The seed script creates at least **20 realistic listings** with varied categories and trust states.

---

# 🔌 API

## FastAPI

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/health` | Service health check |
| `POST` | `/hash-image` | Generate perceptual image hash |
| `POST` | `/analyze-listing` | Generate explainable risk result |

## PHP Action Endpoints

| Endpoint | Purpose |
|---|---|
| `/api/chat.php` | Start/send/poll conversation messages |
| `/api/trade.php` | Request/accept/reject/cancel/complete trades |
| `/api/fraud.php` | Admin approve/remove/retry analysis |

---

# 🔐 Security

RADIUS implements multiple security measures:

- Password hashing with `password_hash()`
- Password verification with `password_verify()`
- PHP session-based authentication
- CSRF protection
- PDO prepared statements
- SQL injection prevention
- Output escaping
- Role-based authorization
- Secure image uploads
- MIME validation
- Image decoding validation
- File-size validation
- Randomized upload filenames
- Protected private conversations
- Admin-only moderation actions
- Environment-based secrets

### Image Upload Protection

Uploaded images are validated by:

```text
Upload Error
     │
     ▼
File Size
     │
     ▼
Extension
     │
     ▼
MIME Type
     │
     ▼
Image Decoding
     │
     ▼
Random Filename
     │
     ▼
Secure Storage
```

> ⚠️ Never commit `.env`, production credentials, database passwords, or API keys.

---

# 🧪 Testing Workflow

## Main User Flow

```text
Guest
  ↓
Browse
  ↓
Register
  ↓
Login
  ↓
Create Listing
  ↓
Upload Image
  ↓
Fraud Analysis
  ↓
Listing / Moderation
  ↓
Another User Views Listing
  ↓
Chat
  ↓
Trade Request
  ↓
Seller Accepts
  ↓
Complete Trade
  ↓
Review
```

## Additional Tests

```text
Suspicious Listing
      ↓
Fraud Queue
      ↓
Full Risk Explanation
      ↓
Admin Approve / Remove
```

Also test:

- Report flow
- Duplicate review protection
- Duplicate trade protection
- AI service unavailable
- AI analysis retry
- Invalid image upload
- Unauthorized admin access
- Unauthorized chat access
- Invalid trade transitions
- Session/authentication failures

---

# 🎯 Project Objective

The main objective of RADIUS is to build a **secure, explainable, and location-aware secondhand marketplace** that combines traditional web technologies with AI-assisted trust and fraud-risk analysis.

The project demonstrates integration between:

```text
PHP
 +
MySQL
 +
Python / FastAPI
 +
Machine Learning
 +
Image Analysis
 +
External API
 +
Location Services
 +
Cloud Deployment
```

---

# 📊 Technology Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Backend | PHP 8+ |
| Database | MySQL / Aiven MySQL |
| Database Access | PDO |
| Authentication | PHP Sessions |
| AI Service | Python / FastAPI |
| ML | pandas, scikit-learn |
| Image Analysis | Pillow, ImageHash |
| External API | SerpAPI |
| Location | Latitude / Longitude |
| Distance | Haversine Formula |
| Development | Visual Studio Code |
| Version Control | Git / GitHub |
| Deployment | Render |
| Local Environment | XAMPP / Replit |

---

# 📚 Academic Context

| Item | Details |
|---|---|
| **Project** | RADIUS — Hyperlocal Secondhand Marketplace |
| **Course** | CSE479 |
| **Project Type** | Academic Mini-Project |
| **University** | East West University |
| **Backend** | PHP |
| **Database** | MySQL / Aiven MySQL |
| **AI Service** | Python / FastAPI |
| **Deployment** | Render |
| **External API** | SerpAPI |

---

# 🔮 Future Improvements

- Real-time WebSocket messaging
- Mobile application
- Map-based marketplace
- Improved deep-learning image analysis
- Advanced recommendation system
- Better price prediction
- Automated moderation
- More advanced seller reputation scoring
- Payment integration
- Push notifications
- Expanded fraud datasets
- Model performance monitoring

---

# ⚠️ Disclaimer

RADIUS is an **academic project developed for educational and demonstration purposes**.

The AI-generated risk score is an assistive signal and should **not** be treated as definitive proof of fraud.

A high-risk score indicates that a listing contains multiple suspicious signals and may require further investigation by a human administrator.

---

<p align="center">

## ⭐ RADIUS

**Discover Locally. Trade Securely. Build Trust.**

</p>
