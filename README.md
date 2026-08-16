# RADIUS — Hyperlocal Secondhand Marketplace

<p align="center">

**RADIUS — Discover Locally. Trade Securely. Build Trust.**

A secure hyperlocal secondhand marketplace built with **PHP, MySQL, and an explainable AI-based fraud-risk analysis service**.

</p>

---

## 📌 Overview

**RADIUS** is a CSE479 academic mini-project that implements a secure, hyperlocal secondhand marketplace for **buying, selling, chatting, trading, reviewing, and discovering products nearby**.

The project combines a traditional **PHP + MySQL marketplace** with a dedicated **Python/FastAPI AI service** that analyzes listings and generates an explainable **0–100 fraud-risk score**.

RADIUS is designed to make local secondhand trading safer by combining:

- Hyperlocal product discovery
- User authentication
- Marketplace listings
- Private messaging
- Trade requests
- Reviews and ratings
- User reporting
- AI-based fraud-risk analysis
- Location-based discovery
- External search/market information through SerpAPI
- Human administrator moderation

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
- Location information
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
```

This allows users to discover secondhand products available near their current location.

---

# 🤖 AI-Based Trust & Fraud Risk Analysis

One of the main features of RADIUS is its dedicated **AI-based trust analysis service**.

Instead of simply marking a listing as:

```text
Safe / Fraud
```

the system generates an explainable **risk score from 0 to 100**.

### Risk Score

| Score | Risk Level | Meaning |
|---:|---|---|
| 0–20 | 🟢 Low | Low-risk listing |
| 21–40 | 🟢 Moderate-Low | Some minor risk signals |
| 41–60 | 🟡 Medium | Requires additional attention |
| 61–80 | 🟠 High | Multiple suspicious signals |
| 81–100 | 🔴 Critical | Strong fraud-risk indicators |

> The exact thresholds can be adjusted according to the implementation of the AI service.

---

# 🔍 AI Risk Signals

The AI service evaluates multiple independent signals.

## 1. 💰 Price Anomaly

The system compares the seller's entered price against available reference or market price information.

### Example

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

- Duplicate images
- Highly similar images
- Reused product photographs
- Suspicious image patterns

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

- Account activity
- Listing history
- Previous reports
- Review history
- Seller reputation
- Transaction behavior

This helps distinguish between a new or unknown seller and a seller with an established history.

---

## 4. 📝 Text Analysis

Listing descriptions and other textual information can be analyzed for suspicious patterns.

Potential indicators include:

- Unrealistic claims
- Excessive urgency
- Suspicious wording
- Repeated promotional patterns
- Potentially misleading statements

---

## 5. 🏷️ Brand / Policy Risk

Certain listing characteristics may contribute additional risk.

The system can identify potentially suspicious:

- Brand claims
- Product descriptions
- Policy violations
- Restricted or suspicious content

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

### Example

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

### Example

```text
Risk Score: 87/100

