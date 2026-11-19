<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="dashboard-heading">
    <div>
        <h2>Scheme wise Reports</h2>
        <p>Select a scheme category</p>
    </div>

    <div class="dashboard-breadcrumb">
        <i class="bi bi-house"></i>
        <a href="<?= site_url('super-admin/dashboard?v=' . esc($version, 'url')) ?>">Dashboard</a> / Schemes
    </div>
</div>

<div class="dashboard-section section-blue">

    <h4 class="dashboard-section-title">Scheme categories</h4>

    <div class="row g-4">
        <?php foreach ($types as $type => [$english, $hindi, $icon, $color]): ?>
            <div class="col-xl-3 col-md-6">
                <a href="<?= site_url('super-admin/scheme-wise-report?' . http_build_query(['type' => $type, 'v' => $version])) ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title"><?= esc($english) ?></span>
                            <small><?= esc($hindi) ?></small>
                        </div>
                        <div class="stat-icon <?= esc($color) ?>"><i class="bi <?= esc($icon) ?>"></i></div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?= $this->endSection() ?>