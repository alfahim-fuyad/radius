````markdown
# RADIUS — Hyperlocal Secondhand Marketplace

<p align="center">

**RADIUS — Discover Locally. Trade Securely. Build Trust.**

A secure hyperlocal secondhand marketplace built with **PHP, MySQL, and an explainable AI-based fraud-risk analysis service**.

</p>

---

## 📌 Overview

**RADIUS** is a CSE479 academic mini-project that implements a secure, hyperlocal secondhand marketplace for **buying, selling, chatting, trading, reviewing, and discovering products nearby**.

The project combines a traditional **PHP + MySQL marketplace** with a dedicated **Python/FastAPI AI service** that analyzes listings and generates an explainable **0–100 fraud-risk score**.

Users can:

- Browse nearby secondhand products
- Search and filter listings
- Create and manage listings
- Upload product images
- Chat privately with buyers and sellers
- Send and manage trade requests
- Leave reviews and ratings
- Report suspicious listings
- View seller information
- View trust and risk information
- Discover products based on location

The AI trust system analyzes multiple signals, including:

- Image similarity
- Price anomaly
- Seller behavior
- Text patterns
- Brand and policy risks
- Listing characteristics

> ⚠️ **Important:** The AI trust system produces risk signals, not proof of fraud. Suspicious and high-risk listings are intended for human administrator review.

---

# ✨ Features

## 🛍️ Marketplace

RADIUS provides the core functionality required for a secondhand marketplace.

- Browse secondhand products
- Search listings
- Category-based marketplace
- Advanced filtering
- Nearby-distance discovery
- Listing details
- Create listings
- Edit listings
- Delete listings
- Seller profiles
- Buyer profiles
- Product condition information
- Seller-entered pricing
- Product image uploads
- Product reporting
- Reviews and ratings
- Listing status management

---

## 📍 Hyperlocal Discovery

RADIUS is designed around **nearby secondhand trading**.

Listings can contain:

- Latitude
- Longitude
- Location name
- Distance from the current user

Distance calculations use the **Haversine formula**.

### Location Flow

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

This allows users to discover secondhand products available near their location.

---

# 🤖 AI-Based Trust & Fraud Risk Analysis

One of the main features of RADIUS is its dedicated AI-based trust analysis service.

Instead of simply marking a listing as:

```text
Safe / Fraud
```

the system generates an explainable **risk score from 0 to 100**.

### Risk Score

|  Score | Risk Level      | Meaning                       |
| -----: | --------------- | ----------------------------- |
|   0–20 | 🟢 Low          | Low-risk listing              |
|  21–40 | 🟢 Moderate-Low | Some minor risk signals       |
|  41–60 | 🟡 Medium       | Requires additional attention |
|  61–80 | 🟠 High         | Multiple suspicious signals   |
| 81–100 | 🔴 Critical     | Strong fraud-risk indicators  |

> The thresholds can be adjusted according to the implementation of the AI service.

---

# 🔍 AI Risk Signals

The AI service evaluates multiple independent signals.

## 1. 💰 Price Anomaly

The system compares the seller's entered price against available reference or market price information.

Example:

```text
Expected Market Price: ৳50,000
Seller Price:          ৳12,000

             ↓

Large Price Difference

             ↓

Higher Price-Anomaly Risk
```

A very unusual price does **not automatically mean fraud**.

It is treated as one signal among multiple signals.

---

## 2. 🖼️ Image Similarity

Product images can be analyzed against reference images or previously analyzed content.

Potential signals include:

* Duplicate images
* Highly similar images
* Reused product photographs
* Suspicious image patterns

### Image Analysis Flow

```text
Uploaded Product Image
          │
          ▼
Image Processing
          │
          ▼
Similarity Analysis
          │
          ▼
Similarity Score
          │
          ▼
Risk Contribution
```

---

## 3. 👤 Seller Behavior

The system can consider seller-related signals such as:

* Account activity
* Listing history
* Previous reports
* Review history
* Seller reputation
* Transaction behavior

This helps distinguish between a new or unknown seller and a seller with an established history.

