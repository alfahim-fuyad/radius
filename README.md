````markdown
# RADIUS — Hyperlocal Secondhand Marketplace

<p align="center">

**RADIUS — Discover Locally. Trade Securely. Build Trust.**

A secure hyperlocal secondhand marketplace built with PHP, MySQL, and an explainable AI-based fraud-risk analysis service.

</p>

---

## 📌 Overview

**RADIUS** is a CSE479 academic mini-project that implements a secure, hyperlocal secondhand marketplace for buying, selling, chatting, trading, reviewing, and discovering products nearby.

The project combines a traditional **PHP + MySQL marketplace** with a dedicated **Python/FastAPI AI service** that analyzes listings and generates an explainable **0–100 fraud-risk score**.

Users can:

- Browse nearby secondhand products
- Search and filter listings
- Create and manage listings
- Upload product images
- Chat privately with buyers and sellers
- Send and manage trade requests
- Leave reviews
- Report suspicious listings
- View trust and risk information

The AI service analyzes multiple signals including image similarity, price anomaly, seller behavior, text patterns, and policy/brand risks.

> ⚠️ **Important:** The AI trust system produces risk signals, not proof of fraud. Suspicious and high-risk listings are intended for human administrator review.

---

# ✨ Features

## 🛍️ Marketplace

- Browse secondhand products
- Search listings
- Category-based marketplace
- Advanced filtering
- Nearby-distance discovery
- Listing details
- Create listings
- Edit listings
- Remove listings
- Seller profiles
- Buyer profiles
- Product condition information
- Seller-entered pricing
- Secure image uploads
- Product reporting
- Reviews and ratings

---

## 📍 Hyperlocal Discovery

RADIUS is designed around **nearby secondhand trading**.

Listings can contain:

- Latitude
- Longitude
- Location information
- Distance from the current user

Distance calculations use the **Haversine formula**.

```text
User Location
      │
      ▼
Listing Latitude / Longitude
      │
      ▼
Haversine Distance Calculation
      │
      ▼
Distance in KM
      │
      ▼
Nearby Listings
````

---

# 💬 Communication

RADIUS includes a private buyer–seller messaging system.

### Features

* Start conversations
* Send messages
* Poll for new messages
* Conversation history
* Participant-based authorization
* Unauthorized conversation protection
* AJAX-based communication

Private conversations can only be accessed by authorized participants.

---

# 🤝 Trading System

RADIUS supports direct trading between buyers and sellers.

### Trade Features

* Create trade requests
* Accept trade requests
* Reject trade requests
* Cancel trade requests
* Complete trades
* Trade-state validation
* Duplicate trade protection
* Buyer/seller authorization

### Trade Workflow

```text
Buyer
  │
  ▼
View Listing
  │
  ▼
Send Trade Request
  │
  ▼
Seller Receives Request
  │
  ├── Reject
  │
  └── Accept
        │
        ▼
      Trade
        │
        ▼
   Complete Trade
        │
        ▼
      Review
```

---

# 🛡️ Trust & Fraud Detection

RADIUS includes an explainable AI-based trust system.

Every analyzed listing can receive a **0–100 fraud-risk score**.

## Risk Components

| Component              | Weight | Technology                         |
| ---------------------- | -----: | ---------------------------------- |
| 🖼️ Image Similarity   |    25% | pHash + Hamming Distance           |
| 💰 Price Anomaly       |    25% | Random Forest Regressor            |
| 👤 Seller Risk         |    20% | Account, reports, trades & reviews |
| 📝 Text Risk           |    20% | TF-IDF + MultinomialNB             |
| ⚠️ Policy / Brand Risk |    10% | Rule-based analysis                |

---

# 📊 Risk Levels

|    Score | Risk Level    |
| -------: | ------------- |
|   `0–29` | 🟢 Safe       |
|  `30–49` | 🟡 Low Risk   |
|  `50–69` | 🟠 Suspicious |
| `70–100` | 🔴 High Risk  |

### Example

```text
Fraud Score = 72
       │
       ▼
