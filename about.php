<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$user = current_user();

$pageTitle = 'About RADIUS';

require_once __DIR__ . '/includes/header.php';

?>

<style>

.about-page {
    max-width: 1150px;
    margin: 0 auto;
    padding: 40px 20px 70px;
}

/* =========================================================
   HERO
========================================================= */

.about-hero {
    background: linear-gradient(135deg, #eff6ff, #ffffff);
    border: 1px solid #dbeafe;
    border-radius: 24px;
    padding: 55px 35px;
    text-align: center;
    margin-bottom: 45px;
}

.about-badge {
    display: inline-block;
    background: #dbeafe;
    color: #1d4ed8;
    padding: 7px 15px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 15px;
}

.about-hero h1 {
    margin: 0 0 15px;
    font-size: 42px;
    color: #0f172a;
}

.about-hero h1 span {
    color: #2563eb;
}

.about-hero p {
    max-width: 780px;
    margin: auto;
    color: #64748b;
    font-size: 17px;
    line-height: 1.7;
}


/* =========================================================
   COMMON SECTION
========================================================= */

.about-section {
    margin-bottom: 50px;
}

.about-section h2 {
    color: #0f172a;
    font-size: 30px;
    margin-bottom: 12px;
}

.about-section > p {
    color: #64748b;
    line-height: 1.75;
}


/* =========================================================
   WHAT IS RADIUS
========================================================= */

.intro-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 30px;
}

.intro-box p {
    color: #475569;
    line-height: 1.8;
    margin: 0;
}

.tagline {
    margin-top: 20px;
    padding: 16px 20px;
    background: #eff6ff;
    border-radius: 12px;
    color: #1d4ed8;
    font-weight: 700;
    text-align: center;
}


/* =========================================================
   WHY RADIUS
========================================================= */

.why-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 25px;
}

.why-card {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    border-radius: 18px;
    padding: 25px;
}

.why-icon {
    font-size: 28px;
    margin-bottom: 12px;
}

.why-card h3 {
    margin: 0 0 8px;
    color: #0f172a;
}

.why-card p {
    margin: 0;
    color: #64748b;
    line-height: 1.6;
    font-size: 14px;
}


/* =========================================================
   HOW TO USE
========================================================= */

.guide {
    margin-top: 25px;
}

.guide-step {
    display: grid;
    grid-template-columns: 55px 1fr;
    gap: 18px;
    padding: 24px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    margin-bottom: 15px;
}

.step-number {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #2563eb;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 16px;
}

.guide-step h3 {
    margin: 0 0 8px;
    color: #0f172a;
    font-size: 19px;
}

.guide-step p {
    margin: 0;
    color: #64748b;
    line-height: 1.7;
}

.guide-step ul {
    margin: 10px 0 0;
    padding-left: 20px;
    color: #475569;
    line-height: 1.8;
}


/* =========================================================
   BUYER GUIDE
========================================================= */

.guide-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 22px;
    margin-top: 25px;
}

.guide-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 28px;
}

.guide-card h3 {
    margin-top: 0;
    color: #0f172a;
    font-size: 21px;
}

.guide-card p {
    color: #64748b;
    line-height: 1.7;
}

.guide-card ol {
    padding-left: 22px;
    color: #475569;
    line-height: 1.9;
}


/* =========================================================
   TRUST
========================================================= */

.trust-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 30px;
}

.trust-box h3 {
    margin-top: 0;
    color: #0f172a;
}

.trust-box p {
    color: #64748b;
    line-height: 1.7;
}

.risk-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-top: 20px;
}

.risk {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px;
    text-align: center;
}

.risk strong {
    display: block;
    color: #0f172a;
    margin-bottom: 6px;
}

.risk span {
    color: #64748b;
    font-size: 13px;
}


/* =========================================================
   SAFETY
========================================================= */

.safety-list {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 22px;
}

