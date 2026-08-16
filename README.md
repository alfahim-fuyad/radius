# RADIUS — Hyperlocal Secondhand Marketplace

<p align="center">

**Discover Locally. Trade Securely. Build Trust.**

A secure hyperlocal secondhand marketplace built with **PHP, MySQL, Python/FastAPI, and AI-based fraud-risk analysis.**

</p>

---

## 📌 Overview

**RADIUS** is a CSE479 academic mini-project designed for secure and convenient local secondhand trading.

Users can discover nearby products, create listings, communicate with buyers and sellers, send trade requests, leave reviews, report suspicious listings, and view AI-assisted trust information.

The system combines a traditional **PHP + MySQL marketplace** with a separate **Python/FastAPI AI service** that generates an explainable **0–100 fraud-risk score**.

> ⚠️ The AI system provides risk signals, not proof of fraud. High-risk listings are intended for further human/admin review.

---

## ✨ Key Features

### 🛍️ Marketplace
- Browse and search secondhand products
- Category and advanced filtering
- Create, edit, and delete listings
- Product image uploads
- Seller profiles
- Product condition and pricing
- Listing status management

### 📍 Hyperlocal Discovery
- Latitude and longitude based listings
- Nearby product discovery
- Distance calculation using the **Haversine Formula**
- Location-based filtering

### 💬 Communication & Trading
- Buyer–seller private chat
- Trade requests
- Trade status management
- Reviews and ratings

### 🛡️ Trust & Safety
- Suspicious listing reports
- Seller reputation
- AI-based fraud-risk analysis
- Explainable risk factors
- Admin moderation
- Trust Radar

---

## 🤖 AI-Based Fraud Risk Analysis

RADIUS uses a dedicated **Python/FastAPI service** to analyze multiple risk signals:

- 💰 Price anomaly
- 🖼️ Image similarity
- 📝 Text patterns
- 👤 Seller behavior
- 🏷️ Brand/policy risks

The system combines these signals into an explainable **0–100 risk score**.

```text
Listing
   │
   ├── Price Analysis
   ├── Image Analysis
   ├── Text Analysis
   ├── Seller Signals
   └── Policy / Brand Signals
             │
             ▼
       Risk Score (0–100)
             │
             ▼
        Trust Radar
```

---

## 📍 Hyperlocal Flow

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

## 🏗️ System Architecture

```text
                    ┌──────────────┐
                    │     User     │
                    │ Web Browser  │
                    └──────┬───────┘
                           │
                           ▼
                    ┌──────────────┐
                    │    Render    │
                    │ RADIUS App   │
                    └──────┬───────┘
                           │
             ┌─────────────┼─────────────┐
             │             │             │
             ▼             ▼             ▼
       ┌──────────┐  ┌───────────┐  ┌──────────┐
       │  Aiven   │  │  FastAPI  │  │ SerpAPI  │
       │  MySQL   │  │ AI Service│  │ External │
       │ Database │  │           │  │   API    │
       └──────────┘  └─────┬─────┘  └──────────┘
                           │
                           ▼
                    Risk Analysis
```

---

## 🔄 Development & Deployment Flow

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
   ├── RADIUS Web Application
   │
   └── FastAPI AI Service
          │
          ├── Aiven MySQL
          │
          └── SerpAPI
```

### Development

The project is developed using **Visual Studio Code** with PHP, JavaScript, MySQL, Python, and FastAPI.

### Version Control

Source code is managed using **Git and GitHub**.

```bash
git add .
git commit -m "Update RADIUS"
git push origin main
```

### Deployment

The application is deployed through **Render**, connected with the GitHub repository.

### Database

Production data is stored in **Aiven MySQL**.

### External API

**SerpAPI** is used for external search/product-market information where required.

---

## 🧰 Technology Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP |
| Database | MySQL / Aiven MySQL |
| Database Access | PDO |
| AI Service | Python / FastAPI |
| External API | SerpAPI |
| Location | Latitude / Longitude |
| Distance | Haversine Formula |
| Development | VS Code |
| Version Control | Git / GitHub |
| Deployment | Render |
| Local Environment | XAMPP |

---

## 🗄️ Database

Main database entities include:

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

## 🔐 Security

RADIUS includes several security mechanisms:

- Password hashing
- Session-based authentication
- Role-based authorization
- PDO prepared statements
- Input validation
- Secure file uploads
- API key protection
- Environment variables
- Admin moderation

> Sensitive credentials such as database passwords and API keys are stored as environment variables and are not committed to GitHub.

---

## 🚀 Project Flow

```text
User
 │
 ▼
Browse / Search
 │
 ▼
View Listing
 │
 ├── Chat with Seller
 ├── Send Trade Request
 ├── Check Reviews
 └── View Trust Information
          │
          ▼
     AI Risk Analysis
          │
          ▼
      Risk Score
          │
          ▼
     User Decision
```

---

## 🎯 Project Objective

The main objective of RADIUS is to create a **secure, explainable, and location-aware secondhand marketplace** that combines traditional web technologies with AI-assisted trust and fraud-risk analysis.

---

## ⚠️ Disclaimer

RADIUS is an **academic project developed for educational purposes**.

The AI-generated risk score is an assistive signal and should not be considered definitive proof of fraud. Suspicious listings should be reviewed by an administrator or appropriate human authority.

---

## 👥 Contributors

- **Project Team — CSE479**
- **East West University**

---

<p align="center">

### ⭐ RADIUS
**Discover Locally. Trade Securely. Build Trust.**

</p>
