<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

$user = require_login();

$pdo = null;
$uploadedAbsolutePath = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        /*
        |--------------------------------------------------------------------------
        | CSRF
        |--------------------------------------------------------------------------
        */

        verify_csrf();


        /*
        |--------------------------------------------------------------------------
        | Read input
        |--------------------------------------------------------------------------
        */

        $title = trim(
            (string)($_POST['title'] ?? '')
        );

        $description = trim(
            (string)($_POST['description'] ?? '')
        );

        $category = strtolower(
            trim(
                (string)($_POST['category'] ?? '')
            )
        );

        $brand = trim(
            (string)($_POST['brand'] ?? '')
        );

        $condition = strtolower(
            trim(
                (string)($_POST['condition'] ?? '')
            )
        );

        $priceRaw = trim(
            (string)($_POST['price'] ?? '')
        );

        $location = trim(
            (string)($_POST['location'] ?? '')
        );

        $latitudeRaw = trim(
            (string)($_POST['latitude'] ?? '')
        );

        $longitudeRaw = trim(
            (string)($_POST['longitude'] ?? '')
        );


        /*
        |--------------------------------------------------------------------------
        | Basic validation
        |--------------------------------------------------------------------------
        */

        if (
            mb_strlen($title) < 3 ||
            mb_strlen($title) > 180
        ) {
            throw new RuntimeException(
                'Title must be between 3 and 180 characters.'
            );
        }


        if (
            mb_strlen($description) < 10
        ) {
            throw new RuntimeException(
                'Description must contain at least 10 characters.'
            );
        }


        if ($category === '') {
            throw new RuntimeException(
                'Please select a category.'
            );
        }


        if (
            mb_strlen($category) > 100
        ) {
            throw new RuntimeException(
                'Category is too long.'
            );
        }


        if (
            mb_strlen($brand) > 100
        ) {
            throw new RuntimeException(
                'Brand is too long.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Condition
        |--------------------------------------------------------------------------
        */

        $allowedConditions = [
            'new',
            'excellent',
            'good',
            'fair',
            'poor'
        ];

        if (
            !in_array(
                $condition,
                $allowedConditions,
                true
            )
        ) {
            throw new RuntimeException(
                'Invalid item condition.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Price
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | This is the ORIGINAL seller-entered price.
        | AI analysis NEVER modifies this value.
        |
        */

        if (
            $priceRaw === '' ||
            !is_numeric($priceRaw)
        ) {
            throw new RuntimeException(
                'Please enter a valid price.'
            );
        }

        $price = (float)$priceRaw;


        if (!is_finite($price)) {
            throw new RuntimeException(
                'Please enter a valid price.'
            );
        }


        if ($price <= 0) {
            throw new RuntimeException(
                'Please enter a valid price.'
            );
        }


        if ($price > 100000000) {
            throw new RuntimeException(
                'Price is too high.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Location
        |--------------------------------------------------------------------------
        */

        if ($location === '') {
            throw new RuntimeException(
                'Please enter the item location.'
            );
        }


        if (
            mb_strlen($location) > 255
        ) {
            throw new RuntimeException(
                'Location is too long.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Latitude
        |--------------------------------------------------------------------------
        */

        $latitude = null;

        if ($latitudeRaw !== '') {

            if (
                !is_numeric($latitudeRaw)
            ) {
                throw new RuntimeException(
                    'Invalid latitude.'
                );
            }

            $latitudeValue = (float)$latitudeRaw;

            if (
                !is_finite($latitudeValue) ||
                $latitudeValue < -90 ||
                $latitudeValue > 90
            ) {
                throw new RuntimeException(
                    'Latitude must be between -90 and 90.'
                );
            }

            $latitude = $latitudeValue;
        }


        /*
        |--------------------------------------------------------------------------
        | Longitude
        |--------------------------------------------------------------------------
        */

        $longitude = null;

        if ($longitudeRaw !== '') {

            if (
                !is_numeric($longitudeRaw)
            ) {
                throw new RuntimeException(
                    'Invalid longitude.'
                );
            }

            $longitudeValue = (float)$longitudeRaw;

            if (
                !is_finite($longitudeValue) ||
                $longitudeValue < -180 ||
                $longitudeValue > 180
            ) {
                throw new RuntimeException(
                    'Longitude must be between -180 and 180.'
                );
            }

            $longitude = $longitudeValue;
        }


        /*
        |--------------------------------------------------------------------------
        | Image validation
        |--------------------------------------------------------------------------
        */

        $image = $_FILES['image'] ?? null;

        if (
            !is_array($image)
        ) {
            throw new RuntimeException(
                'Please upload a product image.'
            );
        }


        validate_listing_upload($image);


        /*
        |--------------------------------------------------------------------------
        | Save image
        |--------------------------------------------------------------------------
        */

        [
            $imagePath,
            $uploadedAbsolutePath,
            $imageMime
        ] = save_listing_upload($image);


        /*
        |--------------------------------------------------------------------------
        | Database
        |--------------------------------------------------------------------------
        */

        $pdo = db();

        $pdo->beginTransaction();


        /*
        |--------------------------------------------------------------------------
        | Create listing
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | status is ALWAYS pending here.
        |
        | AI cannot directly approve/remove the listing.
        |
        */

        $stmt = $pdo->prepare(
            "INSERT INTO listings
            (
                user_id,
                title,
                description,
                category,
                brand,
                item_condition,
                price,
                location,
                latitude,
                longitude,
                status,
                fraud_checked
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                'pending',
                0
            )"
        );


        $stmt->execute([
            (int)$user['id'],
            $title,
            $description,
            $category,
            $brand,
            $condition,

            /*
             * ORIGINAL PRICE
             *
             * Never replace this with AI output.
             */
            $price,

            $location,
            $latitude,
            $longitude
        ]);


        /*
        |--------------------------------------------------------------------------
        | Listing ID
        |--------------------------------------------------------------------------
        */

        $listingId = (int)$pdo->lastInsertId();

        if ($listingId <= 0) {
            throw new RuntimeException(
                'Could not create listing.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Image hash (pHash)
        |--------------------------------------------------------------------------
        */

        $imageHash = null;

        if (
            $uploadedAbsolutePath !== null &&
            is_file($uploadedAbsolutePath)
        ) {

            try {

                $imageHash = ai_hash_image(
                    $uploadedAbsolutePath
                );

            } catch (Throwable $hashException) {

                error_log(
                    'Image hash generation failed for listing #' .
                    $listingId .
                    ': ' .
                    $hashException->getMessage()
                );

                $imageHash = null;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ML image embedding (ResNet50)
        |--------------------------------------------------------------------------
        */

        $imageEmbedding = null;

        if (
            $uploadedAbsolutePath !== null &&
            is_file($uploadedAbsolutePath)
        ) {

            try {

                $imageEmbedding = ai_image_embedding(
                    $uploadedAbsolutePath
                );

            } catch (Throwable $embeddingException) {

                error_log(
                    'Image embedding generation failed for listing #' .
                    $listingId .
                    ': ' .
                    $embeddingException->getMessage()
                );

                $imageEmbedding = null;
            }
        }


        $imageEmbeddingJson =
            $imageEmbedding !== null
                ? json_encode(
                    $imageEmbedding,
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
                )
                : null;


        /*
        |--------------------------------------------------------------------------
        | Insert image
        |--------------------------------------------------------------------------
        */

        $imageStmt = $pdo->prepare(
            "INSERT INTO listing_images
            (
                listing_id,
                image_path,
                image_hash,
                image_embedding
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )"
        );


        $imageStmt->execute([
            $listingId,
            $imagePath,
            $imageHash,
            $imageEmbeddingJson
        ]);


        /*
        |--------------------------------------------------------------------------
        | Commit listing
        |--------------------------------------------------------------------------
        */

        $pdo->commit();


        /*
        |--------------------------------------------------------------------------
        | AI Fraud Analysis
        |--------------------------------------------------------------------------
        |
        | AI runs AFTER listing creation.
        |
        | IMPORTANT:
        | AI analysis can update risk fields only.
        |
        | It cannot modify:
        | - title
        | - description
        | - price
        | - category
        | - brand
        | - condition
        | - location
        |
        */

        $analysis = null;

        try {

            $analysis = run_fraud_analysis(
                $listingId
            );

        } catch (Throwable $aiException) {

            error_log(
                'Fraud analysis exception for listing #' .
                $listingId .
                ': ' .
                $aiException->getMessage()
            );

            $analysis = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Result
        |--------------------------------------------------------------------------
        */

        if (!$analysis) {

            flash(
                'success',
                'Listing created successfully. Fraud analysis is temporarily unavailable. Your listing is pending review.'
            );

        } else {

            $status = (string)(
                $analysis['trust_status']
                ?? 'low_risk'
            );

            $fraudScore = (float)(
                $analysis['fraud_score']
                ?? 0
            );


            /*
            |--------------------------------------------------------------------------
            | IMPORTANT
            |--------------------------------------------------------------------------
            |
            | The product price shown to the user remains the exact
            | price entered during listing creation.
            |
            */

            flash(
                'success',
                'Listing created successfully. Trust analysis: ' .
                trust_label($status) .
                ' (' .
                number_format(
                    $fraudScore,
                    2
                ) .
                '/100). Your original price remains unchanged.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        redirect(
            '/listing.php?id=' .
            $listingId
        );

    } catch (Throwable $e) {

        /*
        |--------------------------------------------------------------------------
        | Rollback
        |--------------------------------------------------------------------------
        */

        if (
            $pdo instanceof PDO &&
            $pdo->inTransaction()
        ) {
            $pdo->rollBack();
        }


        /*
        |--------------------------------------------------------------------------
        | Delete uploaded file if DB operation failed
        |--------------------------------------------------------------------------
        */

        if (
            $uploadedAbsolutePath !== null &&
            is_file($uploadedAbsolutePath)
        ) {

            @unlink(
                $uploadedAbsolutePath
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Log unexpected errors
        |--------------------------------------------------------------------------
        */

        if (
            !($e instanceof RuntimeException)
        ) {

            error_log(
                'Create listing error: ' .
                $e->getMessage()
            );
        }


        /*
        |--------------------------------------------------------------------------
        | User message
        |--------------------------------------------------------------------------
        */

        $message =
            $e instanceof RuntimeException
                ? $e->getMessage()
                : 'Could not create listing. Please try again.';

        flash(
            'error',
            $message
        );
    }
}


/*
|--------------------------------------------------------------------------
| Page
|--------------------------------------------------------------------------
*/

$pageTitle = 'Sell an Item';

include __DIR__ . '/includes/header.php';

?>

<form
    class="form-card"
    method="post"
    enctype="multipart/form-data"
>

    <h1>Create listing</h1>

    <p class="muted">
        Your listing is checked by RADIUS after submission.
        Risk signals support moderation; they are not proof of fraud.
    </p>


    <?= csrf_field() ?>


    <div class="form-grid">


        <!-- TITLE -->

        <div class="field full">

            <label for="title">
                Title *
            </label>

            <input
                id="title"
                type="text"
                name="title"
                maxlength="180"
                required
                value="<?= e(
                    (string)(
                        $_POST['title'] ?? ''
                    )
                ) ?>"
            >

        </div>


        <!-- DESCRIPTION -->

        <div class="field full">

            <label for="description">
                Description *
            </label>

            <textarea
                id="description"
                name="description"
                required
            ><?= e(
                (string)(
                    $_POST['description'] ?? ''
                )
            ) ?></textarea>

        </div>


        <!-- CATEGORY -->

        <div class="field">

            <label for="category">
                Category *
            </label>

            <input
                id="category"
                type="text"
                name="category"
                required
                maxlength="100"
                placeholder="phone, laptop, furniture"
                value="<?= e(
                    (string)(
                        $_POST['category'] ?? ''
                    )
                ) ?>"
            >

        </div>


        <!-- BRAND -->

        <div class="field">

            <label for="brand">
                Brand
            </label>

            <input
                id="brand"
                type="text"
                name="brand"
                maxlength="100"
                value="<?= e(
                    (string)(
                        $_POST['brand'] ?? ''
                    )
                ) ?>"
            >

        </div>


        <!-- CONDITION -->

        <div class="field">

            <label for="condition">
                Condition *
            </label>

            <select
                id="condition"
                name="condition"
                required
            >

                <?php

                $selectedCondition =
                    strtolower(
                        trim(
                            (string)(
                                $_POST['condition']
                                ?? 'new'
                            )
                        )
                    );

                foreach (
                    [
                        'new',
                        'excellent',
                        'good',
                        'fair',
                        'poor'
                    ] as $value
                ):

                ?>

                    <option
                        value="<?= e($value) ?>"
                        <?= $selectedCondition === $value
                            ? 'selected'
                            : '' ?>
                    >
                        <?= e(
                            ucfirst($value)
                        ) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- PRICE -->

        <div class="field">

            <label for="price">
                Price (BDT) *
            </label>

            <input
                id="price"
                type="number"
                name="price"
                min="1"
                max="100000000"
                step="1"
                required
                value="<?= e(
                    (string)(
                        $_POST['price'] ?? ''
                    )
                ) ?>"
            >

            <small class="muted">
                This is your original selling price. AI risk analysis will not change it.
            </small>

        </div>


        <!-- LOCATION -->

        <div class="field full">

            <label for="location">
                Location *
            </label>

            <input
                id="location"
                type="text"
                name="location"
                maxlength="255"
                required
                placeholder="e.g. Mirpur, Dhaka"
                value="<?= e(
                    (string)(
                        $_POST['location'] ?? ''
                    )
                ) ?>"
            >

        </div>


        <!-- LATITUDE -->

        <div class="field">

            <label for="latitude">
                Latitude
            </label>

            <input
                id="latitude"
                type="text"
                name="latitude"
                inputmode="decimal"
                placeholder="23.8103"
                value="<?= e(
                    (string)(
                        $_POST['latitude'] ?? ''
                    )
                ) ?>"
            >

        </div>


        <!-- LONGITUDE -->

        <div class="field">

            <label for="longitude">
                Longitude
            </label>

            <input
                id="longitude"
                type="text"
                name="longitude"
                inputmode="decimal"
                placeholder="90.4125"
                value="<?= e(
                    (string)(
                        $_POST['longitude'] ?? ''
                    )
                ) ?>"
            >

        </div>


        <!-- IMAGE -->

        <div class="field full">

            <label for="image">
                Product image *
            </label>

            <input
                id="image"
                type="file"
                name="image"
                accept="image/jpeg,image/png,image/webp"
                required
            >

            <small class="muted">
                JPG, PNG or WEBP only.
            </small>

        </div>

    </div>


    <div class="actions">

        <button
            type="button"
            class="btn btn-light"
            data-use-location
        >
            Use my location
        </button>

        <button
            type="submit"
            class="btn"
        >
            Create &amp; Analyze
        </button>

    </div>

</form>


<?php

include __DIR__ . '/includes/footer.php';

?>