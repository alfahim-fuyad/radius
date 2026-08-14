<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$u = require_login();

$pdo = db();


/* =========================================================
   OPEN CHAT WITH SPECIFIC USER
========================================================= */

$targetUserId =
    isset($_GET['user_id']) &&
    ctype_digit((string)$_GET['user_id'])
        ? (int)$_GET['user_id']
        : 0;


/*
|--------------------------------------------------------------------------
| ADMIN CHAT
|--------------------------------------------------------------------------
|
| Admin can message ANY user.
|
| Admin conversation:
|
| listing_id = NULL
|
*/

if (
    $u['role'] === 'admin' &&
    $targetUserId > 0 &&
    $targetUserId !== (int)$u['id']
) {


    /* Check target user */

    $target = $pdo->prepare("
        SELECT id, name
        FROM users
        WHERE id = ?
          AND is_active = 1
        LIMIT 1
    ");

    $target->execute([
        $targetUserId
    ]);

    $targetUser = $target->fetch();


    if (!$targetUser) {

        flash(
            'error',
            'User not found.'
        );

        redirect('/admin/users.php');
    }


    /*
    |--------------------------------------------------------------------------
    | Find existing ADMIN conversation
    |--------------------------------------------------------------------------
    |
    | Admin is stored as buyer_id.
    | Target user is seller_id.
    |
    | listing_id = NULL
    |
    */

    $find = $pdo->prepare("
        SELECT id

        FROM conversations

        WHERE listing_id IS NULL

          AND buyer_id = ?

          AND seller_id = ?

        ORDER BY id DESC

        LIMIT 1
    ");

    $find->execute([
        $u['id'],
        $targetUserId
    ]);

    $conversationId = $find->fetchColumn();


    /*
    |--------------------------------------------------------------------------
    | Create admin conversation
    |--------------------------------------------------------------------------
    */

    if (!$conversationId) {

        $create = $pdo->prepare("
            INSERT INTO conversations
            (
                listing_id,
                buyer_id,
                seller_id
            )

            VALUES
            (
                NULL,
                ?,
                ?
            )
        ");

        $create->execute([
            $u['id'],
            $targetUserId
        ]);

        $conversationId = $pdo->lastInsertId();
    }


    /*
    |--------------------------------------------------------------------------
    | Open chat
    |--------------------------------------------------------------------------
    */

    header(
        'Location: /chat.php?conversation_id=' .
        (int)$conversationId
    );

    exit;
}


/* =========================================================
   NORMAL USER → USER CHAT
========================================================= */

/*
|--------------------------------------------------------------------------
| Normal users still use the old listing-based logic.
|--------------------------------------------------------------------------
*/

if (
    $targetUserId > 0 &&
    $targetUserId !== (int)$u['id']
) {


    $find = $pdo->prepare("
        SELECT id

        FROM conversations

        WHERE
        (
            buyer_id = ?
            AND seller_id = ?
        )

        OR

        (
            buyer_id = ?
            AND seller_id = ?
        )

        ORDER BY created_at DESC

        LIMIT 1
    ");

    $find->execute([
        $u['id'],
        $targetUserId,

        $targetUserId,
        $u['id']
    ]);

    $conversationId =
        $find->fetchColumn();


    if ($conversationId) {

        header(
            'Location: /chat.php?conversation_id=' .
            (int)$conversationId
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Normal user cannot create chat without listing.
    |--------------------------------------------------------------------------
    */

    header(
        'Location: /profile.php?id=' .
        $targetUserId .
        '&chat=unavailable'
    );

    exit;
}


/* =========================================================
   GET CONVERSATIONS
========================================================= */

if ($u['role'] === 'admin') {


    /*
    |--------------------------------------------------------------------------
    | ADMIN CONVERSATIONS
    |--------------------------------------------------------------------------
    |
    | Admin sees all conversations.
    |
    */

    $s = $pdo->prepare("
        SELECT

            c.id,

            c.listing_id,

            c.buyer_id,

            c.seller_id,

            c.created_at,

            l.title,

            CASE

                WHEN c.buyer_id = ?

                THEN seller.name

                ELSE buyer.name

            END AS other_name,

            (
                SELECT message

                FROM messages

                WHERE conversation_id = c.id

                ORDER BY id DESC

                LIMIT 1

            ) AS last_message,

            (
                SELECT created_at

                FROM messages

                WHERE conversation_id = c.id

                ORDER BY id DESC

                LIMIT 1

            ) AS last_at,

            (
                SELECT COUNT(*)

                FROM messages

                WHERE conversation_id = c.id

                AND sender_id <> ?

                AND is_read = 0

            ) AS unread

        FROM conversations c

        LEFT JOIN listings l
            ON l.id = c.listing_id

        JOIN users buyer
            ON buyer.id = c.buyer_id

        JOIN users seller
            ON seller.id = c.seller_id

        WHERE
            c.buyer_id = ?
            OR c.seller_id = ?

        ORDER BY
            COALESCE(
                last_at,
                c.created_at
            ) DESC
    ");


    $s->execute([
        $u['id'],
        $u['id'],
        $u['id'],
        $u['id']
    ]);

} else {


    /*
    |--------------------------------------------------------------------------
    | NORMAL USER CONVERSATIONS
    |--------------------------------------------------------------------------
    */

    $s = $pdo->prepare("
        SELECT

            c.*,

            l.title,

            l.id AS listing_id,

            CASE

                WHEN c.buyer_id = ?

                THEN seller.name

                ELSE buyer.name

            END AS other_name,

            (
                SELECT message

                FROM messages

                WHERE conversation_id = c.id

                ORDER BY id DESC

                LIMIT 1

            ) AS last_message,

            (
                SELECT created_at

                FROM messages

                WHERE conversation_id = c.id

                ORDER BY id DESC

                LIMIT 1

            ) AS last_at,

            (
                SELECT COUNT(*)

                FROM messages

                WHERE conversation_id = c.id

                AND sender_id <> ?

                AND is_read = 0

            ) AS unread

        FROM conversations c

        LEFT JOIN listings l
            ON l.id = c.listing_id

        JOIN users buyer
            ON buyer.id = c.buyer_id

        JOIN users seller
            ON seller.id = c.seller_id

        WHERE
            c.buyer_id = ?
            OR c.seller_id = ?

        ORDER BY
            COALESCE(
                last_at,
                c.created_at
            ) DESC
    ");


    $s->execute([
        $u['id'],
        $u['id'],
        $u['id'],
        $u['id']
    ]);
}


$items = $s->fetchAll();


$pageTitle = 'Messages';

include __DIR__ . '/includes/header.php';

?>


<div class="section-head">

    <div>

        <h2>Messages</h2>

        <p>

            <?php if ($u['role'] === 'admin'): ?>

                Admin conversations with users.

            <?php else: ?>

                Your buyer-seller conversations.

            <?php endif; ?>

        </p>

    </div>

</div>


<?php if (
    isset($_GET['chat']) &&
    $_GET['chat'] === 'unavailable'
): ?>

    <div class="toast-container">

        <div class="toast toast-error">

            Start a chat from one of the user's listings.

        </div>

    </div>

<?php endif; ?>


<div class="stack">


    <?php foreach ($items as $c): ?>

        <a
            class="card card-body"
            href="/chat.php?conversation_id=<?= (int)$c['id'] ?>"
        >

            <strong>

                <?= e($c['other_name']) ?>

                <?php if ($c['listing_id']): ?>

                    · <?= e($c['title']) ?>

                <?php else: ?>

                    · Admin Support

                <?php endif; ?>

            </strong>


            <div class="muted">

                <?= e(
                    $c['last_message']
                    ?: 'Start the conversation'
                ) ?>


                <?php if ($c['unread']): ?>

                    · <?= (int)$c['unread'] ?> unread

                <?php endif; ?>

            </div>

        </a>

    <?php endforeach; ?>


</div>


<?php if (!$items): ?>

    <div class="empty">

        No conversations yet.

    </div>

<?php endif; ?>


<?php include __DIR__ . '/includes/footer.php'; ?>