High Risk
       │
       ▼
Admin Fraud Queue
       │
       ▼
Human Review
```

The AI score is a **risk indicator**, not a final fraud verdict.

---

# 🖼️ Image Similarity Detection

RADIUS uses **perceptual hashing (pHash)** to detect potentially reused or visually similar listing images.

Unlike normal file hashes, perceptual hashes are designed to represent visual characteristics of an image.

### Image Analysis

```text
Uploaded Image
      │
      ▼
Image Validation
      │
      ▼
Perceptual Hash
      │
      ▼
Compare Existing Image Hashes
      │
      ▼
Hamming Distance
      │
      ▼
Similarity Risk
```

### Hamming Distance

```text
Distance ≤ 5
→ High Similarity

Distance 6–10
→ Medium Similarity
```

> Image similarity is treated as a risk signal and does not automatically prove that an image is fraudulent.

---

# 💰 Price Anomaly Detection

The seller's original listing price is preserved.

The AI service receives the original price as a **read-only input** and calculates a price anomaly/risk score.

### Important Design Rule

```text
Seller-entered price
        │
        ▼
Original price preserved
        │
        ▼
AI receives price
        │
        ▼
Price anomaly analysis
        │
        ▼
Price risk score
```

The AI does **not** replace the seller's actual product price.

For example:

```text
Product Price:
৳50,000

Price Risk Score:
82/100
```

The `82` represents **risk**, not the product price.

Therefore:

```text
Product Price
      ≠
Price Risk Score
```

---

# 👤 Seller Risk Analysis

Seller risk is calculated using multiple account-level signals.

The system can consider:

* Account age
* Number of previous listings
* Number of reports
* Completed trades
* Removed listings
* Previous suspicious listings
* Average review rating

Conceptually:

```text
Account Age
     +
Previous Listings
     +
Reports
     +
Completed Trades
     +
Removed Listings
     +
Suspicious History
     +
Average Rating
     │
     ▼
Seller Risk Score
```

---

# 📝 Text Risk Analysis

Listing text is analyzed using machine-learning techniques.

### Pipeline

```text
Listing Title
     +
Listing Description
     │
     ▼
Text Processing
     │
     ▼
TF-IDF
     │
     ▼
Multinomial Naive Bayes
     │
     ▼
Text Risk Score
```

The generated text-risk signal is combined with the other fraud-risk components.

---

# ⚠️ Policy & Brand Risk

A rule-based analyzer evaluates potentially risky listing content.

Possible signals include:

* Suspicious promotional language
* Policy violations
* Risky claims
* Brand-related concerns
* Unusual listing patterns

The result contributes to the overall risk score.

---

# 🤖 Explainable AI

RADIUS is designed to provide more than a simple fraud label.

The AI service can return:

* Fraud score
* Image score
* Price score
* Seller score
* Text score
* Policy score
* Trust status
* Model name
* Model version
* Explanation
* Feature snapshot

### Explainable Pipeline

```text
                         Listing
                            │
          ┌─────────────────┼─────────────────┐
          │                 │                 │
          ▼                 ▼                 ▼
       Image              Price             Seller
        Risk               Risk              Risk
          │                 │                 │
          └─────────────────┼─────────────────┘
                            │
                            ▼
                        Text Risk
                            │
                            ▼
                      Policy Risk
                            │
                            ▼
                    Fraud Score 0–100
                            │
                            ▼
                       Explanation
                            │
                            ▼
                    Human Moderation
