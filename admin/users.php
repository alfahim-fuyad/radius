<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/csrf.php';

$admin = require_admin();
$pdo = db();


/* =========================================================
   ENABLE / DISABLE USER
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $id = (int)($_POST['user_id'] ?? 0);

    /*
     * Admin নিজের account disable করতে পারবে না.
     */

    if ($id !== (int)$admin['id']) {

        $pdo->prepare(
            'UPDATE users SET is_active = 1 - is_active WHERE id = ?'
        )->execute([$id]);

        flash('success', 'User status updated.');

        redirect('/admin/users.php');
    }
}


/* =========================================================
   GET USERS
========================================================= */

$items = $pdo->query("
    SELECT
        u.*,

        (
            SELECT COUNT(*)
            FROM listings
            WHERE user_id = u.id
        ) AS listing_count,

        (
            SELECT COALESCE(AVG(rating), 0)
            FROM reviews
            WHERE reviewed_user_id = u.id
        ) AS rating

    FROM users u

    ORDER BY u.created_at DESC
")->fetchAll();


$pageTitle = 'Users';

include dirname(__DIR__) . '/includes/header.php';

?>


<div class="section-head">

    <div>

        <h2>Manage users</h2>

        <p>
            Manage users, view profiles, and contact users.
        </p>

    </div>

</div>


<div class="table-wrap">

    <table class="table">

        <tr>

            <th>Name</th>

            <th>Email</th>

            <th>Role</th>

            <th>Listings</th>

            <th>Rating</th>

            <th>Status</th>

            <th>Action</th>

        </tr>


        <?php foreach ($items as $x): ?>

            <tr>


                <!-- USER NAME -->

                <td>

                    <a
                        href="/profile.php?id=<?= (int)$x['id'] ?>"
                    >
                        <?= e($x['name']) ?>
                    </a>

                </td>


                <!-- EMAIL -->

                <td>

                    <?= e($x['email']) ?>

                </td>


                <!-- ROLE -->

                <td>

                    <?= e($x['role']) ?>

                </td>


                <!-- LISTINGS -->

                <td>

                    <?= (int)$x['listing_count'] ?>

                </td>


                <!-- RATING -->

                <td>

                    <?= number_format(
                        (float)$x['rating'],
                        1
                    ) ?>

                </td>


                <!-- STATUS -->

                <td>

                    <?= $x['is_active']
                        ? 'Active'
                        : 'Disabled'
                    ?>

                </td>


                <!-- ACTION -->

                <td>

                    <div class="actions">


                        <?php if (
                            (int)$x['id'] !== (int)$admin['id']
                        ): ?>


                            <!-- CHAT -->

                            <a
                                class="btn btn-sm"
                                href="/messages.php?user_id=<?= (int)$x['id'] ?>"
                            >
                                Chat
                            </a>


                            <!-- ENABLE / DISABLE -->

                            <form
                                method="post"
                                style="margin:0;"
                            >

                                <?= csrf_field() ?>

                                <input
                                    type="hidden"
                                    name="user_id"
                                    value="<?= (int)$x['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-sm <?= $x['is_active']
                                        ? 'btn-danger'
                                        : 'btn-light'
                                    ?>"
                                >

                                    <?= $x['is_active']
                                        ? 'Disable'
                                        : 'Enable'
                                    ?>

                                </button>

                            </form>


                        <?php endif; ?>


                    </div>

                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>


<?php

include dirname(__DIR__) . '/includes/footer.php';

?>