# RADIUS — Hyperlocal Secondhand Marketplace

> A secure PHP/MySQL marketplace for buying, selling, chatting, trading, and discovering secondhand products nearby — powered by an explainable AI-based trust and fraud-risk analysis service.

<p align="center">
  <strong>RADIUS</strong> — Discover Locally. Trade Securely. Build Trust.
</p>

---

## 📌 Overview

**RADIUS** is a CSE479 mini-project that implements a **hyperlocal secondhand marketplace** using PHP and MySQL, with a dedicated Python/FastAPI service for explainable fraud-risk analysis.

Users can browse nearby listings, create and manage products, communicate with buyers and sellers, request trades, leave reviews, and report suspicious activity.

The trust system analyzes listings using multiple machine-learning and rule-based signals and provides a **0–100 risk score** to assist human moderation.

> ⚠️ **Important:** The trust system produces risk signals, not proof of fraud. Suspicious or high-risk listings are reviewed by human administrators.

---

## ✨ Features

### 🛍️ Marketplace

* Browse, search, and filter listings
* Nearby-distance discovery
* Category-based marketplace
* Listing creation and management
* Secure image uploads
* Buyer and seller profiles
* Reviews and reporting

### 💬 Communication

* Buyer–seller private chat
* Lightweight AJAX polling
* Participant-based conversation authorization

### 🤝 Trading

* Trade request system
* Accept / reject requests
* Cancel and complete trades
* Duplicate trade protection
* Trade-state validation

### 🛡️ Trust & Fraud Detection

* Explainable 0–100 risk score
* Image similarity detection
* Price anomaly detection
* Seller risk analysis
* Text-based risk detection
* Policy and brand risk analysis
* Admin fraud queue
* Manual moderation
* AI analysis retry support

### 🔐 Security

* PHP session authentication
* `password_hash()` / `password_verify()`
* CSRF protection
* PDO prepared statements
* Role-based authorization
* Output escaping
* MIME and image validation
* Randomized upload filenames
* Protected private conversations

---

## 🤖 Explainable Fraud-Risk Analysis

Each listing receives a risk score based on five components:

| Component            | Weight | Technology                         |
| -------------------- | -----: | ---------------------------------- |
| 🖼️ Image Similarity |    25% | pHash + Hamming Distance           |
| 💰 Price Anomaly     |    25% | Random Forest Regressor            |
| 👤 Seller Risk       |    20% | Account, reports, trades & reviews |
| 📝 Text Risk         |    20% | TF-IDF + MultinomialNB             |
| ⚠️ Policy/Brand Risk |    10% | Rule-based analysis                |

### Risk Levels

|    Score | Risk Level    |
| -------: | ------------- |
|   `0–29` | 🟢 Safe       |
|  `30–49` | 🟡 Low Risk   |
|  `50–69` | 🟠 Suspicious |
| `70–100` | 🔴 High Risk  |

### Image Similarity

Perceptual hashing is used to detect potentially reused or visually similar listing images.

* Hamming distance `≤ 5` → High similarity
* Hamming distance `6–10` → Medium similarity

---

## 🏗️ Architecture

```text
                    ┌──────────────────────┐
                    │      Web Browser     │
                    │ HTML / CSS / JS      │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │    PHP Application   │
                    │ Marketplace / Auth   │
                    │ Chat / Trade / Admin │
                    └───────┬────────┬─────┘
                            │        │
                            ▼        ▼
                     ┌──────────┐  ┌──────────────┐
                     │  MySQL   │  │ FastAPI AI   │
                     │ Database │  │ Trust Service│
                     └──────────┘  └──────┬───────┘
                                          │
                          ┌───────────────┼───────────────┐
                          ▼               ▼               ▼
                       Image           Price            Text
                       Risk            Risk             Risk
                          └───────────────┼───────────────┘
                                          ▼
                                  Risk Score 0–100
                                          │
                                          ▼
                                  Human Moderation
```

---

## 🧰 Tech Stack

### Main Application

* PHP 8+
* MySQL
* PDO
* HTML5
* CSS3
* Vanilla JavaScript
* PHP Sessions

### AI Service

* Python 3
* FastAPI
* pandas
* scikit-learn
* Pillow
* ImageHash

### No Dependencies On

* React
* Vite
* Node.js
* Express
* Socket.io
* Supabase

## 🚀 Deploying the GitHub project to Render with Aiven MySQL

This repository includes a `Dockerfile` and `render.yaml` for deploying the PHP
marketplace and its FastAPI trust service together as one Render web service.
The application keeps its existing PHP/MySQL architecture; Aiven supplies the
external MySQL database.

