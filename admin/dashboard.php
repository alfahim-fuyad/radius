<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/csrf.php';

require_admin();

$pdo = db();


/* =========================================================
   AI SERVICE
========================================================= */

$AI_SERVICE_URL = 'http://127.0.0.1:8001';


/* =========================================================
   HELPER: RUN AI FRAUD CHECK
========================================================= */

function run_fraud_check(PDO $pdo, int $listingId): array
{
    global $AI_SERVICE_URL;


    /* -----------------------------------------------------
       Get listing
    ----------------------------------------------------- */

    $stmt = $pdo->prepare("
        SELECT
            l.*,
            u.name,
            u.email,
            u.created_at AS user_created_at
        FROM listings l
        JOIN users u
            ON u.id = l.user_id
        WHERE l.id = ?
        LIMIT 1
    ");

    $stmt->execute([$listingId]);

    $listing = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$listing) {
        throw new Exception('Listing not found.');
    }


    /* -----------------------------------------------------
       Get seller information
    ----------------------------------------------------- */

    $sellerId = (int)$listing['user_id'];


    /* Account age */

    $accountAgeStmt = $pdo->prepare("
        SELECT DATEDIFF(NOW(), created_at)
        FROM users
        WHERE id = ?
    ");

    $accountAgeStmt->execute([$sellerId]);

    $accountAgeDays = (int)$accountAgeStmt->fetchColumn();


    /* Reports */

    $reportStmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM reports
        WHERE reporter_id = ?
    ");

    $reportStmt->execute([$sellerId]);

    $reportCount = (int)$reportStmt->fetchColumn();


    /* Removed listings */

    $removedStmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM listings
        WHERE user_id = ?
          AND status = 'removed'
    ");

    $removedStmt->execute([$sellerId]);

    $removedListings = (int)$removedStmt->fetchColumn();


    /* Suspicious listings */

    $suspiciousStmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM listings
        WHERE user_id = ?
          AND trust_status IN ('suspicious', 'high_risk')
    ");

    $suspiciousStmt->execute([$sellerId]);

    $suspiciousListings = (int)$suspiciousStmt->fetchColumn();


    /* Completed trades */

    $tradeStmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM trade_requests
        WHERE seller_id = ?
          AND status = 'completed'
    ");

    $tradeStmt->execute([$sellerId]);

    $completedTrades = (int)$tradeStmt->fetchColumn();


    /* Seller rating */

    $ratingStmt = $pdo->prepare("
        SELECT COALESCE(AVG(rating), 0)
        FROM reviews
        WHERE reviewed_user_id = ?
    ");

    $ratingStmt->execute([$sellerId]);

    $ratingAverage = (float)$ratingStmt->fetchColumn();


    /* -----------------------------------------------------
       Existing image hashes
    ----------------------------------------------------- */

    $imageStmt = $pdo->query("
        SELECT image_hash
        FROM listing_images
        WHERE image_hash IS NOT NULL
    ");

    $existingImageHashes = [];

    while ($row = $imageStmt->fetch(PDO::FETCH_ASSOC)) {

        if (!empty($row['image_hash'])) {
            $existingImageHashes[] = $row['image_hash'];
        }

    }


    /* -----------------------------------------------------
       Current listing image hashes
    ----------------------------------------------------- */

    $currentImageStmt = $pdo->prepare("
        SELECT image_hash
        FROM listing_images
        WHERE listing_id = ?
          AND image_hash IS NOT NULL
    ");

    $currentImageStmt->execute([$listingId]);

    $imageHashes = [];

    while ($row = $currentImageStmt->fetch(PDO::FETCH_ASSOC)) {

        if (!empty($row['image_hash'])) {
            $imageHashes[] = $row['image_hash'];
        }

    }


    /* -----------------------------------------------------
       Existing descriptions
    ----------------------------------------------------- */

    $descriptionStmt = $pdo->prepare("
        SELECT description
        FROM listings
        WHERE id <> ?
          AND description IS NOT NULL
        LIMIT 500
    ");

    $descriptionStmt->execute([$listingId]);

    $existingDescriptions = [];

    while ($row = $descriptionStmt->fetch(PDO::FETCH_ASSOC)) {

        if (!empty($row['description'])) {
            $existingDescriptions[] = $row['description'];
        }

    }


    /* -----------------------------------------------------
       Build AI request
    ----------------------------------------------------- */

    $payload = [

        'title' => (string)$listing['title'],

        'description' => (string)$listing['description'],

        'category' => (string)$listing['category'],

        'brand' => (string)($listing['brand'] ?? ''),

        'condition' => (string)$listing['item_condition'],

        'price' => (float)$listing['price'],

        'seller_information' => [

            'account_age_days' => $accountAgeDays,

            'report_count' => $reportCount,

            'removed_listings' => $removedListings,

            'suspicious_listings' => $suspiciousListings,

            'completed_trades' => $completedTrades,

            'rating_average' => $ratingAverage

        ],

        'image_hashes' => $imageHashes,

        'existing_image_hashes' => $existingImageHashes,

        'existing_descriptions' => $existingDescriptions

    ];


    /* -----------------------------------------------------
       Call FastAPI
    ----------------------------------------------------- */

    $ch = curl_init(
        $AI_SERVICE_URL . '/analyze-listing'
    );


    curl_setopt_array($ch, [

        CURLOPT_POST => true,

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_HTTPHEADER => [

            'Content-Type: application/json'

        ],

        CURLOPT_POSTFIELDS => json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE
        ),

        CURLOPT_CONNECTTIMEOUT => 5,

        CURLOPT_TIMEOUT => 60

    ]);


    $response = curl_exec($ch);

    $curlError = curl_error($ch);

    $httpCode = (int)curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    curl_close($ch);


    if ($response === false || $curlError) {

        throw new Exception(
            'Could not connect to AI service: '
            . $curlError
        );

    }


    if ($httpCode !== 200) {

        throw new Exception(
            'AI service returned HTTP '
            . $httpCode
            . ': '
            . $response
        );

    }


    $result = json_decode(
        $response,
        true
    );


    if (!is_array($result)) {

        throw new Exception(
            'Invalid response from AI service.'
        );

    }


    /* -----------------------------------------------------
       Save listing fraud result
    ----------------------------------------------------- */

    $update = $pdo->prepare("
        UPDATE listings
        SET
            fraud_score = ?,
            trust_status = ?,
            fraud_checked = 1,
            updated_at = NOW()
        WHERE id = ?
    ");

    $update->execute([

        (float)$result['fraud_score'],

        $result['trust_status'],

        $listingId

    ]);


    /* -----------------------------------------------------
       Save detailed prediction
    ----------------------------------------------------- */

    $prediction = $pdo->prepare("
        INSERT INTO fraud_predictions
        (
            listing_id,
            fraud_score,
            image_score,
            price_score,
            seller_score,
            text_score,
            policy_score,
            model_name,
            model_version,
            explanation,
            feature_snapshot
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");


    $prediction->execute([

        $listingId,

        (float)$result['fraud_score'],

        (float)$result['image_score'],

        (float)$result['price_score'],

        (float)$result['seller_score'],

        (float)$result['text_score'],

        (float)$result['policy_score'],

        (string)$result['model_name'],

        (string)$result['model_version'],

        (string)$result['explanation'],

        json_encode(
            $result['feature_snapshot'] ?? [],
            JSON_UNESCAPED_UNICODE
        )

    ]);


    return $result;
}



/* =========================================================
   POST ACTIONS
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $listingId = (int)(
        $_POST['listing_id'] ?? 0
    );

    $action = $_POST['action'] ?? '';


    /* =====================================================
       RUN FRAUD CHECK
    ===================================================== */

    if ($action === 'fraud_check') {

        try {

            run_fraud_check(
                $pdo,
                $listingId
            );


            flash(
                'success',
                'Fraud check completed successfully.'
            );

        } catch (Throwable $e) {

            flash(
                'error',
                'Fraud check failed: '
                . $e->getMessage()
            );

        }


        redirect(
            '/admin/dashboard.php'
        );
    }



    /* =====================================================
       APPROVE
    ===================================================== */

    if ($action === 'approve') {

        $stmt = $pdo->prepare("
            UPDATE listings
            SET
                status = 'approved',
                updated_at = NOW()
            WHERE id = ?
              AND status IN ('pending', 'flagged')
        ");

        $stmt->execute([
            $listingId
        ]);


        flash(
            'success',
            'Listing approved successfully.'
        );


        redirect(
            '/admin/dashboard.php'
        );
    }



    /* =====================================================
       REJECT
    ===================================================== */

    if ($action === 'reject') {

        $stmt = $pdo->prepare("
            UPDATE listings
            SET
                status = 'removed',
                updated_at = NOW()
            WHERE id = ?
              AND status IN ('pending', 'flagged')
        ");

        $stmt->execute([
            $listingId
        ]);


        flash(
            'success',
            'Listing rejected successfully.'
        );


        redirect(
            '/admin/dashboard.php'
        );
    }

}



/* =========================================================
   STATISTICS
========================================================= */

$stats = [

    'Total Users' => (int)$pdo->query(
        'SELECT COUNT(*) FROM users'
    )->fetchColumn(),


    'Total Listings' => (int)$pdo->query(
        'SELECT COUNT(*) FROM listings'
    )->fetchColumn(),


    'Pending' => (int)$pdo->query(
        "SELECT COUNT(*)
         FROM listings
         WHERE status = 'pending'"
    )->fetchColumn(),


    'Suspicious' => (int)$pdo->query(
        "SELECT COUNT(*)
         FROM listings
         WHERE trust_status = 'suspicious'"
    )->fetchColumn(),


    'High Risk' => (int)$pdo->query(
        "SELECT COUNT(*)
         FROM listings
         WHERE trust_status = 'high_risk'"
    )->fetchColumn(),


    'Reports' => (int)$pdo->query(
        "SELECT COUNT(*)
         FROM reports
         WHERE status = 'open'"
    )->fetchColumn(),


    'Completed Trades' => (int)$pdo->query(
        "SELECT COUNT(*)
         FROM trade_requests
         WHERE status = 'completed'"
    )->fetchColumn()

];



/* =========================================================
   PENDING / FLAGGED LISTINGS
========================================================= */

$queue = $pdo->query("

    SELECT

        l.id,
        l.title,
        l.fraud_score,
        l.trust_status,
        l.fraud_checked,
        l.status,
        l.created_at,

        u.name AS seller

    FROM listings l

    JOIN users u
        ON u.id = l.user_id

    WHERE l.status IN (
        'pending',
        'flagged'
    )

    ORDER BY

        CASE
            WHEN l.fraud_score IS NULL
            THEN 999999
            ELSE -l.fraud_score
        END,

        l.created_at DESC

    LIMIT 8

")->fetchAll();



$pageTitle = 'Admin Dashboard';

include dirname(__DIR__) . '/includes/header.php';

?>


<div class="section-head">

    <div>

        <h2>
            Admin dashboard
        </h2>

        <p>
            Marketplace moderation, reports,
            analytics, and ML prediction logs.
        </p>

    </div>


    <div class="actions">

        <a
            class="btn btn-sm"
            href="/admin/fraud_queue.php"
        >
            Fraud Queue
        </a>


        <a
            class="btn btn-sm btn-light"
            href="/admin/reports.php"
        >
            Reports
        </a>


        <a
            class="btn btn-sm btn-light"
            href="/admin/users.php"
        >
            Users
        </a>


        <a
            class="btn btn-sm btn-light"
            href="/admin/listings.php"
        >
            Listings
        </a>

    </div>

</div>



<div class="stats">

    <?php foreach ($stats as $k => $v): ?>

        <div class="stat">

            <span class="muted">
                <?= e($k) ?>
            </span>

            <strong>
                <?= $v ?>
            </strong>

        </div>

    <?php endforeach; ?>

</div>



<div class="section-head">

    <h2>
        Pending Listings
    </h2>

</div>



<div class="table-wrap">

    <table class="table">

        <tr>

            <th>Product</th>

            <th>Seller</th>

            <th>Score</th>

            <th>Trust</th>

            <th>Status</th>

            <th>Date</th>

            <th>Action</th>

        </tr>


        <?php if ($queue): ?>

            <?php foreach ($queue as $l): ?>

                <tr>


                    <!-- Product -->

                    <td>

                        <a
                            href="/listing.php?id=<?= (int)$l['id'] ?>"
                        >

                            <?= e($l['title']) ?>

                        </a>

                    </td>



                    <!-- Seller -->

                    <td>

                        <?= e($l['seller']) ?>

                    </td>



                    <!-- Score -->

                    <td>

                        <?php if (
                            $l['fraud_score'] !== null
                        ): ?>

                            <strong>
                                <?= e(
                                    (string)$l['fraud_score']
                                ) ?>
                            </strong>

                        <?php else: ?>

                            <span class="muted">
                                Not checked
                            </span>

                        <?php endif; ?>

                    </td>



                    <!-- Trust -->

                    <td>

                        <?php if (
                            !empty($l['trust_status'])
                        ): ?>

                            <?= e(
                                trust_label(
                                    $l['trust_status']
                                )
                            ) ?>

                        <?php else: ?>

                            <span class="muted">
                                Not checked
                            </span>

                        <?php endif; ?>

                    </td>



                    <!-- Status -->

                    <td>

                        <?= e(
                            $l['status']
                        ) ?>

                    </td>



                    <!-- Date -->

                    <td>

                        <?= e(
                            $l['created_at']
                        ) ?>

                    </td>



                    <!-- Actions -->

                    <td>

                        <div class="actions">


                            <!-- RUN FRAUD CHECK -->

                            <form
                                method="post"
                                style="margin:0;"
                            >

                                <?= csrf_field() ?>


                                <input
                                    type="hidden"
                                    name="listing_id"
                                    value="<?= (int)$l['id'] ?>"
                                >


                                <button
                                    class="btn btn-sm"
                                    type="submit"
                                    name="action"
                                    value="fraud_check"
                                >

                                    <?= $l['fraud_checked']
                                        ? 'Run Again'
                                        : 'Run Fraud Check'
                                    ?>

                                </button>

                            </form>



                            <!-- APPROVE -->

                            <form
                                method="post"
                                style="margin:0;"
                            >

                                <?= csrf_field() ?>


                                <input
                                    type="hidden"
                                    name="listing_id"
                                    value="<?= (int)$l['id'] ?>"
                                >


                                <button
                                    class="btn btn-sm"
                                    type="submit"
                                    name="action"
                                    value="approve"
                                >

                                    Approve

                                </button>

                            </form>



                            <!-- REJECT -->

                            <form
                                method="post"
                                style="margin:0;"
                            >

                                <?= csrf_field() ?>


                                <input
                                    type="hidden"
                                    name="listing_id"
                                    value="<?= (int)$l['id'] ?>"
                                >


                                <button
                                    class="btn btn-sm btn-light"
                                    type="submit"
                                    name="action"
                                    value="reject"
                                >

                                    Reject

                                </button>

                            </form>


                        </div>

                    </td>

                </tr>

            <?php endforeach; ?>


        <?php else: ?>

            <tr>

                <td
                    colspan="7"
                    style="text-align:center;"
                >

                    No pending listings.

                </td>

            </tr>

        <?php endif; ?>

    </table>

</div>



<?php

include dirname(__DIR__) . '/includes/footer.php';

?>