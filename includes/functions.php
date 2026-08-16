<?php

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| Session
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
        'lifetime' => 60 * 60 * 24 * 30,
        'path' => '/',
        'secure' => (
            !empty($_SERVER['HTTPS']) &&
            $_SERVER['HTTPS'] !== 'off'
        ),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}


require_once dirname(__DIR__) . '/config/database.php';


/*
|--------------------------------------------------------------------------
| General Helpers
|--------------------------------------------------------------------------
*/

function e(?string $value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}


function flash(
    string $type,
    string $message
): void {

    $_SESSION['flash_' . $type] = $message;
}


function pull_flash(
    string $type
): ?string {

    $key = 'flash_' . $type;

    $value = $_SESSION[$key] ?? null;

    unset($_SESSION[$key]);

    return $value;
}


function money(
    float|int|string $value
): string {

    return '৳' .
        number_format(
            (float)$value,
            0
        );
}


/*
|--------------------------------------------------------------------------
| Trust
|--------------------------------------------------------------------------
*/

function trust_label(
    string $status
): string {

    return [
        'safe'       => 'Verified',
        'low_risk'   => 'Under Review',
        'suspicious' => 'Suspicious',
        'high_risk'  => 'Flagged'
    ][$status] ?? 'Unchecked';
}


function trust_class(
    string $status
): string {

    return 'trust-' .
        preg_replace(
            '/[^a-z_]/',
            '',
            $status
        );
}


function trust_state(
    string $status
): string {

    return match ($status) {

        'safe' => 'verified',

        'low_risk' => 'review',

        'suspicious',
        'high_risk' => 'danger',

        default => 'review'
    };
}


/*
|--------------------------------------------------------------------------
| Radius Visuals
|--------------------------------------------------------------------------
*/

function radius_visuals(): array
{
    return [

        'phone' => [

            'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=82',

            'https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&w=900&q=82',

            'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?auto=format&fit=crop&w=900&q=82',

            'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?auto=format&fit=crop&w=900&q=82'
        ],

        'laptop' => [

            'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=82',

            'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=900&q=82'
        ],

        'camera' => [

            'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=900&q=82',

            'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?auto=format&fit=crop&w=900&q=82'
        ],

        'furniture' => [

            'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=900&q=82',

            'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=82'
        ],

        'bicycle' => [

            'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=900&q=82',

            'https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?auto=format&fit=crop&w=900&q=82'
        ],

        'appliance' => [

            'https://images.unsplash.com/photo-1585659722983-3a675dabf23d?auto=format&fit=crop&w=900&q=82',

            'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=900&q=82'
        ],

        'fashion' => [

            'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=900&q=82',

            'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=900&q=82'
        ],

        'books' => [

            'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?auto=format&fit=crop&w=900&q=82',

            'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=900&q=82'
        ],

        'gaming' => [

            'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?auto=format&fit=crop&w=900&q=82',

            'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=82'
        ],

        'accessories' => [

            'https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=900&q=82',

            'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=82'
        ]
    ];
}


function stable_visual_index(
    string $seed,
    int $count
): int {

    if ($count < 1) {
        return 0;
    }

    return abs(
        (int)crc32(
            strtolower($seed)
        )
    ) % $count;
}


/*
|--------------------------------------------------------------------------
| Listing Visual
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| Uploaded DB image has priority.
|
| Example:
|
| /uploads/listings/abc123.png
|
| If image_path exists, this function will NEVER use
| the Unsplash fallback.
|
|--------------------------------------------------------------------------
*/