Reasons:
✓ Large price anomaly
✓ High image similarity
✓ Suspicious text pattern
✓ Limited seller history
```

This makes the AI output easier for:

- Buyers
- Sellers
- Administrators
- Project evaluators

to understand.

---

# 🛡️ Trust System

RADIUS combines AI-generated risk information with marketplace trust mechanisms.

Trust-related features include:

- Seller reviews
- Buyer reviews
- Ratings
- Listing reports
- Seller history
- Fraud-risk score
- Risk explanations
- Admin moderation

The objective is to help users make **better-informed decisions** rather than blindly trusting an AI prediction.

---

# 💬 Private Messaging

RADIUS includes a private communication system between marketplace users.

Users can:

- Start conversations
- Send messages
- View conversations
- Communicate about listings
- Discuss prices
- Arrange trades
- Negotiate with sellers

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

- Sender
- Receiver
- Offered listing
- Requested listing
- Trade message
- Request status

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

- Rating
- Written feedback
- Reviewer
- Reviewed user
- Associated transaction or listing

This contributes to the overall marketplace trust ecosystem.

---

# 🚨 Reporting System

Users can report suspicious or inappropriate listings.

Possible report categories include:

- Suspected fraud
- Fake product
- Misleading information
- Inappropriate content
- Policy violation
- Other

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

- Users
- Listings
- Reports
- Reviews
- Suspicious listings
- Fraud-risk information
- Marketplace content

The admin system provides human oversight over AI-generated risk signals.

---

# 🔐 Authentication & Authorization

RADIUS uses account-based authentication.

Users can:

- Register
- Login
- Logout
- Maintain sessions
- Manage their account
- Access authorized features

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

RADIUS uses **Aiven MySQL** as its cloud-hosted relational database.

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

- User ID
- Name
- Email
- Password
- Role
- Location
- Account information

---

## `listings`

Stores marketplace products.

Typical fields include:

- Listing ID
- Seller ID
- Title
- Description
- Category
- Price
- Condition
- Latitude
- Longitude
- Location
- Status
- Created date

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

- Listing ID
- Risk score
- Risk level
- Risk factors
- Prediction information
- Analysis timestamp

---

## `price_data`

Stores reference price information used for price-anomaly analysis.

---

# 🏗️ System Architecture

RADIUS follows a multi-component architecture.

```text
                    ┌─────────────────────┐
                    │       User          │
                    │    Web Browser      │
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
       │   Aiven MySQL    │       │ Python / FastAPI │
       │    Database      │       │    AI Service    │
       └──────────────────┘       └────────┬─────────┘
                                           │
                                           ▼
                                  ┌──────────────────┐
                                  │   Risk Analysis  │
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

### Example API Response

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

# 🔎 SerpAPI Integration

RADIUS can use **SerpAPI** as an external search API for obtaining product and market-related information.

The returned information can be used as an additional reference signal for features such as:

- Product information
- Market price reference
- Search-based product comparison
- Price anomaly analysis

### SerpAPI Flow

```text
RADIUS Application
        │
        │ API Request
        ▼
     SerpAPI
        │
        ▼
 Search Results
        │
        ▼
RADIUS Application
        │
        ▼
Market / Product Information
        │
        ▼
Price / Trust Analysis
```

The SerpAPI key should be stored securely as an environment variable.

```text
SERPAPI_KEY=your_api_key
```

> ⚠️ Never commit your actual API key to GitHub.

---

# 🧑‍💻 Development & Deployment Flow

RADIUS follows a complete development, version-control, deployment, database, AI, and external API integration workflow.

```text
                         👨‍💻 Developer
                              │
                              ▼
                     ┌─────────────────┐
                     │     VS Code     │
                     │                 │
                     │ PHP Application │
                     │ FastAPI Service │
                     │ HTML/CSS/JS     │
                     └────────┬────────┘
                              │
                              │ Git
                              ▼
                     ┌─────────────────┐
                     │     GitHub      │
                     │                 │
                     │ Source Code     │
                     │ Version Control │
                     └────────┬────────┘
                              │
                              │ Deploy
                              ▼
                     ┌─────────────────┐
                     │     Render      │
                     │                 │
                     │ Web Application │
                     │ FastAPI Service │
                     └──────┬─────┬────┘
                            │     │
              ┌─────────────┘     └──────────────┐
              ▼                                  ▼
     ┌─────────────────┐                ┌─────────────────┐
     │   Aiven MySQL   │                │    SerpAPI      │
     │                 │                │                 │
     │ Users           │                │ Search API      │
     │ Listings        │                │ Product/Market  │
     │ Messages        │                │ Information     │
     │ Trades          │                │                 │
     │ Reviews         │                └─────────────────┘
     │ Reports         │
     │ Fraud Data      │
     └─────────────────┘
```

---

# 🧑‍💻 1. Development — VS Code

The RADIUS project is developed locally using **Visual Studio Code**.

The development environment contains:

