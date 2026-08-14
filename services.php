<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$user = current_user();

$pageTitle = 'RADIUS Services';

require_once __DIR__ . '/includes/header.php';

?>

<style>

.services-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px 70px;
}

/* =========================================================
   HERO
========================================================= */

.services-hero {
    background: linear-gradient(135deg, #eff6ff, #ffffff);
    border: 1px solid #dbeafe;
    border-radius: 24px;
    padding: 55px 40px;
    text-align: center;
    margin-bottom: 40px;
}

.services-hero .badge {
    display: inline-block;
    background: #dbeafe;
    color: #1d4ed8;
    padding: 7px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 15px;
}

.services-hero h1 {
    margin: 0 0 15px;
    font-size: 42px;
    color: #0f172a;
}

.services-hero h1 span {
    color: #2563eb;
}

.services-hero p {
    max-width: 780px;
    margin: 0 auto;
    color: #64748b;
    font-size: 17px;
    line-height: 1.7;
}


/* =========================================================
   SERVICE GRID
========================================================= */

.services-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
}

.service-card {
    position: relative;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 28px;
    transition: .2s ease;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(15, 23, 42, .08);
}

.service-number {
    position: absolute;
    top: 20px;
    right: 22px;
    color: #cbd5e1;
    font-weight: 800;
    font-size: 14px;
}

.service-icon {
    width: 55px;
    height: 55px;
    border-radius: 16px;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin-bottom: 18px;
}

.service-card h3 {
    margin: 0 0 10px;
    color: #0f172a;
    font-size: 20px;
}

.service-card p {
    margin: 0 0 15px;
    color: #64748b;
    line-height: 1.65;
    font-size: 14px;
}

.service-card ul {
    padding-left: 20px;
    margin: 0;
    color: #475569;
    line-height: 1.8;
    font-size: 14px;
}


/* =========================================================
   TRUST SECTION
========================================================= */

.trust-section {
    margin-top: 50px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 22px;
    padding: 35px;
}

.trust-section h2 {
    margin-top: 0;
    color: #0f172a;
    font-size: 28px;
}

.trust-section > p {
    color: #64748b;
    line-height: 1.7;
}

.score-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
    margin-top: 25px;
}

.score-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    text-align: center;
}

.score-card strong {
    display: block;
    font-size: 26px;
    color: #2563eb;
    margin-bottom: 7px;
}

.score-card span {
    color: #64748b;
    font-size: 13px;
}


/* =========================================================
   RISK LEVELS
========================================================= */

.risk-title {
    margin-top: 32px;
    color: #0f172a;
}

.risk-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-top: 18px;
}

.risk-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 15px;
    padding: 18px;
    text-align: center;
}

.risk-card strong {
    display: block;
    color: #0f172a;
    margin-bottom: 6px;
    font-size: 18px;
}

.risk-card span {
    color: #64748b;
    font-size: 13px;
}


/* =========================================================
   WORKFLOW
========================================================= */

.workflow {
    margin-top: 50px;
}

.workflow h2 {
    color: #0f172a;
    font-size: 28px;
    text-align: center;
}

.workflow > p {
    color: #64748b;
    text-align: center;
    max-width: 700px;
    margin: 10px auto 0;
    line-height: 1.7;
}

.workflow-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
    margin-top: 28px;
}

.workflow-step {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 22px 15px;
    text-align: center;
}

.workflow-number {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #2563eb;
    color: #ffffff;
    margin: 0 auto 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
}

.workflow-step h3 {
    margin: 0 0 7px;
    color: #0f172a;
    font-size: 16px;
}

.workflow-step p {
    margin: 0;
    color: #64748b;
    font-size: 13px;
    line-height: 1.5;
}


/* =========================================================
   SECURITY
========================================================= */

.security-section {
    margin-top: 50px;
}

.security-section h2 {
    color: #0f172a;
    font-size: 28px;
}

.security-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-top: 22px;
}

.security-card {
    background: #eff6ff;
    border-radius: 17px;
    padding: 22px;
}

.security-card h3 {
    margin: 0 0 8px;
    color: #1e3a8a;
    font-size: 16px;
}