function listing_visual(
    array $listing
): string {

    /*
    |--------------------------------------------------------------------------
    | Uploaded image
    |--------------------------------------------------------------------------
    */

    $uploaded = trim(
        (string)(
            $listing['image_path'] ?? ''
        )
    );


    /*
    |--------------------------------------------------------------------------
    | If database contains uploaded image
    |--------------------------------------------------------------------------
    */

    if ($uploaded !== '') {

        /*
         * Normalize Windows-style path.
         */
        $uploaded = str_replace(
            '\\',
            '/',
            $uploaded
        );


        /*
         * Remove accidental duplicate leading slashes.
         *
         * Example:
         *
         * //uploads/listings/a.png
         *
         * becomes:
         *
         * /uploads/listings/a.png
         */
        $uploaded =
            '/' .
            ltrim(
                $uploaded,
                '/'
            );


        /*
         * Only allow our own upload path.
         *
         * This prevents unexpected external paths.
         */
        if (
            str_starts_with(
                $uploaded,
                '/uploads/listings/'
            )
        ) {

            return $uploaded;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Fallback image
    |--------------------------------------------------------------------------
    */

    $all = radius_visuals();


    $category = strtolower(
        trim(
            (string)(
                $listing['category']
                ?? 'accessories'
            )
        )
    );


    /*
    * Normalize category.
    */

    $category = str_replace(
        [
            '-',
            '_'
        ],
        ' ',
        $category
    );


    /*
    |--------------------------------------------------------------------------
    | Category image
    |--------------------------------------------------------------------------
    */

    $images =
        $all[$category]
        ?? $all['accessories'];


    /*
    |--------------------------------------------------------------------------
    | Stable image selection
    |--------------------------------------------------------------------------
    */

    $seed = strtolower(
        trim(
            (string)(
                $listing['title']
                ?? $listing['id']
                ?? $category
            )
        )
    );


    $index =
        stable_visual_index(
            $seed,
            count($images)
        );


    /*
    |--------------------------------------------------------------------------
    | Phone-specific visual rules
    |--------------------------------------------------------------------------
    */

    if ($category === 'phone') {

        if (
            str_contains(
                $seed,
                'samsung'
            )
        ) {

            $index = min(
                3,
                count($images) - 1
            );

        } elseif (
            str_contains(
                $seed,
                'urgent'
            ) ||
            str_contains(
                $seed,
                'pro'
            )
        ) {

            $index = min(
                2,
                count($images) - 1
            );

        } elseif (
            str_contains(
                $seed,
                'iphone'
            )
        ) {

            $index = 0;
        }
    }


    return $images[$index];
}


/*
|--------------------------------------------------------------------------
| AI HTTP
|--------------------------------------------------------------------------
*/

function ai_json(
    string $path,
    array $payload,
    int $timeout = 12
): ?array {

    if (!function_exists('curl_init')) {
        return null;
    }


    $url =
        rtrim(
            AI_SERVICE_URL,
            '/'
        ) .
        '/' .
        ltrim(
            $path,
            '/'
        );


    $json =
        json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );


    if ($json === false) {
        return null;
    }


    $ch = curl_init($url);


    curl_setopt_array(
        $ch,
        [

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_POST => true,

            CURLOPT_HTTPHEADER => [

                'Content-Type: application/json',

                'Accept: application/json'
            ],

            CURLOPT_POSTFIELDS => $json,

            CURLOPT_CONNECTTIMEOUT => 3,

            CURLOPT_TIMEOUT => $timeout
        ]
    );


    $body = curl_exec($ch);


    $code =
        (int)curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );


    $err = curl_error($ch);


    curl_close($ch);


    if (
        $body === false ||
        $code < 200 ||
        $code >= 300
    ) {

        error_log(
            'AI request failed: ' .
            $err .
            ' HTTP ' .
            $code
        );

        return null;
    }


    $data =
        json_decode(
            $body,
            true
        );


    return is_array($data)
        ? $data
        : null;
}


/*
|--------------------------------------------------------------------------
| AI Image Hash
|--------------------------------------------------------------------------
*/

