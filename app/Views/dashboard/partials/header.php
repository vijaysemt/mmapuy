<?php

$userName =
    $user['user_name']
    ?? $user['name']
    ?? $user['mobile']
    ?? $user['depart_dist_mob']
    ?? 'Portal User';

?>

<header class="admin-header d-flex justify-content-between align-items-center bg-white p-3 shadow-sm">

    <div class="header-left d-flex align-items-center gap-3">

        <?php if (empty($headerOnly)): ?>

            <button class="sidebar-toggle btn btn-light"
                    id="sidebarToggle"
                    type="button">

                <i class="bi bi-list"></i>

            </button>

        <?php endif; ?>

        <div class="header-brand fw-bold">

            <i class="bi bi-shield-check text-primary"></i>

            <span>Parivar Utthan Portal</span>

        </div>

    </div>


    <div class="header-right d-flex align-items-center gap-3">

        <div class="header-date text-muted">

            <i class="bi bi-calendar3"></i>

            <?= date('d M Y') ?>

        </div>


        <div class="dropdown">

            <button class="user-profile btn btn-light dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown">

                <span class="user-avatar">

                    <?= esc(strtoupper(substr($userName, 0, 1))) ?>

                </span>

                <span class="user-info">

                    <strong><?= esc($userName) ?></strong>

                    <small class="d-block">
                        Role ID: <?= esc($role) ?>
                    </small>

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