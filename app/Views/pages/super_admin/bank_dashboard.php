<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="dashboard-heading">
    <div>
        <h2>Bank Reports</h2>
        <p>Choose a report</p>
    </div>

    <div class="dashboard-breadcrumb">
        <i class="bi bi-house"></i>
        <a href="<?= site_url('super-admin/dashboard?v=' . esc($version, 'url')) ?>">Dashboard</a> / Bank
    </div>
</div>

<div class="dashboard-section section-amber">

    <h4 class="dashboard-section-title">Reports</h4>

    <div class="row g-4">

        <div class="col-xl-3 col-md-6">
            <a href="<?= site_url('super-admin/bank-wise-report?v=' . esc($version, 'url')) ?>"
                class="dashboard-card-link">
                <div class="stat-card navigation-card">
                    <div>
                        <span class="stat-title">Bankwise Report</span>
                        <small>Applications by bank</small>
                    </div>
                    <div class="stat-icon warning"><i class="bi bi-bank"></i></div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="<?= site_url('super-admin/branch-wise-report?v=' . esc($version, 'url')) ?>"
                class="dashboard-card-link">
                <div class="stat-card navigation-card">
                    <div>
                        <span class="stat-title">Branchwise Report</span>
                        <small>Applications by bank branch</small>
                    </div>
                    <div class="stat-icon info"><i class="bi bi-diagram-3"></i></div>
                </div>
            </a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>