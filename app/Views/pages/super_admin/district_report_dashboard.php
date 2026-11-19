<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="dashboard-heading">
    <div>
        <h2>District Reports</h2>
        <p>Choose how you want to view the district-wise report</p>
    </div>

    <div class="dashboard-breadcrumb">
        <i class="bi bi-house"></i>
        <a href="<?= site_url('super-admin/dashboard?v=' . esc($version, 'url')) ?>">Dashboard</a> / Districts
    </div>
</div>

<div class="dashboard-section section-blue">

    <h4 class="dashboard-section-title">Districts</h4>

    <div class="row g-4">

        <div class="col-xl-3 col-md-6">
            <a href="<?= site_url('super-admin/district-report-family-wise?v=' . esc($version, 'url')) ?>"
                class="dashboard-card-link">
                <div class="stat-card navigation-card">
                    <div>
                        <span class="stat-title">Family wise</span>
                        <small>जिला (परिवार अनुसार)</small>
                    </div>
                    <div class="stat-icon primary"><i class="bi bi-people"></i></div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="<?= site_url('super-admin/district-report-member-wise?v=' . esc($version, 'url')) ?>"
                class="dashboard-card-link">
                <div class="stat-card navigation-card">
                    <div>
                        <span class="stat-title">Member wise</span>
                        <small>जिला (सदस्य अनुसार)</small>
                    </div>
                    <div class="stat-icon danger"><i class="bi bi-person"></i></div>
                </div>
            </a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>