.safety-item {
    padding: 18px 20px;
    background: #eff6ff;
    border-radius: 14px;
    color: #334155;
    line-height: 1.6;
}

.safety-item strong {
    color: #1e3a8a;
}


/* =========================================================
   CTA
========================================================= */

.about-cta {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border-radius: 22px;
    padding: 42px 25px;
    text-align: center;
    color: #ffffff;
}

.about-cta h2 {
    color: #ffffff;
    margin: 0 0 10px;
}

.about-cta p {
    max-width: 650px;
    margin: 0 auto 22px;
    line-height: 1.6;
}

.about-buttons {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.about-buttons a {
    text-decoration: none;
    padding: 11px 20px;
    border-radius: 10px;
    font-weight: 700;
}

.btn-white {
    background: #ffffff;
    color: #1d4ed8;
}

.btn-outline {
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.5);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 800px) {

    .why-grid {
        grid-template-columns: 1fr;
    }

    .guide-grid {
        grid-template-columns: 1fr;
    }

    .risk-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .safety-list {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 600px) {

    .about-page {
        padding: 25px 15px 50px;
    }

    .about-hero {
        padding: 38px 20px;
    }

    .about-hero h1 {
        font-size: 32px;
    }

    .risk-grid {
        grid-template-columns: 1fr;
    }

    .guide-step {
        grid-template-columns: 1fr;
    }

}

</style>


<main class="about-page">


    <!-- =====================================================
         HERO
    ====================================================== -->

    <section class="about-hero">

        <div class="about-badge">
            About RADIUS
        </div>

        <h1>
            Discover Locally.
            <span>Trade Securely.</span>
        </h1>

        <p>
            RADIUS is a hyperlocal secondhand marketplace
            designed to help people discover nearby products,
            communicate with buyers and sellers, trade safely,
            and build trust through reviews and explainable
            risk signals.
        </p>

    </section>


    <!-- =====================================================
         WHAT IS RADIUS
    ====================================================== -->

    <section class="about-section">

        <h2>
            What is RADIUS?
        </h2>

        <div class="intro-box">

            <p>
                RADIUS is a PHP/MySQL-based secondhand
                marketplace where users can buy and sell
                products within their local area.
                The platform combines marketplace features
                with communication, trading, reviews,
                reporting, and an explainable AI-based
                trust system.
            </p>

            <div class="tagline">
                RADIUS — Discover Locally. Trade Securely. Build Trust.
            </div>

        </div>

    </section>


    <!-- =====================================================
         WHY RADIUS
    ====================================================== -->

    <section class="about-section">

        <h2>
            Why use RADIUS?
        </h2>

        <p>
            RADIUS is designed to make secondhand transactions
            easier, more local, and more transparent.
        </p>


        <div class="why-grid">


            <div class="why-card">

                <div class="why-icon">
                    📍
                </div>

                <h3>
                    Find Nearby Items
                </h3>

                <p>
                    Discover secondhand products located
                    closer to you.
                </p>

            </div>


            <div class="why-card">

                <div class="why-icon">
                    🛡️
                </div>

                <h3>
                    Understand Risk
                </h3>

                <p>
                    Use Trust Radar to understand potential
                    risk signals associated with listings.
                </p>

            </div>


            <div class="why-card">

                <div class="why-icon">
                    🤝
                </div>

                <h3>
                    Trade with Confidence
                </h3>

                <p>
                    Chat, request trades, complete transactions,
                    and leave reviews.
                </p>

            </div>

        </div>

    </section>


    <!-- =====================================================
         HOW TO USE RADIUS
    ====================================================== -->

    <section class="about-section">

        <h2>
            📖 How to Use RADIUS
        </h2>

        <p>
            Follow these steps to use the marketplace from
            registration to completing a transaction.
        </p>


        <div class="guide">


            <!-- STEP 1 -->

            <div class="guide-step">

                <div class="step-number">
                    1
                </div>

                <div>

                    <h3>
                        Create an Account
                    </h3>

                    <p>
                        Register a RADIUS account before buying,
                        selling, chatting, or trading.
                    </p>

                    <ul>
                        <li>Open the Register page.</li>
                        <li>Enter your name and email.</li>
                        <li>Create a secure password.</li>
                        <li>Log in to your account.</li>
                    </ul>

                </div>

            </div>


            <!-- STEP 2 -->

            <div class="guide-step">

                <div class="step-number">
                    2
                </div>

                <div>

                    <h3>
                        Browse the Marketplace
                    </h3>

                    <p>
                        Open the Browse/Marketplace page to
                        find products available from other users.
                    </p>

                    <ul>
                        <li>Search for a product.</li>
                        <li>Filter by category.</li>
                        <li>Check price and condition.</li>
                        <li>View the product location.</li>
                    </ul>

                </div>

            </div>


            <!-- STEP 3 -->

            <div class="guide-step">

                <div class="step-number">
                    3
                </div>

                <div>

                    <h3>
                        Check the Listing
                    </h3>

                    <p>
                        Open a listing before contacting the
                        seller and review the available information.
                    </p>

                    <ul>
                        <li>Read the product description.</li>
                        <li>Check product images.</li>
                        <li>Check price and condition.</li>
                        <li>Check seller information.</li>
                        <li>Review Trust Radar information.</li>
                    </ul>

                </div>

            </div>


            <!-- STEP 4 -->

            <div class="guide-step">

                <div class="step-number">
                    4
                </div>

                <div>

                    <h3>
                        View the Seller Profile
                    </h3>

                    <p>
                        Check the seller's profile and available
                        reputation information before proceeding.
                    </p>

                    <ul>
                        <li>View seller name.</li>
                        <li>Check profile information.</li>
                        <li>Check available reviews.</li>
                        <li>Consider previous transaction reputation.</li>
                    </ul>

                </div>

            </div>


            <!-- STEP 5 -->

            <div class="guide-step">

                <div class="step-number">
                    5
                </div>

                <div>

                    <h3>
                        Chat with the Seller
                    </h3>

                    <p>
                        Use the private chat feature to ask
                        questions before requesting a trade.
                    </p>

                    <ul>
                        <li>Ask about product condition.</li>
                        <li>Confirm availability.</li>
                        <li>Discuss price.</li>
                        <li>Discuss meeting/trade arrangements.</li>
                    </ul>

                </div>

            </div>


            <!-- STEP 6 -->

            <div class="guide-step">

                <div class="step-number">
                    6
                </div>

                <div>

                    <h3>
                        Send a Trade Request
                    </h3>

                    <p>
                        If you are satisfied with the listing,
                        seller, and discussion, send a trade request.
                    </p>

                    <ul>
                        <li>Open the listing.</li>
                        <li>Choose the trade/request option.</li>
                        <li>Wait for the seller's response.</li>
                    </ul>

                </div>

            </div>


            <!-- STEP 7 -->

            <div class="guide-step">

                <div class="step-number">
                    7
                </div>

                <div>

                    <h3>
                        Complete the Trade
                    </h3>

                    <p>
                        After the seller accepts the request,
                        complete the transaction according to
                        your agreed arrangement.
                    </p>

                    <ul>
                        <li>Confirm the product.</li>
                        <li>Inspect the item before payment/trade.</li>
                        <li>Complete the transaction.</li>
                    </ul>

                </div>

            </div>


            <!-- STEP 8 -->

            <div class="guide-step">

                <div class="step-number">
                    8
                </div>

                <div>

                    <h3>
                        Leave a Review
                    </h3>

                    <p>
                        After completing a trade, leave a rating
                        and optional comment to help other users.
                    </p>

                    <ul>
                        <li>Give a rating from 1–5.</li>
                        <li>Write a useful comment.</li>
                        <li>Help build marketplace reputation.</li>
                    </ul>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         SELLER GUIDE
    ====================================================== -->

    <section class="about-section">

        <h2>
            🏷️ How to Sell on RADIUS
        </h2>

        <div class="guide-grid">


            <div class="guide-card">

                <h3>
                    Create a Listing
                </h3>

                <p>
                    Sellers can publish their secondhand products
                    through the Create Listing page.
                </p>

                <ol>
                    <li>Open Create Listing.</li>
                    <li>Enter the product title.</li>
                    <li>Select a category.</li>
                    <li>Enter brand and condition.</li>
                    <li>Set the price.</li>
                    <li>Add location.</li>
                    <li>Write a clear description.</li>
                    <li>Upload product images.</li>
                    <li>Submit the listing.</li>
                </ol>

            </div>


            <div class="guide-card">

                <h3>
                    Manage Your Listing
                </h3>

                <p>
                    After creating a listing, sellers can
                    communicate with interested buyers and
                    manage trade requests.
                </p>

                <ol>
                    <li>Respond to buyer messages.</li>
                    <li>Answer product questions.</li>
                    <li>Review incoming trade requests.</li>
                    <li>Accept or reject requests.</li>
                    <li>Complete successful trades.</li>
                    <li>Receive buyer reviews.</li>
                </ol>

            </div>

        </div>

    </section>


    <!-- =====================================================
         TRUST RADAR
    ====================================================== -->

    <section class="about-section">

        <h2>
            🛡️ Understanding Trust Radar
        </h2>

        <div class="trust-box">

            <h3>
                What does the risk score mean?
            </h3>

            <p>
                RADIUS uses several signals to calculate a
                0–100 risk score. The score is intended to
                support human decision-making and moderation.
                It does not prove that a user or listing is fraudulent.
            </p>


            <div class="risk-grid">

                <div class="risk">
                    <strong>0–29</strong>
                    <span>🟢 Safe</span>
                </div>

                <div class="risk">
                    <strong>30–49</strong>
                    <span>🟡 Low Risk</span>
                </div>

                <div class="risk">
                    <strong>50–69</strong>
                    <span>🟠 Suspicious</span>
                </div>

                <div class="risk">
                    <strong>70–100</strong>
                    <span>🔴 High Risk</span>
                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         SAFETY TIPS
    ====================================================== -->

    <section class="about-section">

        <h2>
            ⚠️ Safe Trading Tips
        </h2>

        <div class="safety-list">

            <div class="safety-item">
                <strong>Inspect the product:</strong>
                Check the item carefully before completing a transaction.
            </div>

            <div class="safety-item">
                <strong>Check the seller:</strong>
                Review the seller profile and available reviews.
            </div>

            <div class="safety-item">
                <strong>Use RADIUS chat:</strong>
                Keep communication through the marketplace when possible.
            </div>

            <div class="safety-item">
                <strong>Check Trust Radar:</strong>
                Consider risk signals before proceeding.
            </div>

            <div class="safety-item">
                <strong>Report suspicious listings:</strong>
                Use the report feature when something appears unsafe.
            </div>

            <div class="safety-item">
                <strong>Do not share sensitive information:</strong>
                Never share passwords, OTPs, or unnecessary personal information.
            </div>

        </div>

    </section>


    <!-- =====================================================
         CTA
    ====================================================== -->

    <section class="about-cta">

        <h2>
            Ready to use RADIUS?
        </h2>

        <p>
            Start by browsing nearby secondhand products
            or create your own listing.
        </p>

        <div class="about-buttons">

            <a
                href="listings.php"
                class="btn-white"
            >
                Browse Marketplace
            </a>

            <?php if ($user): ?>

                <a
                    href="create-listing.php"
                    class="btn-outline"
                >
                    Sell an Item
                </a>

            <?php else: ?>

                <a
                    href="register.php"
                    class="btn-outline"
                >
                    Create Account
                </a>

            <?php endif; ?>

        </div>

    </section>


</main>


<?php

require_once __DIR__ . '/includes/footer.php';

?>