- PHP marketplace application
- MySQL database integration
- HTML/CSS/JavaScript frontend
- Python/FastAPI AI service
- SerpAPI integration
- Authentication and session management
- Marketplace features
- Fraud-risk analysis
- Hyperlocal location features

### Development Flow

```text
VS Code
   │
   ├── PHP
   ├── HTML
   ├── CSS
   ├── JavaScript
   ├── Python
   └── FastAPI
        │
        ▼
   Local Testing
        │
        ▼
   Git Commit
```

---

# 🐙 2. Version Control — GitHub

After development and testing, the source code is pushed to **GitHub**.

GitHub is used for:

- Source code management
- Version control
- Collaboration
- Backup
- Deployment integration

### Git Flow

```text
VS Code
   │
   │ git add
   ▼
Git
   │
   │ git commit
   ▼
Local Repository
   │
   │ git push
   ▼
GitHub Repository
```

Example commands:

```bash
git add .
git commit -m "Update RADIUS marketplace"
git push origin main
```

---

# ☁️ 3. Deployment — Render

The RADIUS application is deployed using **Render**.

Render connects with the GitHub repository and deploys the application from the repository.

### Deployment Flow

```text
VS Code
   │
   ▼
GitHub
   │
   │ Deploy
   ▼
Render
   │
   ├── PHP Web Application
   │
   └── FastAPI AI Service
```

Whenever the application is updated and the changes are pushed to GitHub, Render can rebuild and redeploy the application.

---

# 🗄️ 4. Cloud Database — Aiven MySQL

RADIUS uses **Aiven MySQL** as its cloud-hosted database.

The database stores important marketplace information such as:

- Users
- Listings
- Listing images
- Conversations
- Messages
- Trade requests
- Reviews
- Reports
- Fraud predictions
- Price data

### Database Flow

```text
RADIUS Application
        │
        │ MySQL Connection
        ▼
   Aiven MySQL
        │
        ├── Users
        ├── Listings
        ├── Messages
        ├── Trades
        ├── Reviews
        ├── Reports
        └── Fraud Predictions
```

The application connects to Aiven using database credentials stored in environment variables.

Example:

```text
DB_HOST=********
DB_PORT=*****
DB_NAME=********
DB_USER=********
DB_PASSWORD=********
```

---

# 🐍 5. FastAPI AI Service

The AI component is implemented separately using **Python and FastAPI**.

The FastAPI service is responsible for processing listing information and returning structured risk-analysis results.

The separation provides several advantages:

- Independent AI service
- Easier model development
- Easier testing
- API-based communication
- Separation of marketplace and AI logic
- Future model replacement capability

### AI Service Flow

```text
PHP Application
      │
      │ HTTP Request
      ▼
FastAPI AI Service
      │
      ├── Price Analysis
      ├── Image Analysis
      ├── Text Analysis
      ├── Seller Signals
      └── Policy / Brand Signals
              │
              ▼
       Risk Score 0–100
              │
              ▼
         JSON Response
              │
              ▼
       PHP Application
```

---

# 🌐 6. External API — SerpAPI

SerpAPI provides an external search interface that can be used to retrieve search and product-related information.

### API Flow

```text
RADIUS
  │
  │ HTTPS Request
  ▼
SerpAPI
  │
  ▼
Search / Product Results
  │
  ▼
RADIUS
  │
  ▼
Market / Product Reference
```

SerpAPI can provide an additional data source for:

- Product lookup
- Market price reference
- Product comparison
- Search-based analysis

---

# 🔐 7. Environment Variables & Secrets

Sensitive credentials are not stored directly inside the source code.

RADIUS uses environment variables for sensitive configuration.

Typical variables include:

```text
DB_HOST
DB_PORT
DB_NAME
DB_USER
DB_PASSWORD

SERPAPI_KEY

FASTAPI_URL
```

Example:

```text
DB_HOST=your-aiven-host
DB_PORT=your-port
DB_NAME=radius
DB_USER=your-user
DB_PASSWORD=your-password

SERPAPI_KEY=your-serpapi-key

FASTAPI_URL=https://your-fastapi-service-url
```

