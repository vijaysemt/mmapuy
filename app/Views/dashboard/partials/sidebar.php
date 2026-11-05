
<?php

$menus = [

    '2' => [
        ['Dashboard', 'dashboard', 'bi-speedometer2'],
        ['District Users', '#', 'bi-people'],
        ['Reports', '#', 'bi-file-earmark-bar-graph'],
    ],

    '3' => [
        ['Dashboard', 'dashboard', 'bi-speedometer2'],
        ['Department Users', '#', 'bi-building'],
        ['Reports', '#', 'bi-file-earmark-bar-graph'],
    ],

    '6' => [
        ['Dashboard', 'dashboard', 'bi-speedometer2'],
        ['Department Details', '#', 'bi-building'],
        ['District Details', '#', 'bi-geo-alt'],
    ],

    '7' => [
        ['Dashboard', 'dashboard', 'bi-speedometer2'],
        ['Beneficiary Details', '#', 'bi-people'],
        ['Reports', '#', 'bi-file-earmark-bar-graph'],
    ],

    '8' => [
        ['Dashboard', 'dashboard', 'bi-speedometer2'],
        ['District Reports', '#', 'bi-bar-chart'],
    ],

    '11' => [
        ['Dashboard', 'dashboard', 'bi-speedometer2'],
        ['Bank Details', '#', 'bi-bank'],
        ['Reports', '#', 'bi-file-earmark-bar-graph'],
    ],

    '18' => [
        ['Dashboard', 'dashboard', 'bi-speedometer2'],
        ['Department Reports', '#', 'bi-file-earmark-bar-graph'],
    ],

];

$roleMenus = $menus[$role] ?? [
    ['Dashboard', 'dashboard', 'bi-speedometer2'],
];

?>

<aside class="admin-sidebar" id="adminSidebar">

    <div class="sidebar-logo">

        <div class="sidebar-logo-icon">
            <i class="bi bi-building"></i>
        </div>

        <div>
            <strong>MMAPUY</strong>
            <small>Administration Portal</small>
        </div>

    </div>


    <div class="sidebar-section-title">
        MAIN MENU
    </div>


    <ul class="sidebar-menu">

        <?php foreach ($roleMenus as $menu): ?>

            <li>

                <a href="<?= $menu[1] === 'dashboard'
                    ? site_url('dashboard')
                    : esc($menu[1]) ?>"
                   class="<?= $menu[1] === 'dashboard' ? 'active' : '' ?>">

                    <i class="bi <?= esc($menu[2]) ?>"></i>

                    <span><?= esc($menu[0]) ?></span>

                </a>

            </li>

        <?php endforeach; ?>

    </ul>


    <div class="sidebar-bottom">

        <a href="<?= site_url('auth/logout') ?>">

            <i class="bi bi-box-arrow-right"></i>

            <span>Logout</span>

        </a>

    </div>

</aside>