.security-card p {
    margin: 0;
    color: #64748b;
    font-size: 13px;
    line-height: 1.6;
}


/* =========================================================
   USER SERVICES
========================================================= */

.user-services {
    margin-top: 50px;
}

.user-services h2 {
    color: #0f172a;
    font-size: 28px;
}

.user-service-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 22px;
    margin-top: 22px;
}

.user-service-card {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    border-radius: 20px;
    padding: 28px;
}

.user-service-card h3 {
    margin-top: 0;
    color: #0f172a;
    font-size: 21px;
}

.user-service-card p {
    color: #64748b;
    line-height: 1.6;
}

.user-service-card ul {
    margin: 15px 0 0;
    padding-left: 20px;
    color: #475569;
    line-height: 1.9;
}


/* =========================================================
   NOTE
========================================================= */

.note {
    background: #fff7ed;
    border: 1px solid #fed7aa;
    color: #9a3412;
    border-radius: 16px;
    padding: 18px 20px;
    margin-top: 25px;
    line-height: 1.6;
}


/* =========================================================
   CTA
========================================================= */

.services-cta {
    margin-top: 50px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff;
    border-radius: 22px;
    padding: 42px 30px;
    text-align: center;
}

.services-cta h2 {
    color: #ffffff;
    margin: 0 0 10px;
    font-size: 28px;
}

.services-cta p {
    margin: 0 auto 20px;
    max-width: 650px;
    line-height: 1.6;
    opacity: .9;
}

.cta-buttons {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.cta-buttons a {
    display: inline-block;
    padding: 11px 20px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 700;
}

.cta-primary {
    background: #ffffff;
    color: #1d4ed8;
}

.cta-secondary {
    background: rgba(255,255,255,.15);
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.35);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 950px) {

    .services-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .score-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .risk-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .workflow-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .security-grid {
        grid-template-columns: repeat(2, 1fr);
    }

}


@media (max-width: 650px) {

    .services-page {
        padding: 25px 15px 50px;
    }

    .services-hero {
        padding: 38px 20px;
    }

    .services-hero h1 {
        font-size: 32px;
    }

    .services-grid,
    .score-grid,
    .risk-grid,
    .workflow-grid,
    .security-grid,
    .user-service-grid {
        grid-template-columns: 1fr;
    }

    .trust-section {
        padding: 25px 18px;
    }

}

</style>


