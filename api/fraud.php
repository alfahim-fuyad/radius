<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/csrf.php';


require_admin();

verify_csrf();

$pdo = db();

$id = (int)($_POST['listing_id'] ?? 0);

$action = $_POST['action'] ?? '';


/* =========================================================
   CHECK LISTING
========================================================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM listings
    WHERE id = ?
");

$stmt->execute([$id]);

$listing = $stmt->fetch();


if (!$listing) {

    flash(
        'error',
        'Listing not found.'
    );

    redirect('/admin/fraud_queue.php');
}


/* =========================================================
   RETRY FRAUD ANALYSIS
========================================================= */

if ($action === 'retry') {

    $result = run_fraud_analysis($id);


    if ($result) {

        flash(
            'success',
            'Fraud analysis refreshed successfully.'
        );

    } else {

        flash(
            'error',
            'Fraud analysis failed. Make sure the AI service is running.'
        );
    }


    redirect(
        '/admin/fraud_queue.php'
    );
}


/* =========================================================
   APPROVE
========================================================= */

if ($action === 'approve') {

    $stmt = $pdo->prepare("
        UPDATE listings

        SET
            status = 'approved',
            updated_at = NOW()

        WHERE id = ?
    ");

    $stmt->execute([$id]);


    flash(
        'success',
        'Listing approved successfully.'
    );


    redirect(
        '/admin/fraud_queue.php'
    );
}


/* =========================================================
   REMOVE
========================================================= */

if ($action === 'remove') {

    $stmt = $pdo->prepare("
        UPDATE listings

        SET
            status = 'removed',
            updated_at = NOW()

        WHERE id = ?
    ");

    $stmt->execute([$id]);


    flash(
        'success',
        'Listing removed successfully.'
    );


    redirect(
        '/admin/fraud_queue.php'
    );
}


/* =========================================================
   INVALID ACTION
========================================================= */

http_response_code(400);

echo 'Invalid action.';