1. In Render, choose **New > Blueprint** and select this GitHub repository.
2. Confirm the `radius` web service from `render.yaml`.
3. Add these environment variables in Render:
   * `APP_URL`: the final public Render URL
   * `DB_HOST`: the Aiven MySQL host
   * `DB_PORT`: the Aiven MySQL port shown in Aiven (do not assume `3306`)
   * `DB_USER`: the Aiven MySQL user
   * `DB_PASSWORD`: the Aiven MySQL password
4. Keep `DB_NAME=defaultdb` and `DB_SSL_MODE=REQUIRED`. Download the CA
   certificate from Aiven and paste its PEM contents into Render's
   `DB_SSL_CA_PEM` secret environment variable; the startup script writes it to
   a temporary file and PHP verifies the Aiven server certificate.
5. Import `database.sql` into Aiven's `defaultdb` database before first use.
   The SQL file creates the `radius` database for local MySQL, so when using
   Aiven run the table statements against `defaultdb` (or remove the
   `CREATE DATABASE` / `USE radius` lines first).

Do not commit Aiven passwords, CA material, or other credentials. If a
credential was pasted into a file or chat, rotate it in Aiven and store the
replacement only in Render's environment-variable settings.

---

## 📁 Project Structure

```text
radius/
│
├── admin/                  # Admin & moderation
├── api/                    # Chat, trade & fraud APIs
├── assets/                 # CSS & JavaScript
├── config/                 # Application configuration
├── includes/               # Auth, CSRF & helpers
├── uploads/                # Uploaded images
│
├── ai_service/             # FastAPI AI service
│   ├── training/
│   └── requirements.txt
│
├── index.php               # Homepage
├── listings.php            # Marketplace
├── listing.php             # Listing details
├── create-listing.php      # Create listing
├── messages.php            # Messages
├── chat.php                # Chat interface
├── trade-requests.php      # Trade management
├── trust-radar.php         # Trust visualization
├── database.sql            # Database schema
├── seed.php                # Demo data
└── run.sh                  # Startup script
```

---

## ⚙️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/alfahim-fuyad/radius.git
cd radius
```

### 2. Create the Database

```bash
mysql -u root -p < database.sql
```

### 3. Configure Environment Variables

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=radius
DB_USER=root
DB_PASSWORD=your-password
AI_SERVICE_URL=http://127.0.0.1:8001
```

### 4. Install AI Dependencies

```bash
python3 -m pip install -r ai_service/requirements.txt
```

### 5. Seed Demo Data

```bash
php seed.php
```

### 6. Run the Application

```bash
bash run.sh
```

Open:

```text
http://localhost:3000
```

FastAPI health check:

```text
http://127.0.0.1:8001/health
```

---

## 👤 Demo Accounts

After running `php seed.php`:

| Role   | Email                | Password         |
| ------ | -------------------- | ---------------- |
| Admin  | `admin@radius.test`  | `RadiusDemo123!` |
| Seller | `seller@radius.test` | `RadiusDemo123!` |
| Buyer  | `buyer@radius.test`  | `RadiusDemo123!` |
| User   | `nadia@radius.test`  | `RadiusDemo123!` |

> These credentials are intended for local/demo use only.

---

## 🔌 API

### FastAPI

```text
GET  /health
POST /hash-image
POST /analyze-listing
```

### PHP APIs

```text
/api/chat.php
/api/trade.php
/api/fraud.php
```

| Endpoint         | Purpose                       |
| ---------------- | ----------------------------- |
| `/api/chat.php`  | Start, send and poll messages |
| `/api/trade.php` | Manage trade requests         |
| `/api/fraud.php` | Admin fraud actions           |

---

## 🔐 Security

RADIUS implements multiple security measures:

* Password hashing with PHP's password API
* PDO prepared statements
* CSRF protection
* Session-based authentication
* Role-based authorization
* Input/output validation
* `htmlspecialchars()` output escaping
* Secure image MIME validation
* Image decoding validation
* Randomized upload filenames
* Private conversation authorization
* Trade-state authorization

> Never commit `.env` files, database passwords, API keys, or production credentials to the repository.

---

## 🧪 Test Workflow

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
AI Fraud Analysis
  ↓
Listing / Moderation
  ↓
View Listing
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

Additional scenarios:

* Suspicious listing → Fraud Queue → Admin Review
* Listing approval/removal
* User reporting
* Duplicate review protection
* Duplicate trade protection
* AI service unavailable
* AI analysis retry
* Unauthorized admin access
* Unauthorized chat access
* Invalid image upload
* Invalid trade-state transition

---

## 📄 License

This project was developed as a **CSE479 academic mini-project**.

---

<p align="center">
  <strong>RADIUS</strong><br>
  Discover Locally. Trade Securely. Build Trust.
</p>
