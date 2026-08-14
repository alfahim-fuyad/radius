<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/csrf.php';

require_admin();

$pdo = db();


/* =========================================================
   GET FRAUD QUEUE
========================================================= */

$sql = "
    SELECT
        l.*,
        u.name AS seller,

        (
            SELECT fp.explanation
            FROM fraud_predictions fp
            WHERE fp.listing_id = l.id
            ORDER BY fp.id DESC
            LIMIT 1
        ) AS explanation,

        (
            SELECT fp.model_name
            FROM fraud_predictions fp
            WHERE fp.listing_id = l.id
            ORDER BY fp.id DESC
            LIMIT 1
        ) AS model_name

    FROM listings l

    JOIN users u
        ON u.id = l.user_id

    WHERE
        l.status IN ('pending', 'flagged')
        OR l.trust_status IN ('suspicious', 'high_risk')

    ORDER BY
        COALESCE(l.fraud_score, -1) DESC,
        l.created_at DESC
";

$items = $pdo->query($sql)->fetchAll();


$pageTitle = 'Fraud Queue';

include dirname(__DIR__) . '/includes/header.php';

?>


<div class="section-head">

    <div>

        <h2>Fraud Queue</h2>

        <p>
            Review explainable fraud-risk scores.
            Admin makes the final decision.
        </p>

    </div>

</div>


<div class="table-wrap">

    <table class="table">

        <tr>

            <th>Product</th>
            <th>Seller</th>
            <th>Score</th>
            <th>Trust</th>
            <th>Status</th>
            <th>Main Risk</th>
            <th>Actions</th>

        </tr>


        <?php if ($items): ?>

            <?php foreach ($items as $l): ?>

                <tr>

                    <!-- PRODUCT -->

                    <td>

                        <a
                            href="/listing.php?id=<?= (int)$l['id'] ?>"
                        >
                            <?= e($l['title']) ?>
                        </a>

                    </td>


                    <!-- SELLER -->

                    <td>

                        <a
                            href="/profile.php?id=<?= (int)$l['user_id'] ?>"
                        >
                            <?= e($l['seller']) ?>
                        </a>

                    </td>


                    <!-- FRAUD SCORE -->

                    <td>

                        <?php if ($l['fraud_score'] !== null): ?>

                            <strong>
                                <?= e((string)$l['fraud_score']) ?>
                            </strong>

                            / 100

                        <?php else: ?>

                            <span class="muted">
                                Not checked
                            </span>

                        <?php endif; ?>

                    </td>


                    <!-- TRUST -->

                    <td>

                        <?php if ($l['trust_status']): ?>

                            <span
                                class="badge <?= e(
                                    trust_class($l['trust_status'])
                                ) ?>"
                            >
                                <?= e(
                                    trust_label($l['trust_status'])
                                ) ?>
                            </span>

                        <?php else: ?>

                            <span class="muted">
                                Not checked
                            </span>

                        <?php endif; ?>

                    </td>


                    <!-- STATUS -->

                    <td>

                        <?= e($l['status']) ?>

                    </td>


                    <!-- EXPLANATION -->

                    <td>

                        <?php if ($l['explanation']): ?>

                            <?= e(
                                mb_strimwidth(
                                    (string)$l['explanation'],
                                    0,
                                    120,
                                    '…'
                                )
                            ) ?>

                        <?php else: ?>

                            <span class="muted">
                                No analysis available.
                            </span>

                        <?php endif; ?>

                    </td>


                    <!-- ACTIONS -->

                    <td>

                        <div class="actions">


                            <!-- VIEW -->

                            <a
                                class="btn btn-sm btn-light"
                                href="/listing.php?id=<?= (int)$l['id'] ?>"
                            >
                                View
                            </a>


                            <!-- RETRY FRAUD ANALYSIS -->

                            <form
                                method="post"
                                action="/api/fraud.php"
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
                                    value="retry"
                                >
                                    Recheck
                                </button>

                            </form>


                            <!-- APPROVE -->

                            <form
                                method="post"
                                action="/api/fraud.php"
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


                            <!-- REMOVE -->

                            <form
                                method="post"
                                action="/api/fraud.php"
                                style="margin:0;"
                            >

                                <?= csrf_field() ?>

                                <input
                                    type="hidden"
                                    name="listing_id"
                                    value="<?= (int)$l['id'] ?>"
                                >

                                <button
                                    class="btn btn-sm btn-danger"
                                    type="submit"
                                    name="action"
                                    value="remove"
                                >
                                    Remove
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
                    No listings in fraud queue.
                </td>

            </tr>

        <?php endif; ?>

    </table>

</div>


<?php

include dirname(__DIR__) . '/includes/footer.php';

?>