
<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="dashboard-heading">

    <div>

        <h2>Welcome to Dashboard</h2>

        <p>
            मुख्यमंत्री अंत्योदय परिवार उत्थान योजना
            प्रशासनिक पोर्टल
        </p>

    </div>

    <div class="dashboard-breadcrumb">

        <i class="bi bi-house"></i>
        Home / Dashboard

    </div>

</div>


<div class="row g-4">

    <?php foreach ($dashboardStats as $stat): ?>

        <div class="col-xl-3 col-md-6">

            <div class="stat-card">

                <div>

                    <span class="stat-title">
                        <?= esc($stat['title']) ?>
                    </span>

                    <h3>
                        <?= esc($stat['value']) ?>
                    </h3>

                    <small>Portal Overview</small>

                </div>

                <div class="stat-icon <?= esc($stat['color']) ?>">

                    <i class="bi <?= esc($stat['icon']) ?>"></i>

                </div>

            </div>

        </div>

    <?php endforeach; ?>

</div>


<div class="row g-4 mt-2">

    <div class="col-lg-8">

        <div class="dashboard-panel">

            <div class="panel-heading">

                <h5>Portal Overview</h5>

                <span>Overview</span>

            </div>

            <div class="panel-body">

                <div class="welcome-banner">

                    <div>

                        <h4>
                            Welcome to MMAPUY Portal
                        </h4>

                        <p>
                            Manage your assigned activities,
                            access reports and monitor portal information.
                        </p>

                    </div>

                    <i class="bi bi-speedometer2"></i>

                </div>

            </div>

        </div>

    </div>


    <div class="col-lg-4">

        <div class="dashboard-panel">

            <div class="panel-heading">

                <h5>Account Information</h5>

            </div>

            <div class="panel-body">

                <div class="account-detail">

                    <span>Role ID</span>

                    <strong><?= esc($role) ?></strong>

                </div>

                <div class="account-detail">

                    <span>Account Status</span>

                    <strong class="text-success">
                        Active
                    </strong>

                </div>

                <div class="account-detail">

                    <span>Login Status</span>

                    <strong class="text-success">
                        Authenticated
                    </strong>

                </div>

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>