> ⚠️ **Never commit passwords, API keys, database credentials, or secret keys to GitHub.**

---

# 🔄 Complete RADIUS Technology Flow

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
                ┌─────────────┼─────────────┐
                │             │             │
                ▼             ▼             ▼
          ┌──────────┐  ┌──────────┐  ┌──────────┐
          │  Aiven   │  │ FastAPI  │  │  SerpAPI │
          │  MySQL   │  │ AI       │  │ External │
          │   DB     │  │ Service  │  │   API    │
          └──────────┘  └────┬─────┘  └────┬─────┘
                             │             │
                             └──────┬──────┘
                                    │
                                    ▼
                            ┌───────────────┐
                            │     RADIUS    │
                            │               │
                            │ Marketplace   │
                            │ Chat          │
                            │ Trading       │
                            │ Reviews       │
                            │ Nearby Search │
                            │ Trust Radar   │
                            │ Fraud Risk    │
                            └───────────────┘
```

---

# 🚀 Production Request Flow

When a user accesses the deployed RADIUS application:

```text
User Browser
     │
     ▼
Render
     │
     ▼
RADIUS Application
     │
     ├───────────────────────┐
     │                       │
     ▼                       ▼
Aiven MySQL              FastAPI AI
     │                       │
     │                       ├── Risk Analysis
     │                       └── JSON Result
     │
     └──────────────┐
                    │
                    ▼
               RADIUS UI
                    │
                    ▼
               User Result
```

For market or product information:

```text
RADIUS
  │
  ▼
SerpAPI
  │
  ▼
Search / Market Data
  │
  ▼
RADIUS
  │
  ▼
Price / Product Analysis
```

---

# 🏗️ Complete System Architecture

```text
                         ┌──────────────────┐
                         │      USER        │
                         │   Web Browser    │
                         └────────┬─────────┘
                                  │
                                  ▼
                         ┌──────────────────┐
                         │      RENDER      │
                         │                  │
                         │  RADIUS Web App  │
                         └───────┬──────────┘
                                 │
              ┌──────────────────┼──────────────────┐
              │                  │                  │
              ▼                  ▼                  ▼
       ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
       │ Aiven MySQL │    │   FastAPI   │    │   SerpAPI   │
       │             │    │ AI Service  │    │             │
       │ Marketplace │    │             │    │ External    │
       │ Database    │    │ Risk Engine │    │ Search API  │
       └─────────────┘    └─────────────┘    └─────────────┘
              │                  │                  │
              └──────────────────┼──────────────────┘
                                 │
                                 ▼
                         ┌──────────────────┐
                         │     RADIUS       │
                         │                  │
                         │ Buy / Sell       │
                         │ Chat             │
                         │ Trade            │
                         │ Reviews          │
                         │ Nearby Search    │
                         │ Trust Radar      │
                         │ Fraud Detection  │
                         └──────────────────┘
```

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
├── trade-requests.php
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

- HTML5
- CSS3
- JavaScript
- Responsive Web Design

## Backend

- PHP
- PHP Sessions
- PDO / MySQL
- REST-style API communication

## Database

- MySQL
- Aiven MySQL
- SQL
- Relational database design

## AI Service

- Python
- FastAPI
- Machine Learning / AI-based risk analysis
- Image analysis
- Text analysis
- Price anomaly analysis

## External API

- SerpAPI
- Search API
- Product / market information

## Development & Deployment

- Visual Studio Code
- Git
- GitHub
- Render
- Aiven

## Local Development

- XAMPP
- Apache
- MySQL
- phpMyAdmin
- Python

---

# 🚀 Installation & Setup

## 1. Clone the Repository

```bash
git clone https://github.com/alfahim-fuyad/radius.git
cd radius
```

---

## 2. Local Development with VS Code

Open the project in Visual Studio Code:

```bash
code .
```

Make sure the required software is installed:

```text
PHP
MySQL
Python
Git
XAMPP
```

---

# 🖥️ 3. Start XAMPP

Start the following services from XAMPP:

```text
Apache
MySQL
```

---

# 📂 4. Move the Project

Copy the project into the XAMPP web directory:

```text
C:\xampp\htdocs\
```

Example:

```text
C:\xampp\htdocs\radius
```

---

# 🗄️ 5. Local Database Setup

Open:

```text
http://localhost/phpmyadmin
```

Create a database:

```text
radius
```

Import:

```text
database.sql
```

---

# 🔧 6. Database Configuration

For local development, configure the database connection according to your environment.

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

> For production, use the Aiven MySQL credentials through environment variables.

---

# 🐍 7. AI Service Setup

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

# ▶️ 8. Run the PHP Application

Start **Apache** and **MySQL** from XAMPP.

Then open:

```text
http://localhost/radius/
```

---

# ☁️ Production Deployment

The production architecture uses:

```text
VS Code
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