function ai_hash_image(
    string $absolutePath
): ?string {

    if (
        !function_exists('curl_init') ||
        !is_file($absolutePath)
    ) {
        return null;
    }


    $mime =
        mime_content_type(
            $absolutePath
        ) ?: 'image/jpeg';


    $url =
        rtrim(
            AI_SERVICE_URL,
            '/'
        ) .
        '/hash-image';


    $ch =
        curl_init($url);


    curl_setopt_array(
        $ch,
        [

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_POST => true,

            CURLOPT_POSTFIELDS => [

                'image' =>
                    new CURLFile(
                        $absolutePath,
                        $mime,
                        basename(
                            $absolutePath
                        )
                    )
            ],

            CURLOPT_CONNECTTIMEOUT => 3,

            CURLOPT_TIMEOUT => 12
        ]
    );


    $body =
        curl_exec($ch);


    $code =
        (int)curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );


    $err =
        curl_error($ch);


    curl_close($ch);


    if (
        $body === false ||
        $code < 200 ||
        $code >= 300
    ) {

        error_log(
            'AI image hash request failed: ' .
            $err .
            ' HTTP ' .
            $code
        );

        return null;
    }


    $data =
        json_decode(
            $body,
            true
        );


    if (!is_array($data)) {
        return null;
    }


    $hash =
        $data['image_hash']
        ?? null;


    return is_string($hash)
        ? $hash
        : null;
}


/*
|--------------------------------------------------------------------------
| AI Image Embedding (ML)
|--------------------------------------------------------------------------
*/

function ai_image_embedding(
    string $absolutePath
): ?array {

    if (
        !function_exists('curl_init') ||
        !is_file($absolutePath)
    ) {
        return null;
    }


    $mime =
        mime_content_type(
            $absolutePath
        ) ?: 'image/jpeg';


    $url =
        rtrim(
            AI_SERVICE_URL,
            '/'
        ) .
        '/image-embedding';


    $ch =
        curl_init($url);


    curl_setopt_array(
        $ch,
        [

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_POST => true,

            CURLOPT_POSTFIELDS => [

                'image' =>
                    new CURLFile(
                        $absolutePath,
                        $mime,
                        basename(
                            $absolutePath
                        )
                    )
            ],

            CURLOPT_CONNECTTIMEOUT => 5,

            /*
             * ResNet50 inference is heavier than pHash,
             * give it more time on cold starts.
             */
            CURLOPT_TIMEOUT => 25
        ]
    );


    $body =
        curl_exec($ch);


    $code =
        (int)curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );


    $err =
        curl_error($ch);


    curl_close($ch);


    if (
        $body === false ||
        $code < 200 ||
        $code >= 300
    ) {

        error_log(
            'AI image embedding request failed: ' .
            $err .
            ' HTTP ' .
            $code
        );

        return null;
    }


    $data =
        json_decode(
            $body,
            true
        );


    if (!is_array($data)) {
        return null;
    }


    $embedding =
        $data['embedding']
        ?? null;


    if (
        !is_array($embedding) ||
        $embedding === []
    ) {
        return null;
    }


    return $embedding;
}


/*
|--------------------------------------------------------------------------
| AI Compare Image (ML)
|--------------------------------------------------------------------------
*/

function ai_compare_image(
    array $queryEmbedding,
    array $existingEmbeddings
): ?array {

    if ($queryEmbedding === []) {
        return null;
    }

    $payload = [

        'query_embedding' =>
            $queryEmbedding,

        'existing_embeddings' =>
            $existingEmbeddings
    ];

    return ai_json(
        '/compare-image',
        $payload,
        20
    );
}


/*
|--------------------------------------------------------------------------
| Existing Image Embeddings (for comparison)
|--------------------------------------------------------------------------
*/

function get_existing_image_embeddings(
    int $excludeListingId,
    int $limit = 500
): array {

    $pdo = db();

    $stmt =
        $pdo->prepare(
            "SELECT listing_id, image_embedding
             FROM listing_images
             WHERE listing_id <> ?
             AND image_embedding IS NOT NULL
             AND image_embedding <> ''
             ORDER BY id DESC
             LIMIT ?"
        );

    $stmt->bindValue(1, $excludeListingId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();

    $rows =
        $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );

    $result = [];

    foreach ($rows as $row) {

        $decoded =
            json_decode(
                (string)$row['image_embedding'],
                true
            );

        if (
            !is_array($decoded) ||
            $decoded === []
        ) {
            continue;
        }

        $result[] = [

            'listing_id' =>
                (int)$row['listing_id'],

            'embedding' =>
                $decoded
        ];
    }

    return $result;
}


