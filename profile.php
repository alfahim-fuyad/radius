<?php

require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/functions.php';
require_once __DIR__.'/includes/csrf.php';

$u = require_login();

$pdo = db();


/*
|--------------------------------------------------------------------------
| Determine which profile to show
|--------------------------------------------------------------------------
|
| /profile.php
|       -> Logged-in user's profile
|
| /profile.php?id=5
|       -> User ID 5's profile
|
*/

$profileId = isset($_GET['id']) && ctype_digit((string)$_GET['id'])
    ? (int)$_GET['id']
    : (int)$u['id'];


/*
|--------------------------------------------------------------------------
| Get Profile User
|--------------------------------------------------------------------------
*/

$q = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
    LIMIT 1
");

$q->execute([$profileId]);

$profileUser = $q->fetch();


/*
|--------------------------------------------------------------------------
| User Not Found
|--------------------------------------------------------------------------
*/

if (!$profileUser) {

    http_response_code(404);

    $pageTitle = 'Profile Not Found';

    include __DIR__.'/includes/header.php';
    ?>

    <div class="section-head">

        <div>

            <h2>Profile not found</h2>

            <p>
                The user profile you're looking for does not exist.
            </p>

        </div>

    </div>

    <?php

    include __DIR__.'/includes/footer.php';

    exit;
}


/*
|--------------------------------------------------------------------------
| Is This My Profile?
|--------------------------------------------------------------------------
*/

$isOwnProfile = (
    (int)$profileUser['id'] === (int)$u['id']
);


/*
|--------------------------------------------------------------------------
| Completed Trades
|--------------------------------------------------------------------------
*/

