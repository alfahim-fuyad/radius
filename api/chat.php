<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/csrf.php';

$u = require_login();

$pdo = db();


/* =========================================================
   GET MESSAGES
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $id = (int)(
        $_GET['conversation_id'] ?? 0
    );


    /* ---------------------------------------------------------
       ADMIN
    --------------------------------------------------------- */

    if ($u['role'] === 'admin') {

        $c = $pdo->prepare("
            SELECT id

            FROM conversations

            WHERE id = ?

            LIMIT 1
        ");

        $c->execute([
            $id
        ]);

    }


    /* ---------------------------------------------------------
       NORMAL USER
    --------------------------------------------------------- */

    else {

        $c = $pdo->prepare("
            SELECT id

            FROM conversations

            WHERE id = ?

            AND (
                buyer_id = ?
                OR seller_id = ?
            )

            LIMIT 1
        ");

        $c->execute([
            $id,
            $u['id'],
            $u['id']
        ]);
    }


    if (!$c->fetch()) {

        http_response_code(403);

        exit;
    }


    /* ---------------------------------------------------------
       GET MESSAGES
    --------------------------------------------------------- */

    $s = $pdo->prepare("
        SELECT

            m.*,

            u.name AS sender_name

        FROM messages m

        JOIN users u
            ON u.id = m.sender_id

        WHERE m.conversation_id = ?

        ORDER BY m.id ASC
    ");

    $s->execute([
        $id
    ]);

    $items = $s->fetchAll();


    foreach ($items as &$m) {

        $m['mine'] =
            (int)$m['sender_id'] ===
            (int)$u['id'];
    }

    unset($m);


    header(
        'Content-Type: application/json'
    );


    echo json_encode([
        'items' => $items
    ]);

    exit;
}


/* =========================================================
   POST
========================================================= */

verify_csrf();


$action =
    $_POST['action'] ?? '';


/* =========================================================
   START NORMAL LISTING CHAT
========================================================= */

if ($action === 'start') {


    $listing =
        (int)($_POST['listing_id'] ?? 0);


    $s = $pdo->prepare("
        SELECT

            id,

            user_id

        FROM listings

        WHERE id = ?

        AND status = 'approved'

        LIMIT 1
    ");

    $s->execute([
        $listing
    ]);

    $l = $s->fetch();


    /*
    |--------------------------------------------------------------------------
    | Cannot chat own listing
    |--------------------------------------------------------------------------
    */

    if (
        !$l ||
        (int)$l['user_id'] ===
        (int)$u['id']
    ) {

        flash(
            'error',
            'Chat is unavailable for this listing.'
        );

        redirect(
            '/listing.php?id=' .
            $listing
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Find existing conversation
    |--------------------------------------------------------------------------
    */

    $q = $pdo->prepare("
        SELECT id

        FROM conversations

        WHERE listing_id = ?

        AND buyer_id = ?

        AND seller_id = ?

        LIMIT 1
    ");

    $q->execute([
        $listing,
        $u['id'],
        $l['user_id']
    ]);


    $conversationId =
        $q->fetchColumn();


    /*
    |--------------------------------------------------------------------------
    | Create conversation
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
                ?,
                ?,
                ?
            )
        ");

        $create->execute([
            $listing,
            $u['id'],
            $l['user_id']
        ]);

        $conversationId =
            $pdo->lastInsertId();
    }


    redirect(
        '/chat.php?conversation_id=' .
        (int)$conversationId
    );
}


/* =========================================================
   SEND MESSAGE
========================================================= */

if ($action === 'send') {


    $id =
        (int)(
            $_POST['conversation_id'] ?? 0
        );


    $msg =
        trim(
            $_POST['message'] ?? ''
        );


    if ($msg === '') {

        flash(
            'error',
            'Message cannot be empty.'
        );

        redirect(
            '/chat.php?conversation_id=' .
            $id
        );
    }


    /* ---------------------------------------------------------
       ADMIN CAN SEND TO ANY CONVERSATION
    --------------------------------------------------------- */

    if ($u['role'] === 'admin') {

        $c = $pdo->prepare("
            SELECT id

            FROM conversations

            WHERE id = ?

            LIMIT 1
        ");

        $c->execute([
            $id
        ]);
    }


    /* ---------------------------------------------------------
       NORMAL USER
    --------------------------------------------------------- */

    else {

        $c = $pdo->prepare("
            SELECT id

            FROM conversations

            WHERE id = ?

            AND (
                buyer_id = ?
                OR seller_id = ?
            )

            LIMIT 1
        ");

        $c->execute([
            $id,
            $u['id'],
            $u['id']
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Permission failed
    |--------------------------------------------------------------------------
    */

    if (!$c->fetch()) {

        http_response_code(403);

        exit(
            'You cannot send messages in this conversation.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Insert message
    |--------------------------------------------------------------------------
    */

    $insert = $pdo->prepare("
        INSERT INTO messages
        (
            conversation_id,
            sender_id,
            message
        )

        VALUES
        (
            ?,
            ?,
            ?
        )
    ");

    $insert->execute([
        $id,
        $u['id'],
        $msg
    ]);


    redirect(
        '/chat.php?conversation_id=' .
        $id
    );
}


/* =========================================================
   INVALID ACTION
========================================================= */

http_response_code(400);

exit(
    'Invalid chat action.'
);