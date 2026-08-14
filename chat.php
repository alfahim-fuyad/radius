<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$u = require_login();

$pdo = db();

$id = (int)(
    $_GET['conversation_id'] ?? 0
);


/* =========================================================
   GET CONVERSATION
========================================================= */

if ($u['role'] === 'admin') {


    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    | Admin can access any conversation.
    */

    $s = $pdo->prepare("
        SELECT

            c.*,

            l.title,

            buyer.name AS buyer_name,

            seller.name AS seller_name

        FROM conversations c

        LEFT JOIN listings l
            ON l.id = c.listing_id

        JOIN users buyer
            ON buyer.id = c.buyer_id

        JOIN users seller
            ON seller.id = c.seller_id

        WHERE c.id = ?

        LIMIT 1
    ");

    $s->execute([
        $id
    ]);

} else {


    /*
    |--------------------------------------------------------------------------
    | NORMAL USER
    |--------------------------------------------------------------------------
    | Only buyer/seller can access conversation.
    */

    $s = $pdo->prepare("
        SELECT

            c.*,

            l.title,

            buyer.name AS buyer_name,

            seller.name AS seller_name

        FROM conversations c

        LEFT JOIN listings l
            ON l.id = c.listing_id

        JOIN users buyer
            ON buyer.id = c.buyer_id

        JOIN users seller
            ON seller.id = c.seller_id

        WHERE c.id = ?

        AND (
            c.buyer_id = ?
            OR c.seller_id = ?
        )

        LIMIT 1
    ");

    $s->execute([
        $id,
        $u['id'],
        $u['id']
    ]);
}


$c = $s->fetch();


if (!$c) {

    http_response_code(403);

    exit(
        'Conversation not available.'
    );
}


/* =========================================================
   MARK MESSAGES AS READ
========================================================= */

$pdo->prepare("
    UPDATE messages

    SET is_read = 1

    WHERE conversation_id = ?

    AND sender_id <> ?
")->execute([
    $id,
    $u['id']
]);


/* =========================================================
   PAGE
========================================================= */

$pageTitle = 'Chat';

include __DIR__ . '/includes/header.php';

?>


<div class="section-head">

    <div>

        <h2>

            <?php if ($c['listing_id']): ?>

                <?= e($c['title']) ?>

            <?php else: ?>

                Admin Support

            <?php endif; ?>

        </h2>


        <p>

            Chat with

            <?php

            if ((int)$c['buyer_id'] === (int)$u['id']) {

                echo e($c['seller_name']);

            } else {

                echo e($c['buyer_name']);

            }

            ?>

        </p>

    </div>

</div>


<div class="chat">


    <div
        class="chat-log"
        data-chat-log
        data-conversation="<?= (int)$id ?>"
    ></div>


    <form
        class="chat-form"
        method="post"
        action="/api/chat.php"
    >

        <?= csrf_field() ?>


        <input
            type="hidden"
            name="action"
            value="send"
        >


        <input
            type="hidden"
            name="conversation_id"
            value="<?= (int)$id ?>"
        >


        <input
            name="message"
            maxlength="2000"
            required
            placeholder="Write a message"
        >


        <button
            class="btn"
            type="submit"
        >
            Send
        </button>

    </form>

</div>


<?php include __DIR__ . '/includes/footer.php'; ?>