$q = $pdo->prepare("
    SELECT COUNT(*)
    FROM trade_requests
    WHERE (buyer_id = ? OR seller_id = ?)
      AND status = 'completed'
");

$q->execute([
    $profileUser['id'],
    $profileUser['id']
]);

$trades = (int)$q->fetchColumn();


/*
|--------------------------------------------------------------------------
| Average Rating
|--------------------------------------------------------------------------
*/

$q = $pdo->prepare("
    SELECT COALESCE(AVG(rating), 0)
    FROM reviews
    WHERE reviewed_user_id = ?
");

$q->execute([
    $profileUser['id']
]);

$rating = (float)$q->fetchColumn();


/*
|--------------------------------------------------------------------------
| User Listings
|--------------------------------------------------------------------------
*/

$q = $pdo->prepare("
    SELECT
        *,
        (
            SELECT image_path
            FROM listing_images
            WHERE listing_id = listings.id
            ORDER BY id
            LIMIT 1
        ) AS image_path
    FROM listings
    WHERE user_id = ?
      AND status <> 'removed'
    ORDER BY created_at DESC
");

$q->execute([
    $profileUser['id']
]);

$own = $q->fetchAll();


/*
|--------------------------------------------------------------------------
| Reviews
|--------------------------------------------------------------------------
*/

$r = $pdo->prepare("
    SELECT
        r.*,
        u.name AS reviewer
    FROM reviews r
    JOIN users u
        ON u.id = r.reviewer_id
    WHERE r.reviewed_user_id = ?
    ORDER BY r.created_at DESC
");

$r->execute([
    $profileUser['id']
]);

$reviews = $r->fetchAll();


$pageTitle = $profileUser['name'].' · Profile';

include __DIR__.'/includes/header.php';

?>


<div class="section-head">

    <div>

        <h2>
            <?=e($profileUser['name'])?>
        </h2>

        <p>
            <?=e($profileUser['location'] ?: 'Location not set')?>
            ·
            Member since
            <?=date('M Y', strtotime($profileUser['created_at']))?>
        </p>

    </div>


    <?php if(!$isOwnProfile): ?>

        <div class="actions">

            <a
                class="btn btn-primary btn-sm"
                href="/messages.php?user_id=<?=$profileUser['id']?>"
            >
                Chat
            </a>

        </div>

    <?php endif; ?>

</div>


<div class="stats">


    <!-- Rating -->

    <div class="stat">

        <span class="muted">
            Rating
        </span>

        <strong>
            <?=number_format($rating, 1)?>
        </strong>

    </div>


    <!-- Completed Trades -->

    <div class="stat">

        <span class="muted">
            Completed trades
        </span>

        <strong>
            <?=$trades?>
        </strong>

    </div>


    <!-- Active Listings -->

    <div class="stat">

        <span class="muted">
            Active listings
        </span>

        <strong>
            <?=count($own)?>
        </strong>

    </div>


    <!-- Reviews -->

    <div class="stat">

        <span class="muted">
            Reviews
        </span>

        <strong>
            <?=count($reviews)?>
        </strong>

    </div>


</div>


<div class="section-head">

    <div>

        <h2>
            <?= $isOwnProfile ? 'Your listings' : 'Listings' ?>
        </h2>

    </div>


    <?php if($isOwnProfile): ?>

        <a
            class="btn btn-sm"
            href="/create-listing.php"
        >
            Sell item
        </a>

    <?php endif; ?>


</div>


<div class="grid">


    <?php foreach($own as $l): ?>


        <div class="card">


            <!-- Product image / View listing -->

            <a
                href="/listing.php?id=<?=$l['id']?>"
                style="text-decoration:none;color:inherit"
            >

                <div class="product-img placeholder">

                    <?php if($l['image_path']): ?>

                        <img
                            class="product-img"
                            src="<?=e($l['image_path'])?>"
                            alt="<?=e($l['title'])?>"
                        >

                    <?php else: ?>

                        RADIUS

                    <?php endif; ?>

                </div>


                <div class="card-body">


                    <span
                        class="badge <?=e(
                            trust_class(
                                $l['trust_status'] ?? ''
                            )
                        )?>"
                    >

                        <?=e(
                            trust_label(
                                $l['trust_status'] ?? ''
                            )
                        )?>

                    </span>


                    <h3>
                        <?=e($l['title'])?>
                    </h3>


                    <strong>
                        <?=money($l['price'])?>
                    </strong>


                    <div class="muted">

                        Status:
                        <?=e($l['status'])?>

                    </div>


                </div>

            </a>


            <!-- Edit / Delete -->

            <?php if($isOwnProfile): ?>

                <div
                    class="actions"
                    style="
                        padding:0 16px 16px;
                        display:flex;
                        gap:8px;
                    "
                >


                    <!-- Edit -->

                    <a
                        class="btn btn-sm"
                        href="/edit-listing.php?id=<?=$l['id']?>"
                    >
                        Edit
                    </a>


                    <!-- Delete -->

                    <form
                        method="post"
                        action="/delete-listing.php"
                        style="display:inline"
                        onsubmit="return confirm('Are you sure you want to delete this listing?');"
                    >

                        <?=csrf_field()?>

                        <input
                            type="hidden"
                            name="id"
                            value="<?=$l['id']?>"
                        >

                        <button
                            type="submit"
                            class="btn btn-sm"
                        >
                            Delete
                        </button>

                    </form>


                </div>

            <?php endif; ?>


        </div>


    <?php endforeach; ?>


</div>


<?php if(!$own): ?>

    <div class="empty">
        No listings yet.
    </div>

<?php endif; ?>


<div class="section-head">

    <h2>
        Reviews
    </h2>

</div>


<?php foreach($reviews as $rv): ?>


    <div
        class="card card-body"
        style="margin-bottom:10px"
    >

        <strong>

            <?=e($rv['reviewer'])?>

            ·

            <?=$rv['rating']?>/5

        </strong>


        <p>
            <?=e($rv['comment'])?>
        </p>

    </div>


<?php endforeach; ?>


<?php if(!$reviews): ?>

    <div class="empty">
        No reviews yet.
    </div>

<?php endif; ?>


<?php include __DIR__.'/includes/footer.php'; ?>