```

---

# 🏗️ System Architecture

```text
                         ┌──────────────────────┐
                         │      Web Browser     │
                         │    HTML / CSS / JS   │
                         └──────────┬───────────┘
                                    │
                                    ▼
                         ┌──────────────────────┐
                         │    PHP Application   │
                         │                      │
                         │ Marketplace          │
                         │ Authentication       │
                         │ Chat                 │
                         │ Trading              │
                         │ Reviews              │
                         │ Administration       │
                         └───────┬───────┬──────┘
                                 │       │
                    ┌────────────┘       └────────────┐
                    ▼                                 ▼
             ┌──────────────┐                ┌────────────────┐
             │    MySQL     │                │  FastAPI AI    │
             │   Database   │                │ Trust Service  │
             └──────────────┘                └───────┬────────┘
                                                     │
                              ┌──────────────────────┼──────────────────────┐
                              │                      │                      │
                              ▼                      ▼                      ▼
                         Image Risk              Price Risk             Text Risk
                              │                      │                      │
                              └──────────────────────┼──────────────────────┘
                                                     │
                                                     ▼
                                              Seller Risk
                                                     │
                                                     ▼
                                             Policy Risk
                                                     │
                                                     ▼
                                           Risk Score 0–100
                                                     │
                                                     ▼
                                           Human Moderation
```

---

# 🧰 Tech Stack

## Main Application

| Technology          | Purpose                   |
| ------------------- | ------------------------- |
| PHP 8+              | Backend application       |
| MySQL               | Relational database       |
| PDO                 | Database access           |
| HTML5               | Frontend structure        |
| CSS3                | UI styling                |
| Vanilla JavaScript  | Client-side functionality |
| AJAX                | Message polling           |
| PHP Sessions        | Authentication            |
| Prepared Statements | SQL injection protection  |

---

## AI Service

| Technology              | Purpose                 |
| ----------------------- | ----------------------- |
| Python 3                | AI backend              |
| FastAPI                 | REST API                |
| pandas                  | Data processing         |
| scikit-learn            | Machine learning        |
| Pillow                  | Image processing        |
| ImageHash               | Perceptual hashing      |
| Random Forest Regressor | Price anomaly analysis  |
| TF-IDF                  | Text feature extraction |
| MultinomialNB           | Text classification     |

---

# 🚫 No Dependencies On

The main RADIUS application does **not** depend on:

* React
* Vite
* Node.js
* Express
* Socket.io
* Supabase

The core architecture remains:

```text
PHP
 +
MySQL
 +
FastAPI
 +
Python ML
```

---

# 📁 Project Structure

```text
radius/
│
├── admin/
│   ├── dashboard.php
│   ├── fraud-queue.php
│   └── ...
│
├── api/
│   ├── chat.php
│   ├── trade.php
│   └── fraud.php
│
├── assets/
│   ├── css/
│   └── js/
│
├── config/
│   ├── config.php
│   └── database.php
│
├── includes/
│   ├── auth.php
│   ├── csrf.php
│   └── functions.php
│
├── uploads/
│   └── listings/
│
├── ai_service/
│   ├── training/
│   ├── requirements.txt
│   └── ...
│
├── index.php
├── listings.php
├── listing.php
├── create-listing.php
├── messages.php
├── chat.php
├── trade-requests.php
├── trust-radar.php
├── database.sql
├── seed.php
├── Dockerfile
├── render.yaml
├── run.sh
└── README.md
```

---

# ⚙️ Installation

## 1. Clone the Repository

```bash
git clone https://github.com/alfahim-fuyad/radius.git
cd radius
```

---

## 2. Create the Database

For local MySQL:

```bash
mysql -u root -p < database.sql
```

Alternatively, import:

```text
database.sql
```

through phpMyAdmin.

---

# 🔧 Environment Configuration

Configure the application using environment variables.

### Local Example

```env
APP_URL=http://localhost:3000

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=radius
DB_USER=root
DB_PASSWORD=