---

## 4. 📝 Text Analysis

Listing descriptions and other textual information can be analyzed for suspicious patterns.

Potential indicators include:

* Unrealistic claims
* Excessive urgency
* Suspicious wording
* Repeated promotional patterns
* Potentially misleading statements

---

## 5. 🏷️ Brand / Policy Risk

Certain listing characteristics may contribute additional risk.

The system can identify potentially suspicious:

* Brand claims
* Product descriptions
* Policy violations
* Restricted or suspicious content

---

# 🧮 Explainable Risk Score

The final risk score is calculated from multiple signals instead of relying on a single prediction.

```text
                    Listing
                       │
        ┌──────────────┼──────────────┐
        ▼              ▼              ▼
      Price          Image           Text
     Analysis       Analysis        Analysis
        │              │              │
        └──────────────┼──────────────┘
                       ▼
               Seller Behavior
                       │
                       ▼
                Policy / Brand
                    Signals
                       │
                       ▼
              Risk Score Engine
                       │
                       ▼
                 0 – 100 Score
                       │
                       ▼
               Explanation Layer
                       │
             ┌─────────┴─────────┐
             ▼                   ▼
         Risk Level        Risk Factors
```

Example:

```text
Fraud Risk Score: 78/100

Risk Factors:
• Price significantly below market reference
• Similar image detected
• Limited seller history
• Suspicious listing wording
```

---

# 🧠 Explainable AI

A major design goal of RADIUS is **explainability**.

Instead of displaying only:

```text
Fraud Probability: 87%
```

the system provides understandable reasons behind the risk score.

Example:

```text
Risk Score: 87/100

Reasons:
✓ Large price anomaly
✓ High image similarity
✓ Suspicious text pattern
✓ Limited seller history
```

This makes the AI output easier for:

* Buyers
* Sellers
* Administrators
* Project evaluators

to understand.

---

# 🛡️ Trust System

RADIUS combines AI-generated risk information with marketplace trust mechanisms.

Trust-related features include:

* Seller reviews
* Buyer reviews
* Ratings
* Listing reports
* Seller history
* Fraud-risk score
* Risk explanations
* Admin moderation

The objective is to help users make **better-informed decisions** rather than blindly trusting an AI prediction.

---

# 💬 Private Messaging

RADIUS includes a private communication system between marketplace users.

Users can:

* Start conversations
* Send messages
* View conversations
* Communicate about listings
* Discuss prices
* Arrange trades
* Negotiate with sellers

### Messaging Flow

```text
Buyer
  │
  │ Message
  ▼
Seller
  │
  │ Reply
  ▼
Conversation
  │
  ▼
Negotiation / Trade
```

---

# 🔄 Trade Request System

RADIUS supports product trading in addition to conventional buying.

Users can send trade requests to other users.

A trade request can contain:

* Sender
* Receiver
* Offered listing
* Requested listing
* Trade message
* Request status

### Trade Status

```text
Pending
   │
   ├── Accepted
   │
   ├── Rejected
   │
   └── Cancelled
```

---

# ⭐ Reviews & Ratings

Users can provide feedback after marketplace interactions.

The review system helps establish seller and buyer reputation.

Reviews may include:

* Rating
* Written feedback
* Reviewer
* Reviewed user
* Associated transaction or listing

This contributes to the overall marketplace trust ecosystem.

---

# 🚨 Reporting System

Users can report suspicious or inappropriate listings.

Possible report categories include:

* Suspected fraud
* Fake product
* Misleading information
* Inappropriate content
* Policy violation
* Other

### Reporting Flow

```text
User
 │
 ▼
Suspicious Listing
 │
 ▼
Submit Report
 │
 ▼
Admin Review
 │
 ├── No Action
 │
 ├── Warning
 │
 ├── Listing Removal
 │
 └── Further Investigation
```

---

# 👨‍💼 Admin Dashboard

RADIUS includes administrative functionality for marketplace moderation.

Administrators can manage:

* Users
* Listings
* Reports
* Reviews
* Suspicious listings
* Fraud-risk information
* Marketplace content

