<?php

require_once __DIR__ . '/config/database.php';

$pdo = db();

/*
|--------------------------------------------------------------------------
| Demo Password
|--------------------------------------------------------------------------
*/

$password = password_hash('fuad1234@', PASSWORD_DEFAULT);


/*
|--------------------------------------------------------------------------
| Demo Users
|--------------------------------------------------------------------------
*/

$users = [

    [
        'RADIUS Admin',
        'fuad@gmail.com',
        'admin',
        'Dhaka',
        23.8103,
        90.4125
    ],

    [
        'Demo Seller',
        'seller@radius.test',
        'user',
        'Badda, Dhaka',
        23.7806,
        90.4267
    ],

    [
        'Demo Buyer',
        'buyer@radius.test',
        'user',
        'Aftabnagar, Dhaka',
        23.7639,
        90.4291
    ],

    [
        'Nadia Rahman',
        'nadia@radius.test',
        'user',
        'Rampura, Dhaka',
        23.7612,
        90.4208
    ]

];


/*
|--------------------------------------------------------------------------
| Insert / Update Users
|--------------------------------------------------------------------------
|
| Existing user's password is NOT overwritten.
|
*/

foreach ($users as $u) {

    $s = $pdo->prepare(
        'INSERT INTO users
        (
            name,
            email,
            password_hash,
            role,
            location,
            latitude,
            longitude
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)

        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            role = VALUES(role),
            location = VALUES(location),
            latitude = VALUES(latitude),
            longitude = VALUES(longitude),
            is_active = 1'
    );

    $s->execute([
        $u[0],
        $u[1],
        $password,
        $u[2],
        $u[3],
        $u[4],
        $u[5]
    ]);
}


/*
|--------------------------------------------------------------------------
| Get Demo Seller ID
|--------------------------------------------------------------------------
*/

$seller = (int) $pdo
    ->query(
        "SELECT id
         FROM users
         WHERE email = 'seller@radius.test'
         LIMIT 1"
    )
    ->fetchColumn();

if (!$seller) {
    die("Demo seller user could not be found.\n");
}


/*
|--------------------------------------------------------------------------
| Demo Listings
|--------------------------------------------------------------------------
*/

$count = (int) $pdo
    ->query('SELECT COUNT(*) FROM listings')
    ->fetchColumn();


if ($count < 20) {

    $titles = [

        'iPhone 13 128GB',
        'Dell Latitude 5420',
        'Canon EOS 200D',
        'Study desk solid wood',
        'Giant commuter bicycle',
        'Samsung 32L microwave',
        'Sony PlayStation 5',
        'Mechanical keyboard',
        'University CSE book bundle',
        'Office chair ergonomic',
        'Samsung Galaxy S22',
        'HP EliteBook 840',
        'Sony mirrorless camera',
        'Dining table 4 chair',
        'Mountain bike 27.5',
        'LG refrigerator',
        'Xbox Series S',
        'AirPods Pro 2',
        'Winter jacket',
        '27 inch monitor'

    ];


    $cats = [

        'phone',
        'laptop',
        'camera',
        'furniture',
        'bicycle',
        'appliance',
        'gaming',
        'accessories',
        'books',
        'furniture',
        'phone',
        'laptop',
        'camera',
        'furniture',
        'bicycle',
        'appliance',
        'gaming',
        'accessories',
        'fashion',
        'accessories'

    ];


    $trustStatuses = [

        'safe',
        'low_risk',
        'safe',
        'safe',
        'low_risk',
        'safe',
        'suspicious',
        'safe',
        'safe',
        'safe',
        'safe',
        'low_risk',
        'safe',
        'safe',
        'safe',
        'safe',
        'high_risk',
        'safe',
        'safe',
        'safe'

    ];


    $scores = [

        8,
        32,
        12,
        5,
        35,
        19,
        58,
        10,
        3,
        7,
        14,
        38,
        22,
        9,
        17,
        11,
        78,
        16,
        6,
        12

    ];


    /*
    |--------------------------------------------------------------------------
    | Prepare Listing Insert
    |--------------------------------------------------------------------------
    */

    $s = $pdo->prepare(
        'INSERT INTO listings
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
            fraud_score,
            trust_status,
            fraud_checked
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)'
    );


    /*
    |--------------------------------------------------------------------------
    | Insert Listings
    |--------------------------------------------------------------------------
    */

    foreach ($titles as $i => $title) {

        $price = 1200 + (($i + 1) * 3200);

        $trust = $trustStatuses[$i];

        $score = $scores[$i];


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        $status = in_array(
            $trust,
            ['suspicious', 'high_risk'],
            true
        )
            ? 'flagged'
            : 'approved';


        /*
        |--------------------------------------------------------------------------
        | Brand
        |--------------------------------------------------------------------------
        */

        $brand = in_array(
            $cats[$i],
            [
                'phone',
                'laptop',
                'camera',
                'appliance',
                'gaming'
            ],
            true
        )
            ? 'Samsung'
            : 'generic';


        /*
        |--------------------------------------------------------------------------
        | Coordinates
        |--------------------------------------------------------------------------
        */

        $latitude =
            23.76 + (($i % 5) * 0.006);

        $longitude =
            90.40 + (($i % 6) * 0.006);


        $s->execute([

            $seller,

            $title,

            'Demo secondhand listing. Inspect the item before purchase and use the RADIUS trade flow.',

            $cats[$i],

            $brand,

            'good',

            $price,

            'Dhaka',

            $latitude,

            $longitude,

            $status,

            $score,

            $trust

        ]);
    }
}


/*
|--------------------------------------------------------------------------
| Success Message
|--------------------------------------------------------------------------
*/

echo "========================================\n";
echo " RADIUS DEMO DATA SEEDED SUCCESSFULLY\n";
echo "========================================\n\n";

echo "Admin Login\n";
echo "Email    : fuad@gmail.com\n";
echo "Password : fuad1234@\n";
echo "Role     : admin\n\n";

echo "Demo Seller\n";
echo "Email    : seller@radius.test\n";
echo "Password : fuad1234@\n\n";

echo "Demo Buyer\n";
echo "Email    : buyer@radius.test\n";
echo "Password : fuad1234@\n\n";

echo "Nadia\n";
echo "Email    : nadia@radius.test\n";
echo "Password : fuad1234@\n\n";

echo "Demo listings are ready.\n";