AI_SERVICE_URL=http://127.0.0.1:8001
```

---

# 🤖 Install AI Dependencies

```bash
python3 -m pip install -r ai_service/requirements.txt
```

Windows:

```bash
py -m pip install -r ai_service/requirements.txt
```

---

# 🌱 Seed Demo Data

Run:

```bash
php seed.php
```

This creates demo users and sample marketplace data.

---

# ▶️ Run the Application

Run:

```bash
bash run.sh
```

The application should be available at:

```text
http://localhost:3000
```

FastAPI health check:

```text
http://127.0.0.1:8001/health
```

---

# 👤 Demo Accounts

After running:

```bash
php seed.php
```

the following demo accounts are available:

| Role   | Email                | Password         |
| ------ | -------------------- | ---------------- |
| Admin  | `admin@radius.test`  | `RadiusDemo123!` |
| Seller | `seller@radius.test` | `RadiusDemo123!` |
| Buyer  | `buyer@radius.test`  | `RadiusDemo123!` |
| User   | `nadia@radius.test`  | `RadiusDemo123!` |

> ⚠️ These credentials are intended for local/demo use only.

---

# 🚀 Deploying to Render

RADIUS includes:

```text
Dockerfile
render.yaml
run.sh
```

These files allow the PHP marketplace and FastAPI trust service to be deployed together as one Render web service.

The application keeps its existing:

```text
PHP + MySQL
```

architecture.

For production database hosting, **Aiven MySQL** can be used as the external database.

---

# ☁️ Render Deployment

## Step 1 — Push to GitHub

```bash
git add .
git commit -m "Prepare RADIUS for deployment"
git push origin main
```

---

## Step 2 — Create Render Blueprint

In Render:

```text
New
  ↓
Blueprint
  ↓
Select GitHub Repository
  ↓
Confirm render.yaml
```

Render will use the configuration defined in:

```text
render.yaml
```

---

# 🔐 Render Environment Variables

Configure:

```env
APP_URL=https://your-app.onrender.com

DB_HOST=your-aiven-host
DB_PORT=your-aiven-port
DB_NAME=defaultdb
DB_USER=your-aiven-user
DB_PASSWORD=your-aiven-password

DB_SSL_MODE=REQUIRED

AI_SERVICE_URL=http://127.0.0.1:8001
```

### Important

Do **not** assume that the Aiven MySQL port is always `3306`.

Use the actual port shown by Aiven.

---

# 🔒 Aiven MySQL SSL

When using Aiven MySQL:

1. Open your Aiven service.
2. Download the CA certificate.
3. Copy the PEM content.
4. Store it as a secure Render environment variable.

Example:

```env
DB_SSL_MODE=REQUIRED
DB_SSL_CA_PEM=-----BEGIN CERTIFICATE-----
...
-----END CERTIFICATE-----
```

The startup configuration can write the certificate to a temporary file so PHP can verify the Aiven MySQL server certificate.

---

# 🗄️ Aiven Database Setup

The local `database.sql` may contain:

```sql
CREATE DATABASE IF NOT EXISTS radius;
USE radius;
```

For Aiven, the database is typically:

```text
defaultdb
```

Therefore, import the table/schema definitions into the existing Aiven database.

If required, remove:

```sql
CREATE DATABASE ...
USE radius;
```

before importing.

---

# 🔌 API

## FastAPI

### Health Check

```http
GET /health
```

---

### Image Hash

```http
POST /hash-image
```

Used to generate a perceptual hash for an uploaded listing image.

---

### Listing Analysis

```http
POST /analyze-listing
```

Used to analyze listing information and generate fraud-risk signals.

Example response:

```json
{
  "fraud_score": 37,
  "image_score": 20,
  "price_score": 41,
  "seller_score": 30,
  "text_score": 35,
  "policy_score": 25,
  "trust_status": "low_risk",
  "model_name": "radius_explainable_ensemble",
  "model_version": "1.0",
  "explanation": "Low overall risk."
}
```

---

# 🔌 PHP APIs

| Endpoint         | Purpose                       |
| ---------------- | ----------------------------- |
| `/api/chat.php`  | Start, send and poll messages |
| `/api/trade.php` | Manage trade requests         |
| `/api/fraud.php` | Admin fraud actions           |

---

# 🔐 Security

RADIUS implements multiple security measures.

## Authentication

* PHP session authentication
* `password_hash()`
* `password_verify()`
* Secure session cookies
* Authentication middleware/checks
* Role-based authorization

---

## Database Security

Database queries use PDO prepared statements.

Example:

```php
$stmt = $pdo->prepare(
    "SELECT * FROM users WHERE email = ?"
);