---

# 🐙 GitHub Deployment Flow

After making changes:

```bash
git add .
git commit -m "Update RADIUS"
git push origin main
```

Then Render can deploy the latest version from GitHub.

---

# ☁️ Render Configuration

Typical production environment variables may include:

```text
DB_HOST
DB_PORT
DB_NAME
DB_USER
DB_PASSWORD
SERPAPI_KEY
FASTAPI_URL
```

These should be configured inside the Render service environment settings.

---

# 🗄️ Aiven MySQL Production Database

The production application connects to the Aiven MySQL database.

```text
Render
  │
  │ Secure MySQL Connection
  ▼
Aiven MySQL
```

The database connection information should be stored as environment variables rather than hard-coded into PHP files.

---

# 🔎 SerpAPI Configuration

Add the SerpAPI key to the production environment:

```text
SERPAPI_KEY=your_api_key
```

The application can then send requests to SerpAPI when external product or market information is required.

---

# 🔐 Security Considerations

Security is an important component of RADIUS.

The application considers:

- Password hashing
- Session-based authentication
- Authorization
- SQL injection prevention
- Prepared statements
- Input validation
- File upload validation
- Access control
- User reporting
- Admin moderation
- Environment-based secrets
- API key protection

For production deployment, additional security hardening should be applied.

---

# 🧪 Testing

Testing should cover the major system components.

## Authentication

- Registration
- Login
- Logout
- Invalid credentials
- Session handling
- Authorization

## Marketplace

- Listing creation
- Listing editing
- Listing deletion
- Search
- Filtering
- Image uploads

## Location

- Latitude/longitude storage
- Nearby listing search
- Distance calculation
- Haversine formula

## Messaging

- Creating conversations
- Sending messages
- Receiving messages

## Trading

- Sending trade requests
- Accepting requests
- Rejecting requests
- Cancelling requests

## Reviews

- Creating reviews
- Rating validation
- Review display

## Reporting

- Creating reports
- Admin report review
- Listing moderation

## AI Service

- Price anomaly detection
- Image similarity analysis
- Text analysis
- Seller risk signals
- Risk score generation
- Risk explanation

## External API

- SerpAPI request
- API response handling
- Invalid API key handling
- API failure handling

---

# 📈 Future Improvements

Possible future improvements include:

- 🔔 Real-time notifications
- 💬 Real-time messaging using WebSockets
- 🤖 More advanced recommendation systems
- 🖼️ Improved image fraud detection
- 🧠 Deep-learning-based image analysis
- 💰 Better price prediction
- 👤 More sophisticated seller reputation scoring
- 📍 Location-based recommendation ranking
- 📱 Mobile application
- 💳 Online payment integration
- 🗺️ Map-based marketplace browsing
- 🛡️ Automated moderation
- 📊 Improved fraud-detection models
- 📈 Model performance monitoring
- 🗃️ More extensive fraud datasets
- 🔐 Improved security monitoring

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
10. Integrate external search information through SerpAPI.
11. Demonstrate cloud database integration using Aiven MySQL.
12. Deploy the application using GitHub and Render.
13. Provide a practical academic implementation of AI-assisted marketplace security.

