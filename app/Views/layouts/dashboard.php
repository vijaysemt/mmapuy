
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