$stmt->execute([$email]);
```

This helps protect against SQL injection.

---

# 🛡️ CSRF Protection

State-changing actions use CSRF protection.

Conceptual workflow:

```text
Generate CSRF Token
        ↓
Store Token in Session
        ↓
Send Token With Form
        ↓
Validate Token
        ↓
Perform Action
```

---

# 🧹 Output Escaping

User-generated content is escaped using:

```php
htmlspecialchars()
```

Example:

```php
echo htmlspecialchars(
    $title,
    ENT_QUOTES,
    'UTF-8'
);
```

This reduces the risk of HTML injection and XSS.

---

# 🖼️ Secure Image Uploads

Uploaded listing images are validated using:

* File size validation
* MIME type detection
* Extension validation
* Image decoding validation
* `is_uploaded_file()`
* `getimagesize()`
* Randomized filenames

Supported formats:

```text
JPG
JPEG
PNG
WEBP
```

Example stored image:

```text
/uploads/listings/
cec433563846a3bf36186bd8bd0adad319ae.png
```

---

# 🖼️ Listing Image Storage

Listing images are stored using randomized filenames instead of user-provided filenames.

Example:

```text
/uploads/listings/
cec433563846a3bf36186bd8bd0adad319ae.png

/uploads/listings/
165156e02e1456784253d81dcc816f7ef89a.png

/uploads/listings/
73cd72daa8d8e58f758325956632ddeb074f.png

/uploads/listings/
518a9e095f02d7681f09a9ca0f7a48804c4b.png

/uploads/listings/
5d94355287466590d562cfd50445b1f1ab77.png
```

---

# 💾 Original Price Protection

One of the important design requirements of RADIUS is that **AI analysis must never modify the original listing price**.

The analysis process works like this:

```text
Seller enters price
        ↓
Price saved to database
        ↓
AI reads original price
        ↓
AI calculates price risk
        ↓
Risk score saved
```

Fraud analysis updates only risk-related fields such as:

```text
fraud_score
trust_status
fraud_checked
updated_at
```

It does **not** update:

```text
price
title
description
category
brand
item_condition
```

A database safety check also verifies that the original price remains unchanged after analysis.

---

# 🤖 AI Service Failure Handling

If the FastAPI service is temporarily unavailable, the PHP application handles the failure gracefully.

```text
PHP Application
      │
      ▼
AI Request
      │
      ├── Success
      │      ↓
      │   Save Analysis
      │
      └── Failure
             ↓
        Graceful Failure
             ↓
        Admin Can Retry
```

The AI service is therefore not intended to become a single point of failure for basic marketplace functionality.

---

# 🧪 Test Workflow

A complete user workflow can be tested as follows:

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
Risk Score Generated
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

---

# 🧪 Additional Test Scenarios

## Authentication

* Registration
* Login
* Logout
* Invalid credentials
* Unauthorized access

## Listings

* Create listing
* Edit listing
* Remove listing
* Invalid image
* Oversized image
* Invalid MIME type
* Missing image

## AI / Fraud

* Successful AI analysis
* AI service unavailable
* AI analysis retry
* Safe listing
* Low-risk listing
* Suspicious listing
* High-risk listing
* Image similarity detection
* Price anomaly detection

## Chat

* Start conversation
* Send message
* Poll messages
* Unauthorized chat access

## Trading

* Create trade request
* Accept trade
* Reject trade
* Cancel trade
* Complete trade
* Duplicate trade protection
* Invalid state transition

## Reviews

* Create review
* Duplicate review protection
* Unauthorized review attempt

## Administration

* Fraud queue
* Manual review
* Listing moderation
* Unauthorized admin access
* Suspicious listing handling

---

# 🧠 Fraud Analysis Pipeline

```text
Listing Created
      │
      ▼