<main class="services-page">


    <!-- =====================================================
         HERO
    ====================================================== -->

    <section class="services-hero">

        <div class="badge">
            RADIUS Marketplace Services
        </div>

        <h1>
            Services Built for
            <span>Safer Trading</span>
        </h1>

        <p>
            RADIUS brings together marketplace discovery,
            buying and selling, private communication,
            trade management, reviews, reporting, and
            explainable AI-based trust analysis.
        </p>

    </section>


    <!-- =====================================================
         MAIN SERVICES
    ====================================================== -->

    <section class="services-grid">


        <!-- 01 -->

        <div class="service-card">

            <div class="service-number">
                01
            </div>

            <div class="service-icon">
                🛍️
            </div>

            <h3>
                Buy & Sell Marketplace
            </h3>

            <p>
                Find secondhand products nearby or create
                your own listing and sell items to local buyers.
            </p>

            <ul>
                <li>Browse listings</li>
                <li>Search products</li>
                <li>Filter by category</li>
                <li>Nearby discovery</li>
                <li>Create listings</li>
            </ul>

        </div>


        <!-- 02 -->

        <div class="service-card">

            <div class="service-number">
                02
            </div>

            <div class="service-icon">
                📍
            </div>

            <h3>
                Hyperlocal Discovery
            </h3>

            <p>
                Discover secondhand products based on location
                so buyers can find items closer to them.
            </p>

            <ul>
                <li>Location-based listings</li>
                <li>Latitude & longitude</li>
                <li>Nearby products</li>
                <li>Local marketplace</li>
            </ul>

        </div>


        <!-- 03 -->

        <div class="service-card">

            <div class="service-number">
                03
            </div>

            <div class="service-icon">
                💬
            </div>

            <h3>
                Buyer–Seller Chat
            </h3>

            <p>
                Communicate directly with another marketplace
                participant before making a trade.
            </p>

            <ul>
                <li>Private conversations</li>
                <li>Buyer–seller messaging</li>
                <li>AJAX polling</li>
                <li>Participant authorization</li>
            </ul>

        </div>


        <!-- 04 -->

        <div class="service-card">

            <div class="service-number">
                04
            </div>

            <div class="service-icon">
                🤝
            </div>

            <h3>
                Trade Requests
            </h3>

            <p>
                Manage the complete trade process between
                buyers and sellers.
            </p>

            <ul>
                <li>Send trade request</li>
                <li>Accept request</li>
                <li>Reject request</li>
                <li>Cancel request</li>
                <li>Complete trade</li>
            </ul>

        </div>


        <!-- 05 -->

        <div class="service-card">

            <div class="service-number">
                05
            </div>

            <div class="service-icon">
                🛡️
            </div>

            <h3>
                Trust Radar
            </h3>

            <p>
                Understand listing risk through an explainable
                0–100 trust and fraud-risk score.
            </p>

            <ul>
                <li>Risk score</li>
                <li>Risk classification</li>
                <li>Explainable signals</li>
                <li>Human moderation</li>
            </ul>

        </div>


        <!-- 06 -->

        <div class="service-card">

            <div class="service-number">
                06
            </div>

            <div class="service-icon">
                🤖
            </div>

            <h3>
                AI Fraud Analysis
            </h3>

            <p>
                Analyze listings using multiple machine-learning
                and rule-based risk signals.
            </p>

            <ul>
                <li>Image similarity</li>
                <li>Price anomaly</li>
                <li>Seller risk</li>
                <li>Text risk</li>
                <li>Policy / brand risk</li>
            </ul>

        </div>


        <!-- 07 -->

        <div class="service-card">

            <div class="service-number">
                07
            </div>

            <div class="service-icon">
                👤
            </div>

            <h3>
                User Profiles
            </h3>

            <p>
                View marketplace users and build a trustworthy
                reputation through completed trades.
            </p>

            <ul>
                <li>Buyer profiles</li>
                <li>Seller profiles</li>
                <li>User information</li>
                <li>Trade history</li>
                <li>Reputation</li>
            </ul>

        </div>


        <!-- 08 -->

        <div class="service-card">

            <div class="service-number">
                08
            </div>

            <div class="service-icon">
                ⭐
            </div>

            <h3>
                Reviews & Ratings
            </h3>

            <p>
                Users can review completed trades and help
                build trust across the marketplace.
            </p>

            <ul>
                <li>1–5 star rating</li>
                <li>Written comments</li>
                <li>Trade-based reviews</li>
                <li>Duplicate review protection</li>
            </ul>

        </div>


        <!-- 09 -->

        <div class="service-card">

            <div class="service-number">
                09
            </div>

            <div class="service-icon">
                🚨
            </div>

            <h3>
                Listing Reports
            </h3>

            <p>
                Users can report potentially harmful or
                suspicious marketplace listings.
            </p>

            <ul>
                <li>Scam reports</li>
                <li>Fake products</li>
                <li>Suspicious prices</li>
                <li>Reused images</li>
                <li>Prohibited items</li>
            </ul>

        </div>


        <!-- 10 -->

        <div class="service-card">

            <div class="service-number">
                10
            </div>

            <div class="service-icon">
                ⚖️
            </div>

            <h3>
                Admin Moderation
            </h3>

            <p>
                Administrators can investigate suspicious
                listings and reports.
            </p>

            <ul>
                <li>Fraud queue</li>
                <li>Manual review</li>
                <li>Listing moderation</li>
                <li>Report management</li>
                <li>AI analysis retry</li>
            </ul>

        </div>


        <!-- 11 -->

        <div class="service-card">

            <div class="service-number">
                11
            </div>

            <div class="service-icon">
                🔐
            </div>

            <h3>
                Secure Authentication
            </h3>

            <p>
                RADIUS protects user accounts with secure
                authentication and authorization mechanisms.
            </p>

            <ul>
                <li>PHP sessions</li>
                <li>Password hashing</li>
                <li>Password verification</li>
                <li>Role-based authorization</li>
            </ul>

        </div>


        <!-- 12 -->

        <div class="service-card">

            <div class="service-number">
                12
            </div>

            <div class="service-icon">
                📷
            </div>

            <h3>
                Secure Image Upload
            </h3>

            <p>
                Product images are validated before being
                stored in the marketplace.
            </p>

            <ul>
                <li>MIME validation</li>
                <li>Image validation</li>
                <li>Randomized filenames</li>
                <li>Image hashing</li>
            </ul>

        </div>

    </section>


    <!-- =====================================================
         TRUST RADAR
    ====================================================== -->

    <section class="trust-section">

        <h2>
            🛡️ Trust Radar & Fraud-Risk Analysis
        </h2>

        <p>
            RADIUS generates an explainable risk score from
            0 to 100 by combining several signals from the
            listing, seller, image, price, text, and policy
            analysis.
        </p>


        <div class="score-grid">


            <div class="score-card">

                <strong>
                    25%
                </strong>

                <span>
                    🖼️ Image Similarity
                </span>

            </div>


            <div class="score-card">

                <strong>
                    25%
                </strong>

                <span>
                    💰 Price Anomaly
                </span>

            </div>


            <div class="score-card">

                <strong>
                    20%
                </strong>

                <span>
                    👤 Seller Risk
                </span>

            </div>


            <div class="score-card">

                <strong>
                    20%
                </strong>

                <span>
                    📝 Text Risk
                </span>

            </div>


            <div class="score-card">

                <strong>
                    10%
                </strong>

                <span>
                    ⚠️ Policy / Brand
                </span>

            </div>

        </div>


        <h3 class="risk-title">
            Risk Levels
        </h3>


        <div class="risk-grid">


            <div class="risk-card">

                <strong>
                    0–29
                </strong>

                <span>
                    🟢 Safe
                </span>

            </div>


            <div class="risk-card">

                <strong>
                    30–49
                </strong>

                <span>
                    🟡 Low Risk
                </span>

            </div>


            <div class="risk-card">

                <strong>
                    50–69
                </strong>

                <span>
                    🟠 Suspicious
                </span>

            </div>


            <div class="risk-card">

                <strong>
                    70–100
                </strong>

                <span>
                    🔴 High Risk
                </span>

            </div>

        </div>


        <div class="note">

            <strong>Important:</strong>

            The trust system produces risk signals,
            not proof of fraud. Suspicious or high-risk
            listings should be reviewed by human
            administrators.

        </div>

    </section>


    <!-- =====================================================
         HOW THE SERVICE WORKS
    ====================================================== -->

    <section class="workflow">

        <h2>
            How RADIUS Services Work
        </h2>

        <p>
            From discovering an item to completing a trade,
            RADIUS connects marketplace tools and trust
            features into one workflow.
        </p>


        <div class="workflow-grid">


            <div class="workflow-step">

                <div class="workflow-number">
                    1
                </div>

                <h3>
                    Discover
                </h3>

                <p>
                    Find products nearby.
                </p>

            </div>


            <div class="workflow-step">

                <div class="workflow-number">
                    2
                </div>

                <h3>
                    Inspect
                </h3>

                <p>
                    View listing and trust details.
                </p>

            </div>


            <div class="workflow-step">

                <div class="workflow-number">
                    3
                </div>

                <h3>
                    Chat
                </h3>

                <p>
                    Communicate with the seller.
                </p>

            </div>


            <div class="workflow-step">

                <div class="workflow-number">
                    4
                </div>

                <h3>
                    Trade
                </h3>

                <p>
                    Send and manage a trade request.
                </p>

            </div>


            <div class="workflow-step">

                <div class="workflow-number">
                    5
                </div>

                <h3>
                    Review
                </h3>

                <p>
                    Review the completed transaction.
                </p>

            </div>

        </div>

    </section>


    <!-- =====================================================
         BUYER / SELLER SERVICES
    ====================================================== -->

    <section class="user-services">

        <h2>
            Services for Different Users
        </h2>


        <div class="user-service-grid">


            <!-- BUYER -->

            <div class="user-service-card">

                <h3>
                    🛒 Buyer Services
                </h3>

                <p>
                    Buyers get tools to discover products,
                    evaluate sellers, communicate, and
                    manage transactions.
                </p>

                <ul>

                    <li>
                        Browse nearby listings
                    </li>

                    <li>
                        Search and filter products
                    </li>

                    <li>
                        View product details
                    </li>

                    <li>
                        Check Trust Radar
                    </li>

                    <li>
                        View seller profile
                    </li>

                    <li>
                        Chat with seller
                    </li>

                    <li>
                        Send trade request
                    </li>

                    <li>
                        Leave review after trade
                    </li>

                </ul>

            </div>


            <!-- SELLER -->

            <div class="user-service-card">

                <h3>
                    🏷️ Seller Services
                </h3>

                <p>
                    Sellers can publish products, communicate
                    with buyers, and manage incoming trades.
                </p>

                <ul>

                    <li>
                        Create product listings
                    </li>

                    <li>
                        Upload product images
                    </li>

                    <li>
                        Set price and condition
                    </li>

                    <li>
                        Receive trust analysis
                    </li>

                    <li>
                        Chat with interested buyers
                    </li>

                    <li>
                        Receive trade requests
                    </li>

                    <li>
                        Accept or reject trades
                    </li>

                    <li>
                        Receive reviews
                    </li>

                </ul>

            </div>

        </div>

    </section>


    <!-- =====================================================
         SECURITY
    ====================================================== -->

    <section class="security-section">

        <h2>
            🔐 Security Services
        </h2>


        <div class="security-grid">


            <div class="security-card">

                <h3>
                    Password Protection
                </h3>

                <p>
                    Passwords are stored using PHP's secure
                    password hashing API.
                </p>

            </div>


            <div class="security-card">

                <h3>
                    CSRF Protection
                </h3>

                <p>
                    Important state-changing requests are
                    protected against CSRF attacks.
                </p>

            </div>


            <div class="security-card">

                <h3>
                    Database Protection
                </h3>

                <p>
                    PDO prepared statements help protect
                    database queries from SQL injection.
                </p>

            </div>


            <div class="security-card">

                <h3>
                    Role Authorization
                </h3>

                <p>
                    Administrative functionality is restricted
                    using role-based authorization.
                </p>

            </div>


            <div class="security-card">

                <h3>
                    Secure Uploads
                </h3>

                <p>
                    Uploaded images are validated before
                    being stored.
                </p>

            </div>


            <div class="security-card">

                <h3>
                    Chat Authorization
                </h3>

                <p>
                    Private conversations are accessible only
                    to authorized participants.
                </p>

            </div>


            <div class="security-card">

                <h3>
                    Trade Validation
                </h3>

                <p>
                    Trade state transitions are checked before
                    actions are performed.
                </p>

            </div>


            <div class="security-card">

                <h3>
                    Output Escaping
                </h3>

                <p>
                    User-generated content is escaped before
                    being displayed.
                </p>

            </div>

        </div>

    </section>


    <!-- =====================================================
         CTA
    ====================================================== -->

    <section class="services-cta">

        <h2>
            Discover Locally. Trade Securely. Build Trust.
        </h2>

        <p>
            Explore nearby secondhand products or create
            your own listing with RADIUS.
        </p>


        <div class="cta-buttons">

            <a
                href="listings.php"
                class="cta-primary"
            >
                Browse Marketplace
            </a>


            <?php if (!$user): ?>

                <a
                    href="register.php"
                    class="cta-secondary"
                >
                    Create Account
                </a>

            <?php else: ?>

                <a
                    href="create-listing.php"
                    class="cta-secondary"
                >
                    Sell an Item
                </a>

            <?php endif; ?>

        </div>

    </section>


</main>


<?php

require_once __DIR__ . '/includes/footer.php';

?>