/*
|--------------------------------------------------------------------------
| Listing Upload Validation
|--------------------------------------------------------------------------
*/

function validate_listing_upload(
    array $file
): array {

    if (
        ($file['error'] ?? UPLOAD_ERR_NO_FILE)
        !== UPLOAD_ERR_OK
    ) {

        throw new RuntimeException(
            'Please upload a product image.'
        );
    }


    $size =
        (int)(
            $file['size'] ?? 0
        );


    if (
        $size <= 0 ||
        $size > MAX_UPLOAD_BYTES
    ) {

        throw new RuntimeException(
            'Image exceeds the upload size limit.'
        );
    }


    $tmp =
        (string)(
            $file['tmp_name']
            ?? ''
        );


    if (
        $tmp === '' ||
        !is_uploaded_file($tmp)
    ) {

        throw new RuntimeException(
            'Invalid uploaded image.'
        );
    }


    $finfo =
        new finfo(
            FILEINFO_MIME_TYPE
        );


    $mime =
        $finfo->file($tmp);


    $allowed = [

        'image/jpeg' =>
            'jpg',

        'image/png' =>
            'png',

        'image/webp' =>
            'webp'
    ];


    if (
        !isset(
            $allowed[$mime]
        )
    ) {

        throw new RuntimeException(
            'Only valid JPG, PNG, or WEBP images are allowed.'
        );
    }


    if (
        @getimagesize($tmp) === false
    ) {

        throw new RuntimeException(
            'Uploaded file is not a valid image.'
        );
    }


    $originalExt =
        strtolower(
            pathinfo(
                (string)(
                    $file['name']
                    ?? ''
                ),
                PATHINFO_EXTENSION
            )
        );


    if (
        !in_array(
            $originalExt,
            [
                'jpg',
                'jpeg',
                'png',
                'webp'
            ],
            true
        )
    ) {

        throw new RuntimeException(
            'Invalid image extension.'
        );
    }


    return [
        $mime,
        $allowed[$mime]
    ];
}


/*
|--------------------------------------------------------------------------
| Save Listing Upload
|--------------------------------------------------------------------------
*/

