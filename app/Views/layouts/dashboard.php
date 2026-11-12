
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= esc($dashboardTitle ?? 'Dashboard') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="<?= base_url('assets/mmapua/css/dashboard.css') ?>">

            <style>

        body {
            background: #f5f7fb;
            font-family: Arial, sans-serif;
        }

        .admin-main {
            padding: 25px;
            min-height: calc(100vh - 120px);
        }

        .dashboard-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .dashboard-heading h2 {
            font-size: 25px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .dashboard-heading p {
            color: #6b7280;
            margin: 0;
        }

        .dashboard-breadcrumb {
            color: #6b7280;
        }

        .dashboard-section-title {
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .stat-card {
            background: #fff;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 125px;
        }

        .stat-title {
            color: #6b7280;
            font-size: 14px;
        }

        .stat-card h3 {
            font-size: 27px;
            font-weight: 700;
            margin: 10px 0;
        }

        .stat-card small {
            color: #9ca3af;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-icon.primary {
            color: #2563eb;
            background: #dbeafe;
        }

        .stat-icon.success {
            color: #16a34a;
            background: #dcfce7;
        }

        .stat-icon.warning {
            color: #d97706;
            background: #fef3c7;
        }

        .stat-icon.info {
            color: #0891b2;
            background: #cffafe;
        }

        .stat-icon.danger {
            color: #dc2626;
            background: #fee2e2;
        }

        .dashboard-tool-card {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 22px;
            min-height: 150px;
            background: #fff;
            color: #1f2937;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 2px 12px rgba(0,0,0,.04);
            transition: .2s;
        }

        .dashboard-tool-card:hover {
            transform: translateY(-3px);
            color: #2563eb;
        }

        .dashboard-tool-card i {
            font-size: 25px;
        }

        .dashboard-tool-card small {
            color: #6b7280;
        }

        .admin-footer {
            background: #fff;
            padding: 15px 25px;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
        }

        @media(max-width: 767px) {

            .admin-main {
                padding: 15px;
            }

            .dashboard-heading h2 {
                font-size: 21px;
            }

        }

    </style>

</head>

<body>

<div class="admin-layout">

    <?php if (empty($headerOnly)): ?>

        <?= view('dashboard/partials/sidebar', [
            'role' => $role
        ]) ?>

    <?php endif; ?>


    <div class="admin-main">

        <?= view('dashboard/partials/header', [
            'headerOnly' => $headerOnly ?? false,
            'user' => $user ?? [],
            'role' => $role
        ]) ?>


        <main class="dashboard-content">

            <?= $this->renderSection('content') ?>

        </main>


        <?= view('dashboard/partials/footer') ?>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= base_url('assets/mmapua/js/dashboard.js') ?>"></script>

</body>

</html>
