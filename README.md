# RADIUS — Hyperlocal Secondhand Marketplace

> **RADIUS — Discover Locally. Trade Securely. Build Trust.**

A secure **PHP/MySQL hyperlocal secondhand marketplace** for buying, selling, chatting, trading, reviewing, and discovering products nearby — powered by a dedicated **Python/FastAPI explainable AI trust and fraud-risk analysis service**.

---

## 📌 Overview

**RADIUS** is a CSE479 academic mini-project that implements a complete hyperlocal secondhand marketplace using:

- PHP
- MySQL
- PDO
- HTML/CSS/JavaScript
- Python
- FastAPI
- Machine Learning
- Image similarity analysis
- Explainable fraud-risk scoring

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

RADIUS also includes a dedicated AI service that analyzes listings and produces an **explainable 0–100 fraud-risk score**.

> ⚠️ **Important:** The trust system produces risk signals, not proof of fraud. Suspicious or high-risk listings are intended for human administrator review.

---

# ✨ Features

## 🛍️ Marketplace

- Browse secondhand listings
- Search listings
- Category-based browsing
- Advanced filtering
- Nearby-distance discovery
- Listing details
- Create listings
- Edit and manage listings
- Seller profiles
- Buyer profiles
- Product condition information
- Original seller-entered pricing
- Secure image uploads
- Listing reporting
- Reviews and ratings

---

## 📍 Hyperlocal Discovery

RADIUS is designed around nearby secondhand trading.

Listings can contain:

- Latitude
- Longitude
- Location information
- Distance from the current user

Distance calculations use the **Haversine formula** to support location-based discovery.

---

# 💬 Communication

RADIUS includes a private buyer–seller messaging system.

### Features

- Start conversations
- Send messages
- Poll for new messages
- Conversation history
- Participant-based authorization
- Unauthorized conversation protection
- AJAX-based lightweight communication

The system does not expose private conversations to unauthorized users.

---

# 🤝 Trading System

Users can negotiate and complete trades through the built-in trade system.

### Trade Features

- Create trade requests
- Accept trade requests
- Reject trade requests
- Cancel requests
- Complete trades
- Trade-state validation
- Duplicate trade protection
- Seller/buyer authorization

Example workflow:

```text
Buyer
  ↓
View Listing
  ↓
Send Trade Request
  ↓
Seller Receives Request
  ↓
Accept / Reject
  ↓
Trade
  ↓
Complete Trade
  ↓
Review