function save_listing_upload(
    array $file
): array {

    [
        $mime,
        $ext
    ] = validate_listing_upload(
        $file
    );


    if (
        !is_dir(
            UPLOAD_LISTING_DIR
        )
    ) {

        if (
            !mkdir(
                UPLOAD_LISTING_DIR,
                0755,
                true
            ) &&
            !is_dir(
                UPLOAD_LISTING_DIR
            )
        ) {

            throw new RuntimeException(
                'Could not create upload directory.'
            );
        }
    }


    $name =
        bin2hex(
            random_bytes(18)
        ) .
        '.' .
        $ext;


    $dest =
        rtrim(
            UPLOAD_LISTING_DIR,
            '/\\'
        ) .
        DIRECTORY_SEPARATOR .
        $name;


    if (
        !move_uploaded_file(
            $file['tmp_name'],
            $dest
        )
    ) {

        throw new RuntimeException(
            'Could not save uploaded image.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Make sure file really exists
    |--------------------------------------------------------------------------
    */

    if (
        !is_file($dest)
    ) {

        throw new RuntimeException(
            'Uploaded image was not saved correctly.'
        );
    }


    return [

        '/uploads/listings/' .
        $name,

        $dest,

        $mime
    ];
}


/*
|--------------------------------------------------------------------------
| Seller Risk Context
|--------------------------------------------------------------------------
*/

function seller_risk_context(
    int $sellerId
): array {

    $pdo = db();


    /*
    |--------------------------------------------------------------------------
    | Account age
    |--------------------------------------------------------------------------
    */

    $stmt =
        $pdo->prepare(
            "SELECT DATEDIFF(
                NOW(),
                created_at
            )
            FROM users
            WHERE id=?"
        );


    $stmt->execute([
        $sellerId
    ]);


    $age =
        (int)(
            $stmt->fetchColumn()
            ?: 0
        );


    /*
    |--------------------------------------------------------------------------
    | Previous listings
    |--------------------------------------------------------------------------
    */

    $q =
        $pdo->prepare(
            "SELECT COUNT(*)
             FROM listings
             WHERE user_id=?"
        );


    $q->execute([
        $sellerId
    ]);


    $listings =
        (int)$q->fetchColumn();


    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    $q =
        $pdo->prepare(
            "SELECT COUNT(*)
             FROM reports r
             JOIN listings l
               ON l.id = r.listing_id
             WHERE l.user_id=?"
        );


    $q->execute([
        $sellerId
    ]);


    $reports =
        (int)$q->fetchColumn();


    /*
    |--------------------------------------------------------------------------
    | Completed trades
    |--------------------------------------------------------------------------
    */

    $q =
        $pdo->prepare(
            "SELECT COUNT(*)
             FROM trade_requests
             WHERE seller_id=?
             AND status='completed'"
        );


    $q->execute([
        $sellerId
    ]);


    $completed =
        (int)$q->fetchColumn();


    /*
    |--------------------------------------------------------------------------
    | Removed listings
    |--------------------------------------------------------------------------
    */

    $q =
        $pdo->prepare(
            "SELECT COUNT(*)
             FROM listings
             WHERE user_id=?
             AND status='removed'"
        );


    $q->execute([
        $sellerId
    ]);


    $removed =
        (int)$q->fetchColumn();


    /*
    |--------------------------------------------------------------------------
    | Suspicious listings
    |--------------------------------------------------------------------------
    */

    $q =
        $pdo->prepare(
            "SELECT COUNT(*)
             FROM listings
             WHERE user_id=?
             AND trust_status IN
             ('suspicious','high_risk')"
        );


    $q->execute([
        $sellerId
    ]);


    $suspicious =
        (int)$q->fetchColumn();


    /*
    |--------------------------------------------------------------------------
    | Average rating
    |--------------------------------------------------------------------------
    */

    $q =
        $pdo->prepare(
            "SELECT COALESCE(
                AVG(rating),
                0
            )
            FROM reviews
            WHERE reviewed_user_id=?"
        );


    $q->execute([
        $sellerId
    ]);


    $rating =
        (float)$q->fetchColumn();


    return [

        'account_age_days' =>
            $age,

        'previous_listings' =>
            $listings,

        'report_count' =>
            $reports,

        'completed_trades' =>
            $completed,

        'removed_listings' =>
            $removed,

        'suspicious_listings' =>
            $suspicious,

        'rating_average' =>
            $rating
    ];
}


/*
|--------------------------------------------------------------------------
| Fraud Analysis
|--------------------------------------------------------------------------
*/

