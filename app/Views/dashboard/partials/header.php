
<?php
$userName =
    $user['user_name']
    ?? $user['name']
    ?? $user['mobile']
    ?? $user['depart_dist_mob']
    ?? 'Portal User';
?>

<header class="admin-header">

    <div class="header-left">

        <?php if (empty($headerOnly)): ?>

            <button class="sidebar-toggle"
                    id="sidebarToggle"
                    type="button">

                <i class="bi bi-list"></i>

            </button>

        <?php endif; ?>

        <div class="header-brand">

            <i class="bi bi-shield-check"></i>

            <span>
                Parivar Utthan Portal
            </span>

        </div>

    </div>


    <div class="header-right">

        <div class="header-date">
            <i class="bi bi-calendar3"></i>
            <?= date('d M Y') ?>
        </div>


        <div class="dropdown">

            <button class="user-profile dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown">

                <span class="user-avatar">
                    <?= strtoupper(substr($userName, 0, 1)) ?>
                </span>

                <span class="user-info">

                    <strong><?= esc($userName) ?></strong>

                    <small>Role ID: <?= esc($role) ?></small>

                </span>

            </button>


            <ul class="dropdown-menu dropdown-menu-end">

                <li>
                    <a class="dropdown-item"
                       href="<?= site_url('auth/logout') ?>">

                        <i class="bi bi-box-arrow-right me-2"></i>
                        Logout

                    </a>
                </li>

            </ul>

        </div>

    </div>

</header>