Image Uploaded
      │
      ▼
Image Validation
      │
      ▼
Image Hash
      │
      ▼
Seller Information
      │
      ▼
Existing Image Hashes
      │
      ▼
Existing Listing Descriptions
      │
      ▼
FastAPI AI Service
      │
      ├───────────────┐
      ▼               ▼
Image Risk        Price Risk
      │               │
      └───────┬───────┘
              │
              ▼
         Seller Risk
              │
              ▼
          Text Risk
              │
              ▼
        Policy Risk
              │
              ▼
       Fraud Score 0–100
              │
              ▼
        Trust Status
              │
              ▼
         Explanation
              │
              ▼
        Database
              │
              ▼
      Admin Moderation
```

---

# 🗃️ Database Structure

Major database tables include:

```text
users
listings
listing_images
reports
conversations
messages
trade_requests
reviews
fraud_predictions
price_data
```

Conceptual relationship:

```text
                         Users
                           │
            ┌──────────────┼──────────────┐
            │              │              │
            ▼              ▼              ▼
        Listings      Conversations   Trade Requests
            │              │
      ┌─────┼─────┐        ▼
      │     │     │      Messages
      ▼     ▼     ▼
   Images Reports Fraud
                   Predictions

Users ─────────── Reviews
```

---

# 🛡️ Moderation Philosophy

RADIUS intentionally separates **AI risk detection** from **final moderation decisions**.

```text
AI Prediction
     ≠
Fraud Verdict
```

For example:

```text
Fraud Score = 82
       ↓
High Risk
       ↓
Fraud Queue
       ↓
Administrator Review
       ↓
Final Decision
```

This approach makes the system more explainable and reduces the risk of treating a machine-learning prediction as definitive proof of fraud.

---

# 📊 Trust Radar

The project includes:

```text
trust-radar.php
```

The Trust Radar provides a visual representation of major risk dimensions.

Example:

```text
              Image
                │
                │
        Seller ─┼─ Price
                │
                │
              Text
                │
              Policy
```

This allows administrators to understand which components contributed to the overall risk score.

---

# 🌐 Local Development Requirements

Recommended environment:

```text
PHP 8+
MySQL 8+
Python 3+
PDO MySQL
cURL
FileInfo
GD / Image Processing Support
```

Check PHP:

```bash
php -v
```

Check Python:

```bash
python --version
```

Check MySQL:

```bash
mysql --version
```

---

# 🩺 Troubleshooting

## Database Connection Error

Check:

```env
DB_HOST
DB_PORT
DB_NAME
DB_USER
DB_PASSWORD
```

For local XAMPP MySQL:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=radius
DB_USER=root
DB_PASSWORD=
```

---

## AI Service Not Responding

Open:

```text
http://127.0.0.1:8001/health
```

If the health endpoint does not respond, verify that the FastAPI service is running.

---

## Uploaded Image Not Showing

If a listing image is missing, check:

```text
uploads/listings/
```

Verify:

1. The image file exists.
2. The database contains the correct `image_path`.
3. The path starts correctly with `/uploads/listings/`.
4. The upload directory is accessible.
5. The filename has not been changed.
6. The `listing_images` row has the correct `listing_id`.
7. The image was saved successfully before AI hashing.
8. The web server is serving the `uploads` directory.

Example:

```text
Database:
image_path = /uploads/listings/example.png

File:
uploads/listings/example.png
```

Both must point to the same actual file.

---

# 🔒 Production Security Checklist

Before deploying to production:

* [ ] Enable HTTPS
* [ ] Use strong database passwords
* [ ] Use environment variables for secrets
* [ ] Enable MySQL SSL
* [ ] Do not commit `.env`
* [ ] Do not commit database passwords
* [ ] Do not commit private certificates
* [ ] Do not expose API secrets
* [ ] Change demo account passwords
* [ ] Disable production debug output
* [ ] Configure persistent storage for uploaded images
* [ ] Review admin permissions
* [ ] Review session configuration
* [ ] Verify CSRF protection
* [ ] Verify file-upload validation

