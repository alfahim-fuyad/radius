<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';


/*
|--------------------------------------------------------------------------
| Require Login
|--------------------------------------------------------------------------
*/

$u = require_login();


/*
|--------------------------------------------------------------------------
| Only POST requests are allowed
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    exit('Method Not Allowed.');
}


/*
|--------------------------------------------------------------------------
| CSRF Protection
|--------------------------------------------------------------------------
*/

verify_csrf();


/*
|--------------------------------------------------------------------------
| Get Listing ID
|--------------------------------------------------------------------------
*/

$id = (int)($_POST['id'] ?? 0);


if ($id <= 0) {

    flash(
        'error',
        'Invalid listing.'
    );

    redirect('/profile.php');
}


$pdo = db();


/*
|--------------------------------------------------------------------------
| Verify Ownership
|--------------------------------------------------------------------------
|
| IMPORTANT:
| A user can delete ONLY their own listing.
|
*/

$stmt = $pdo->prepare("
    SELECT id
    FROM listings
    WHERE id = ?
      AND user_id = ?
      AND status <> 'removed'
    LIMIT 1
");

$stmt->execute([
    $id,
    $u['id']
]);

$listing = $stmt->fetch();


if (!$listing) {

    http_response_code(403);

    exit('You cannot delete this listing.');
}


/*
|--------------------------------------------------------------------------
| Soft Delete
|--------------------------------------------------------------------------
|
| We do NOT permanently delete the database row.
|
| Instead:
|
| status = 'removed'
|
| This protects:
| - fraud history
| - reports
| - trade history
| - listing images
| - AI predictions
|
*/

$stmt = $pdo->prepare("
    UPDATE listings
    SET
        status = 'removed',
        updated_at = NOW()
    WHERE id = ?
      AND user_id = ?
");

$stmt->execute([
    $id,
    $u['id']
]);


/*
|--------------------------------------------------------------------------
| Success Message
|--------------------------------------------------------------------------
*/

flash(
    'success',
    'Listing deleted successfully.'
);


/*
|--------------------------------------------------------------------------
| Return to Profile
|--------------------------------------------------------------------------
*/

redirect('/profile.php');