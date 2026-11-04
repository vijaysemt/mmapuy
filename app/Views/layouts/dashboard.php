<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MMAPUY Dashboard | Haryana Government</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <!-- Dashboard CSS -->
    <link rel="stylesheet"
          href="<?= base_url('assets/mmapua/css/dashboard.css?v=2') ?>">
</head>

<body class="admin-dashboard">

    <div class="admin-layout">

        <?= $this->include('includes/sidebar') ?>

        <div class="admin-main">

            <!-- TOP BAR -->
            <header class="admin-topbar">

                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>

                <div class="admin-user">
                    <i class="bi bi-person-circle"></i>

                    <a href="<?= site_url('login') ?>"
                       class="logout-link">
                        Log Out
                    </a>
                </div>

            </header>

            <!-- DASHBOARD CONTENT -->
            <main class="admin-content">
                <?= $this->renderSection('content') ?>
            </main>

            <!-- FOOTER -->
            <footer class="admin-footer">
                Developed by Citizen Resources Information Department,
                Government of Haryana.
                Content owned by Department of Social Justice and
                Empowerment, Government of Haryana.
                <br>
                © 2026 All Rights Reserved.
            </footer>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.querySelector('.admin-layout').classList.toggle('sidebar-collapsed');
        });
    </script>

</body>
</html>