---

# 📋 Environment Variables

## Local

```env
APP_URL=http://localhost:3000

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=radius
DB_USER=root
DB_PASSWORD=

AI_SERVICE_URL=http://127.0.0.1:8001

DB_SSL_MODE=DISABLED
```

## Production

```env
APP_URL=https://your-domain.com

DB_HOST=your-mysql-host
DB_PORT=your-mysql-port
DB_NAME=defaultdb
DB_USER=your-mysql-user
DB_PASSWORD=your-mysql-password

DB_SSL_MODE=REQUIRED
DB_SSL_CA_PEM=your-ca-certificate

AI_SERVICE_URL=http://127.0.0.1:8001
```

> ⚠️ Never commit real credentials to GitHub.

---

# 🎯 Project Objectives

The main objectives of RADIUS are:

1. Build a secure secondhand marketplace.
2. Enable hyperlocal product discovery.
3. Provide buyer–seller communication.
4. Support direct trading.
5. Implement reviews and reporting.
6. Detect potentially risky listings.
7. Provide explainable AI risk scores.
8. Support human moderation.
9. Protect user information and conversations.
10. Demonstrate PHP–MySQL–Python AI integration.

---

# 📈 Future Improvements

Possible future improvements include:

* Real-time WebSocket messaging
* Push notifications
* Map-based marketplace
* Advanced geolocation
* Deep-learning image embeddings
* Improved image similarity detection
* More advanced price prediction
* Larger training datasets
* Model evaluation dashboard
* Model monitoring
* Seller reputation scoring
* Product recommendation system
* Marketplace analytics
* Mobile application
* Payment integration
* Delivery integration
* Automated notification system

---

# 🤝 Contribution

This project was developed as an academic mini-project.

To contribute:

```bash
git clone https://github.com/alfahim-fuyad/radius.git
cd radius
```

Create a feature branch:

```bash
git checkout -b feature/your-feature
```

Make your changes and commit:

```bash
git add .
git commit -m "Add your feature"
```

Push:

```bash
git push origin feature/your-feature
```

---

# 🎓 Academic Information

| Information      | Details                                    |
| ---------------- | ------------------------------------------ |
| Project          | RADIUS — Hyperlocal Secondhand Marketplace |
| Course           | CSE479 Mini Project                        |
| Application Type | Web-based Marketplace                      |
| Backend          | PHP                                        |
| Database         | MySQL                                      |
| AI Service       | Python + FastAPI                           |
| Machine Learning | scikit-learn                               |
| Image Analysis   | ImageHash + Pillow                         |
| Frontend         | HTML + CSS + JavaScript                    |

---

# 📄 License

This project was developed as a **CSE479 academic mini-project**.

The project is intended primarily for educational and demonstration purposes.

---

# ⭐ RADIUS

<p align="center">

### Discover Locally. Trade Securely. Build Trust.

</p>

```text
              RADIUS
                │
     ┌──────────┼──────────┐
     │          │          │
     ▼          ▼          ▼
 Discover     Trade       Chat
 Locally     Securely    Privately
     │          │          │
     └──────────┼──────────┘
                │
                ▼
          AI Trust Layer
                │
                ▼
        Explainable Risk
                │
                ▼
        Human Moderation
                │
                ▼
             TRUST
```

---

## 🚀 Project Summary

**RADIUS** brings together:

```text
PHP
+
MySQL
+
FastAPI
+
Machine Learning
+
Image Similarity
+
Hyperlocal Discovery
+
Chat
+
Trading
+
Reviews
+
Human Moderation
```

to create a secure and explainable secondhand marketplace.

> **RADIUS — Discover Locally. Trade Securely. Build Trust.**

```
```