The admin system provides human oversight over AI-generated risk signals.

---

# 🔐 Authentication & Authorization

RADIUS uses account-based authentication.

Users can:

* Register
* Login
* Logout
* Maintain sessions
* Manage their account
* Access authorized features

### User Roles

```text
Regular User
     │
     ├── Browse
     ├── Buy
     ├── Sell
     ├── Chat
     ├── Trade
     └── Review

Administrator
     │
     ├── Manage Users
     ├── Manage Listings
     ├── Review Reports
     └── Moderate Platform
```

---

# 🗄️ Database Architecture

RADIUS uses **MySQL** as its primary relational database.

The database stores marketplace data such as:

```text
Users
 │
 ├── Listings
 │     └── Listing Images
 │
 ├── Messages
 │     └── Conversations
 │
 ├── Trade Requests
 │
 ├── Reviews
 │
 └── Reports

Listings
 │
 └── Fraud Predictions
```

---

# 📊 Main Database Tables

## `users`

Stores user account information.

Typical information includes:

* User ID
* Name
* Email
* Password
* Role
* Location
* Account information

---

## `listings`

Stores marketplace products.

Typical fields include:

* Listing ID
* Seller ID
* Title
* Description
* Category
* Price
* Condition
* Latitude
* Longitude
* Location
* Status
* Created date

---

## `listing_images`

Stores product image information.

```text
Listing
   │
   ├── Image 1
   ├── Image 2
   ├── Image 3
   └── ...
```

---

## `conversations`

Stores private conversation information between users.

---

## `messages`

Stores individual messages exchanged inside conversations.

---

## `trade_requests`

Stores trade offers and their statuses.

---

## `reviews`

Stores user ratings and reviews.

---

## `reports`

Stores user-submitted reports about suspicious listings or users.

---

## `fraud_predictions`

Stores AI-generated fraud-risk analysis.

Potential information includes:

* Listing ID
* Risk score
* Risk level
* Risk factors
* Prediction information
* Analysis timestamp

---

## `price_data`

Stores reference price information used for price-anomaly analysis.

---

# 🏗️ System Architecture

RADIUS follows a multi-component architecture.

```text
                    ┌─────────────────────┐
                    │       User          │
                    │   Web Browser       │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │     PHP Web App     │
                    │                     │
                    │ Marketplace         │
                    │ Authentication      │
                    │ Messaging           │
                    │ Trading             │
                    │ Reviews             │
                    │ Reports             │
                    └───────┬─────┬───────┘
                            │     │
                 ┌──────────┘     └──────────┐
                 ▼                           ▼
       ┌──────────────────┐       ┌──────────────────┐
       │      MySQL       │       │ Python / FastAPI │
       │    Database      │       │    AI Service    │
       └──────────────────┘       └────────┬─────────┘
                                           │
                                           ▼
                                  ┌──────────────────┐
                                  │ Risk Analysis    │
                                  │                  │
                                  │ Price            │
                                  │ Image            │
                                  │ Text             │
                                  │ Seller           │
                                  │ Policy / Brand   │
                                  └──────────────────┘
```

---

# 🔗 PHP ↔ FastAPI Integration

The PHP application communicates with the Python/FastAPI service through HTTP API requests.

### Basic Flow

```text
PHP Application
      │
      │ HTTP Request
      ▼
FastAPI AI Service
      │
      ▼
Risk Analysis
      │
      ▼
JSON Response
      │
      ▼
PHP Application
      │
      ▼
Display Risk Information
```

Example conceptual response:

```json
{
  "risk_score": 78,
  "risk_level": "high",
  "factors": [
    "price_anomaly",
    "image_similarity",
    "seller_history"
  ]
}
```

---

# 🐍 FastAPI AI Service

The AI component is implemented separately using **Python and FastAPI**.

The separation provides several advantages:

* Independent AI service
* Easier model development
* Easier testing
* API-based communication
* Separation of marketplace and AI logic
* Future model replacement capability

The FastAPI service is responsible for processing listing information and returning structured risk-analysis results.