---

# 📚 Academic Context

| Item | Details |
|---|---|
| **Project Name** | RADIUS — Hyperlocal Secondhand Marketplace |
| **Course** | CSE479 |
| **Project Type** | Academic Mini Project |
| **Backend** | PHP |
| **Database** | Aiven MySQL |
| **AI Service** | Python / FastAPI |
| **External API** | SerpAPI |
| **Frontend** | HTML / CSS / JavaScript |
| **Development** | Visual Studio Code |
| **Version Control** | Git / GitHub |
| **Deployment** | Render |
| **Local Environment** | XAMPP |
| **Location Calculation** | Haversine Formula |

---

# 🧰 Core Technologies

```text
PHP
MySQL
Aiven MySQL
Python
FastAPI
SerpAPI
JavaScript
HTML5
CSS3
Machine Learning / AI
Git
GitHub
Render
Visual Studio Code
XAMPP
```

---

# 🔄 One-Line Project Flow

```text
VS Code
   ↓
Git
   ↓
GitHub
   ↓
Render
   ↓
RADIUS Application
   ├── Aiven MySQL
   ├── FastAPI AI Service
   └── SerpAPI
```

---

# 🔄 Complete User-to-AI Flow

```text
User
 │
 ▼
RADIUS Web Application
 │
 ├───────────────► Aiven MySQL
 │                    │
 │                    └── Users / Listings / Messages /
 │                        Trades / Reviews / Reports
 │
 ├───────────────► FastAPI AI Service
 │                    │
 │                    ├── Price Analysis
 │                    ├── Image Analysis
 │                    ├── Text Analysis
 │                    ├── Seller Analysis
 │                    └── Policy / Brand Analysis
 │                           │
 │                           ▼
 │                      Risk Score
 │                           │
 │                           ▼
 │                      Trust Radar
 │
 └───────────────► SerpAPI
                      │
                      ▼
                 Search / Market
                    Information
```

---

# 🌐 Final Deployment Architecture

```text
                              INTERNET
                                  │
                                  ▼
                         ┌─────────────────┐
                         │      USER       │
                         │  Web Browser    │
                         └────────┬────────┘
                                  │
                                  ▼
                         ┌─────────────────┐
                         │     RENDER      │
                         │                 │
                         │ RADIUS Web App  │
                         └───────┬─────────┘
                                 │
                ┌────────────────┼────────────────┐
                │                │                │
                ▼                ▼                ▼
       ┌────────────────┐ ┌──────────────┐ ┌──────────────┐
       │  Aiven MySQL   │ │   FastAPI    │ │   SerpAPI    │
       │                │ │  AI Service  │ │              │
       │ Marketplace DB │ │              │ │ External API │
       └────────────────┘ └──────┬───────┘ └──────────────┘
                                 │
                                 ▼
                         ┌─────────────────┐
                         │  Risk Analysis  │
                         │                 │
                         │ Price           │
                         │ Image           │
                         │ Text            │
                         │ Seller          │
                         │ Policy          │
                         └────────┬────────┘
                                  │
                                  ▼
                         ┌─────────────────┐
                         │  Risk Score     │
                         │     0–100       │
                         └────────┬────────┘
                                  │
                                  ▼
                         ┌─────────────────┐
                         │     RADIUS      │
                         │   Trust Radar   │
                         └─────────────────┘
```

---

# ⚠️ Disclaimer

RADIUS is an **academic project** developed for educational and demonstration purposes.

The AI-based fraud-risk score is an assistive signal and should **not be treated as definitive proof of fraud**.

A high-risk score indicates that a listing contains multiple suspicious signals and may require further investigation.

Final decisions should involve appropriate human review.

---

# 📄 License

This project was developed for academic and educational purposes.

If you plan to publish or reuse this project, update this section with the appropriate license.

---

# ⭐ RADIUS

<p align="center">

**Discover Locally. Trade Securely. Build Trust.**

</p>