function run_fraud_analysis(
    int $listingId
): ?array {

    $pdo = db();


    /*
    |--------------------------------------------------------------------------
    | Get listing
    |--------------------------------------------------------------------------
    */

    $stmt =
        $pdo->prepare(
            "SELECT *
             FROM listings
             WHERE id=?"
        );


    $stmt->execute([
        $listingId
    ]);


    $listing =
        $stmt->fetch(
            PDO::FETCH_ASSOC
        );


    if (!$listing) {
        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Preserve original price
    |--------------------------------------------------------------------------
    */

    $originalPrice =
        (float)$listing['price'];


    /*
    |--------------------------------------------------------------------------
    | Own image hashes
    |--------------------------------------------------------------------------
    */

    $h =
        $pdo->prepare(
            "SELECT image_hash
             FROM listing_images
             WHERE listing_id=?
             AND image_hash IS NOT NULL
             AND image_hash <> ''"
        );


    $h->execute([
        $listingId
    ]);


    $own =
        $h->fetchAll(
            PDO::FETCH_COLUMN
        );


    /*
    |--------------------------------------------------------------------------
    | Existing image hashes
    |--------------------------------------------------------------------------
    */

    $all =
        $pdo->prepare(
            "SELECT li.image_hash
             FROM listing_images li
             WHERE li.listing_id<>?
             AND li.image_hash IS NOT NULL
             AND li.image_hash <> ''
             LIMIT 1000"
        );


    $all->execute([
        $listingId
    ]);


    $existingHashes =
        $all->fetchAll(
            PDO::FETCH_COLUMN
        );


    /*
    |--------------------------------------------------------------------------
    | Existing descriptions
    |--------------------------------------------------------------------------
    */

    $d =
        $pdo->prepare(
            "SELECT description
             FROM listings
             WHERE id<>?
             AND description IS NOT NULL
             AND description <> ''
             ORDER BY id DESC
             LIMIT 200"
        );


    $d->execute([
        $listingId
    ]);


    $existingDescriptions =
        $d->fetchAll(
            PDO::FETCH_COLUMN
        );


    /*
    |--------------------------------------------------------------------------
    | AI Payload
    |--------------------------------------------------------------------------
    */

    $payload = [

        'title' =>
            (string)$listing['title'],

        'description' =>
            (string)$listing['description'],

        'category' =>
            (string)$listing['category'],

        'brand' =>
            (string)$listing['brand'],

        'condition' =>
            (string)$listing['item_condition'],

        /*
         * IMPORTANT:
         * Original seller price.
         */
        'price' =>
            $originalPrice,

        'seller_information' =>
            seller_risk_context(
                (int)$listing['user_id']
            ),

        'image_hashes' =>
            $own,

        'existing_image_hashes' =>
            $existingHashes,

        'existing_descriptions' =>
            $existingDescriptions
    ];


    /*
    |--------------------------------------------------------------------------
    | Call AI (pHash + price + seller + text + policy)
    |--------------------------------------------------------------------------
    */

    $result =
        ai_json(
            '/analyze-listing',
            $payload,
            20
        );


    if (!$result) {
        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | ML Image Similarity (ResNet50) — blend with pHash image_score
    |--------------------------------------------------------------------------
    */

    $mlImageScore = null;

    $ownEmbeddingStmt =
        $pdo->prepare(
            "SELECT image_embedding
             FROM listing_images
             WHERE listing_id = ?
             AND image_embedding IS NOT NULL
             AND image_embedding <> ''
             LIMIT 1"
        );

    $ownEmbeddingStmt->execute([
        $listingId
    ]);

    $ownEmbeddingRaw =
        $ownEmbeddingStmt->fetchColumn();

    if ($ownEmbeddingRaw) {

        $decodedOwn =
            json_decode(
                (string)$ownEmbeddingRaw,
                true
            );

        if (
            is_array($decodedOwn) &&
            $decodedOwn !== []
        ) {

            $existingEmbeddings =
                get_existing_image_embeddings(
                    $listingId
                );

            $compareResult =
                ai_compare_image(
                    $decodedOwn,
                    $existingEmbeddings
                );

            $bestMatch =
                $compareResult['best_match']
                ?? null;

            if (
                is_array($bestMatch) &&
                isset($bestMatch['risk_score'])
            ) {

                $mlImageScore =
                    max(
                        0.0,
                        min(
                            100.0,
                            (float)$bestMatch['risk_score']
                        )
                    );
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Trust status
    |--------------------------------------------------------------------------
    */

    $trustStatus =
        strtolower(
            trim(
                (string)(
                    $result['trust_status']
                    ?? 'low_risk'
                )
            )
        );


    $allowedStatuses = [

        'safe',

        'low_risk',

        'suspicious',

        'high_risk'
    ];


    if (
        !in_array(
            $trustStatus,
            $allowedStatuses,
            true
        )
    ) {

        $trustStatus =
            'low_risk';
    }


    /*
    |--------------------------------------------------------------------------
    | Fraud score (from Python, pHash-based)
    |--------------------------------------------------------------------------
    */

    $fraudScore =
        (float)(
            $result['fraud_score']
            ?? 0
        );


    if (
        !is_finite(
            $fraudScore
        )
    ) {

        $fraudScore = 0;
    }


    $fraudScore =
        max(
            0,
            min(
                100,
                $fraudScore
            )
        );


    /*
    |--------------------------------------------------------------------------
    | Component scores
    |--------------------------------------------------------------------------
    */

    $imageScore =
        (float)(
            $result['image_score']
            ?? 0
        );


    $priceScore =
        (float)(
            $result['price_score']
            ?? 0
        );


    $sellerScore =
        (float)(
            $result['seller_score']
            ?? 0
        );


    $textScore =
        (float)(
            $result['text_score']
            ?? 0
        );


    $policyScore =
        (float)(
            $result['policy_score']
            ?? 0
        );


    /*
    |--------------------------------------------------------------------------
    | Normalize scores
    |--------------------------------------------------------------------------
    */

    $imageScore =
        max(
            0,
            min(
                100,
                $imageScore
            )
        );


    $priceScore =
        max(
            0,
            min(
                100,
                $priceScore
            )
        );


    $sellerScore =
        max(
            0,
            min(
                100,
                $sellerScore
            )
        );


    $textScore =
        max(
            0,
            min(
                100,
                $textScore
            )
        );


    $policyScore =
        max(
            0,
            min(
                100,
                $policyScore
            )
        );


    /*
    |--------------------------------------------------------------------------
    | Blend pHash image_score with ML image_score (if available)
    |--------------------------------------------------------------------------
    |
    | 50% pHash + 50% ResNet50 ML similarity.
    | If ML score is unavailable (AI service down, no embedding yet),
    | fall back to pHash-only score — nothing breaks.
    |
    */

    if ($mlImageScore !== null) {

        $imageScore =
            ($imageScore * 0.5) +
            ($mlImageScore * 0.5);

        $imageScore =
            max(
                0,
                min(
                    100,
                    $imageScore
                )
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Recompute fraud score if ML blending changed the image score
    |--------------------------------------------------------------------------
    */

    if ($mlImageScore !== null) {

        $fraudScore = round(
            ($imageScore * 0.25) +
            ($priceScore * 0.25) +
            ($sellerScore * 0.20) +
            ($textScore * 0.20) +
            ($policyScore * 0.10),
            2
        );

        if ($fraudScore < 30) {
            $trustStatus = 'safe';
        } elseif ($fraudScore < 50) {
            $trustStatus = 'low_risk';
        } elseif ($fraudScore < 70) {
            $trustStatus = 'suspicious';
        } else {
            $trustStatus = 'high_risk';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Current status
    |--------------------------------------------------------------------------
    */

    $currentStatus =
        (string)(
            $listing['status']
            ?? 'pending'
        );


    /*
    |--------------------------------------------------------------------------
    | Feature snapshot
    |--------------------------------------------------------------------------
    */

    $featureSnapshot =
        $result['feature_snapshot']
        ?? $payload;


    if (
        !is_array(
            $featureSnapshot
        )
    ) {

        $featureSnapshot =
            $payload;
    }


    /*
    |--------------------------------------------------------------------------
    | Price protection
    |--------------------------------------------------------------------------
    */

    $featureSnapshot[
        'original_listing_price'
    ] = $originalPrice;


    $featureSnapshot[
        'price_was_modified_by_ai'
    ] = false;


    $featureSnapshot[
        'price_score_is_risk_only'
    ] = true;


    $featureSnapshot[
        'ml_image_score'
    ] = $mlImageScore;


    $featureSnapshotJson =
        json_encode(
            $featureSnapshot,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );


    if (
        $featureSnapshotJson === false
    ) {

        $featureSnapshotJson =
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Explanation
    |--------------------------------------------------------------------------
    */

    $explanation =
        (string)(
            $result['explanation']
            ?? 'No explanation provided.'
        );


    if ($mlImageScore !== null) {

        $explanation .=
            ' ML-based visual similarity score: ' .
            number_format(
                $mlImageScore,
                2
            ) .
            '/100.';
    }


    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    */

    $modelName =
        (string)(
            $result['model_name']
            ?? 'radius_explainable_ensemble'
        );


    $modelVersion =
        (string)(
            $result['model_version']
            ?? '1.0'
        );


    if ($mlImageScore !== null) {
        $modelVersion .= '+resnet50';
    }


    /*
    |--------------------------------------------------------------------------
    | Save analysis
    |--------------------------------------------------------------------------
    */

    $pdo->beginTransaction();


    try {

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | Price is NOT updated.
        | Status is NOT updated.
        |--------------------------------------------------------------------------
        */

        $u =
            $pdo->prepare(
                "UPDATE listings
                 SET fraud_score=?,
                     trust_status=?,
                     fraud_checked=1,
                     updated_at=NOW()
                 WHERE id=?"
            );


        $u->execute([

            $fraudScore,

            $trustStatus,

            $listingId
        ]);


        /*
        |--------------------------------------------------------------------------
        | Insert prediction
        |--------------------------------------------------------------------------
        */

        $ins =
            $pdo->prepare(
                "INSERT INTO fraud_predictions
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
                    ?
                )"
            );


        $ins->execute([

            $listingId,

            $fraudScore,

            $imageScore,

            $priceScore,

            $sellerScore,

            $textScore,

            $policyScore,

            $modelName,

            $modelVersion,

            $explanation,

            $featureSnapshotJson
        ]);


        /*
        |--------------------------------------------------------------------------
        | Safety check
        |--------------------------------------------------------------------------
        */

        $check =
            $pdo->prepare(
                "SELECT price
                 FROM listings
                 WHERE id=?"
            );


        $check->execute([
            $listingId
        ]);


        $savedPrice =
            (float)$check->fetchColumn();


        if (
            abs(
                $savedPrice -
                $originalPrice
            ) > 0.00001
        ) {

            throw new RuntimeException(
                'Safety check failed: listing price was modified unexpectedly.'
            );
        }


        $pdo->commit();

    } catch (Throwable $e) {

        if (
            $pdo->inTransaction()
        ) {

            $pdo->rollBack();
        }

        throw $e;
    }


    /*
    |--------------------------------------------------------------------------
    | Return analysis
    |--------------------------------------------------------------------------
    */

    return [

        'trust_status' =>
            $trustStatus,

        'fraud_score' =>
            $fraudScore,

        'image_score' =>
            $imageScore,

        'ml_image_score' =>
            $mlImageScore,

        'price_score' =>
            $priceScore,

        'seller_score' =>
            $sellerScore,

        'text_score' =>
            $textScore,

        'policy_score' =>
            $policyScore,

        'model_name' =>
            $modelName,

        'model_version' =>
            $modelVersion,

        'explanation' =>
            $explanation,

        'feature_snapshot' =>
            $featureSnapshot,

        'original_price' =>
            $originalPrice,

        'price_modified' =>
            false,

        'listing_status' =>
            $currentStatus
    ];
}


/*
|--------------------------------------------------------------------------
| Haversine SQL
|--------------------------------------------------------------------------
*/

function haversine_sql(
    string $lat = 'l.latitude',
    string $lng = 'l.longitude'
): string {

    return "(6371 * ACOS(
        LEAST(
            1,
            COS(RADIANS(:ulat1))
            * COS(RADIANS($lat))
            * COS(
                RADIANS($lng)
                - RADIANS(:ulng)
            )
            + SIN(RADIANS(:ulat2))
            * SIN(RADIANS($lat))
        )
    ))";
}