---

# 🧮 Haversine Distance Formula

RADIUS uses the **Haversine formula** to calculate the approximate distance between two geographic coordinates.

The formula is useful for determining how far a listing is from the user's current location.

Conceptually:

```text
User Coordinates
      │
      ▼
Listing Coordinates
      │
      ▼
Haversine Formula
      │
      ▼
Distance in Kilometers
```

This enables the platform to prioritize nearby products.

---

# 📁 Project Structure

A simplified project structure is:

```text
RADIUS/
│
├── admin/
│   ├── dashboard.php
│   ├── users.php
│   ├── listings.php
│   └── reports.php
│
├── api/
│   └── ...
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── uploads/
│   └── listings/
│
├── config/
│   └── database.php
│
├── includes/
│   ├── auth.php
│   ├── header.php
│   ├── footer.php
│   └── ...
│
├── ai-service/
│   ├── main.py
│   ├── requirements.txt
│   └── ...
│
├── index.php
├── listings.php
├── create-listing.php
├── listing-details.php
├── messages.php
├── trades.php
├── reviews.php
├── login.php
├── register.php
├── logout.php
│
├── database.sql
├── seed.php
└── README.md
```

> The exact structure may vary depending on the current project implementation.

---

# ⚙️ Technology Stack

## Frontend

* HTML5
* CSS3
* JavaScript
* Responsive Web Design

## Backend

* PHP
* PHP Sessions
* REST-style API communication

## Database

* MySQL
* SQL
* Relational database design

## AI Service

* Python
* FastAPI
* Machine Learning / AI-based risk analysis
* Image analysis
* Text analysis
* Price anomaly analysis

## Development Environment

* XAMPP
* Apache
* MySQL
* phpMyAdmin
* Python
* Git
* GitHub

---

# 🚀 Installation & Setup

## 1. Clone the Repository

```bash
git clone https://github.com/YOUR-USERNAME/YOUR-REPOSITORY.git
cd YOUR-REPOSITORY
```

---

## 2. Start XAMPP

Start the following services from XAMPP:

```text
Apache
MySQL
```

---

## 3. Move the Project

Copy the project into the XAMPP web directory:

```text
C:\xampp\htdocs\
```

Example:

```text
C:\xampp\htdocs\RADIUS
```

---

# 🗄️ Database Setup

## 1. Open phpMyAdmin

Open:

```text
http://localhost/phpmyadmin
```

## 2. Create the Database

Create a MySQL database named:

```text
radius
```

## 3. Import the SQL File

Import:

```text
database.sql
```

The database should create the required tables automatically.

---

# 🔧 Database Configuration

Update the database configuration according to your local environment.

Example:

```php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "radius";

$conn = new mysqli(
    $host,
    $username,
    $password,
    $dbname
);
```

> Update the credentials if your MySQL configuration is different.

---

# 🐍 AI Service Setup

Navigate to the AI service directory:

```bash
cd ai-service
```

Create a virtual environment:

```bash
python -m venv venv
```

Activate it on Windows:

```bash
venv\Scripts\activate
```

Install dependencies:

```bash
pip install -r requirements.txt
```

Start the FastAPI service:

```bash
uvicorn main:app --host 0.0.0.0 --port 8000
```

The AI service should then be available at:

```text
http://localhost:8000
```

FastAPI documentation:

```text
http://localhost:8000/docs
```

---

# ▶️ Running the PHP Application

Start Apache and MySQL from XAMPP.

Then open:

```text
http://localhost/RADIUS/
```

Depending on your folder name, the URL may be different.

---

# 🔄 Complete Application Flow

The overall RADIUS workflow can be represented as:

```text
                    User
                     │
                     ▼
               Authentication
                     │
                     ▼
              Browse Listings
                     │
            ┌────────┴────────┐
            ▼                 ▼
       Search/Filter      Nearby Search
            │                 │
            └────────┬────────┘
                     ▼
               Listing Details
                     │
          ┌──────────┼──────────┐
          ▼          ▼          ▼
        Chat       Trade      Report
          │          │          │
          └──────────┼──────────┘
                     ▼
                Trust System
                     │
          ┌──────────┴──────────┐
          ▼                     ▼
      Reviews             AI Risk Analysis
                                │
                    ┌───────────┼───────────┐
                    ▼           ▼           ▼
                  Price       Image        Text
                    │           │           │
                    └───────────┼───────────┘
                                ▼
                          Risk Score
                                │
                                ▼
                         User / Admin
```

---

# 🔐 Security Considerations

Security is an important component of RADIUS.

The application considers:

* Password hashing
* Session-based authentication
* Authorization
* SQL injection prevention
* Input validation
* File upload validation
* Access control
* User reporting
* Admin moderation

For production deployment, additional security hardening should be applied.

---

# 🌐 Deployment

RADIUS can be deployed using a PHP-compatible hosting environment together with a MySQL-compatible database.

The AI service can be deployed separately as a Python/FastAPI service.

### Production Architecture

```text
                   Internet
                      │
          ┌───────────┴───────────┐
          ▼                       ▼
   PHP Web Application       FastAPI Service
          │                       │
          ▼                       ▼
       MySQL DB             AI Risk Engine
          │                       │
          └───────────┬───────────┘
                      │
                      ▼
                  RADIUS
```

---

# 🧪 Testing

Testing should cover the major system components.

### Authentication

* Registration
* Login
* Logout
* Invalid credentials
* Session handling
* Authorization

### Marketplace

* Listing creation
* Listing editing
* Listing deletion
* Search
* Filtering
* Image uploads

### Messaging

* Creating conversations
* Sending messages
* Receiving messages

### Trading

* Sending trade requests
* Accepting requests
* Rejecting requests
* Cancelling requests

### Reviews

* Creating reviews
* Rating validation
* Review display

### AI Service

* Price anomaly detection
* Image similarity analysis
* Text analysis
* Seller risk signals
* Risk score generation
* Risk explanation

---

# 📈 Future Improvements

Possible future improvements include:

* Real-time notifications
* Real-time messaging using WebSockets
* More advanced recommendation systems
* Improved image fraud detection
* Deep-learning-based image analysis
* Better price prediction
* More sophisticated seller reputation scoring
* Location-based recommendation ranking
* Mobile application
* Online payment integration
* Map-based marketplace browsing
* Automated moderation
* Improved fraud-detection models
* Model performance monitoring
* More extensive fraud datasets

---

# 🎯 Project Objectives

The primary objectives of RADIUS are:

1. Build a functional hyperlocal secondhand marketplace.
2. Enable users to discover products nearby.
3. Provide secure communication between buyers and sellers.
4. Support both buying and trading.
5. Build a reputation and review system.
6. Detect suspicious marketplace behavior.
7. Provide explainable AI-based fraud-risk analysis.
8. Combine automated risk detection with human moderation.
9. Demonstrate integration between PHP, MySQL, and Python/FastAPI.
10. Provide a practical academic implementation of AI-assisted marketplace security.

---

# 📚 Academic Context

**Course:** CSE479
**Project Type:** Academic Mini Project
**Project Name:** RADIUS — Hyperlocal Secondhand Marketplace

### Core Technologies

```text
PHP
MySQL
Python
FastAPI
JavaScript
HTML
CSS
Machine Learning / AI
```

---

# ⚠️ Disclaimer

RADIUS is an **academic project** developed for educational and demonstration purposes.

The AI-based fraud-risk score is an assistive signal and should **not be treated as definitive proof of fraud**.

A high-risk score indicates that a listing contains multiple suspicious signals and may require further investigation.

Final decisions should involve appropriate human review.

---

# 👥 Contributors

Add your project members here:

```text
1. Your Name
2. Member Name
3. Member Name
4. Member Name
```

---

# 📄 License

This project was developed for academic and educational purposes.

If you plan to publish or reuse this project, update this section with the appropriate license.

---

# ⭐ RADIUS

<p align="center">

**Discover Locally. Trade Securely. Build Trust.**

</p>
```
