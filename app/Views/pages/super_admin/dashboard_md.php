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


<!-- Dashboard version selection -->

<div class="dashboard-version-switch mb-4">

    <a href="<?= site_url('super-admin/dashboard?v=1') ?>"
        class="btn <?= $dashboardVersion === '1' ? 'btn-primary' : 'btn-outline-primary' ?>">

        MMAPUY 1.0

    </a>

    <a href="<?= site_url('super-admin/dashboard?v=2') ?>"
        class="btn <?= $dashboardVersion === '2' ? 'btn-primary' : 'btn-outline-primary' ?>">

        MMAPUY 2.0

    </a>

</div>


<!-- Loading / error message -->

<div id="dashboardMessage" class="alert alert-info">

    <span class="spinner-border spinner-border-sm me-2"></span>

    Loading dashboard statistics...

</div>


<!-- Administration -->

<div class="dashboard-section">

    <h4 class="dashboard-section-title">
        Administration
    </h4>

    <div class="row g-4">

        <div class="col-xl-3 col-md-6">
            <div class="stat-card mela-card">
                <div>
                    <span class="stat-title">Districts</span>
                    <h3 class="stat-value" data-stat="administration.districts">--</h3>
                    <small>District list</small>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-geo-alt"></i>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6">
            <div class="stat-card mela-card">
                <div>
                    <span class="stat-title">Departments</span>
                    <h3 class="stat-value" data-stat="administration.departments">--</h3>
                    <small>Active departments</small>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-building"></i>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6">
            <div class="stat-card mela-card">
                <div>
                    <span class="stat-title">Schemes</span>
                    <h3 class="stat-value" data-stat="administration.schemes">--</h3>
                    <small>Active schemes</small>
                </div>
                <div class="stat-icon info">
                    <i class="bi bi-list-check"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card mela-card">
                <div>
                    <span class="stat-title">Bank</span>
                    <h3 class="stat-value" data-stat="administration.schemes">--</h3>
                    <!-- <small>Active schemes</small> -->
                </div>
                <div class="stat-icon info">
                    <i class="bi bi-list-check"></i>
                </div>
            </div>
        </div>

        <?php if ($dashboardVersion === '1'): ?>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card mela-card">
                    <div>
                        <span class="stat-title">Zonal Committees</span>
                        <h3 class="stat-value">
                            <span data-stat="administration.formed_zones">--</span>
                            /
                            <span data-stat="administration.total_zones">--</span>
                        </h3>
                        <small>Formed / Total</small>
                    </div>
                    <div class="stat-icon warning">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                </div>
            </div>








        <?php endif; ?>

    </div>

    <?php if ($dashboardVersion === '1'): ?>
        <div class="row g-3 mt-2">

            <!-- Phase 1 -->
            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/mela-dashboard-phase-1'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card mela-card">
                        <div>
                            <span class="stat-title">Mela Phase 1</span>
                            <h3 class="stat-value" data-stat="melaStats.phase_1.dayDate">
                                0
                            </h3>
                            <small>Total Mela Days</small>

                            <div class="mt-2">
                                <span class="stat-subtitle">Total Mela Venues:</span>
                                <strong data-stat="melaStats.phase_1.Total">
                                    0
                                </strong>
                            </div>
                        </div>

                        <div class="stat-icon primary">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Phase 2 -->
            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/mela-dashboard-phase-2'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card mela-card">
                        <div>
                            <span class="stat-title">Mela Phase 2</span>
                            <h3 class="stat-value" data-stat="melaStats.phase_2.mela_days">
                                0
                            </h3>
                            <small>Total Mela Days</small>

                            <div class="mt-2">
                                <span class="stat-subtitle">Total Mela Venues:</span>
                                <strong data-stat="melaStats.phase_2.total_mela_venues">
                                    0
                                </strong>
                            </div>
                        </div>

                        <div class="stat-icon success">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Phase 3 -->
            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/mela-dashboard-phase-3'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card mela-card">
                        <div>
                            <span class="stat-title">Mela Phase 3</span>
                            <h3 class="stat-value" data-stat="melaStats.phase_3.mela_days">
                                0
                            </h3>
                            <small>Total Mela Days</small>

                            <div class="mt-2">
                                <span class="stat-subtitle">Total Mela Venues:</span>
                                <strong data-stat="melaStats.phase_3.total_mela_venues">
                                    0
                                </strong>
                            </div>
                        </div>

                        <div class="stat-icon warning">
                            <i class="bi bi-calendar-week"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Harhit Mela -->
            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/harhit-mela-dashboard'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card mela-card">
                        <div>
                            <span class="stat-title">Harhit Mela</span>
                            <h3 class="stat-value" data-stat="melaStats.harhit.mela_days">
                                0
                            </h3>
                            <small>Total Mela Days</small>

                            <div class="mt-2">
                                <span class="stat-subtitle">Total Mela Venues:</span>
                                <strong data-stat="melaStats.harhit.total_mela_venues">
                                    0
                                </strong>
                            </div>
                        </div>

                        <div class="stat-icon danger">
                            <i class="bi bi-shop"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Phase 4 -->
            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/mela-dashboard-phase-4?mela_phase=5'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card mela-card">
                        <div>
                            <span class="stat-title">Mela Phase 4</span>
                            <h3 class="stat-value" data-stat="melaStats.phase_4.mela_days">
                                0
                            </h3>
                            <small>Total Mela Days</small>

                            <div class="mt-2">
                                <span class="stat-subtitle">Total Mela Venues:</span>
                                <strong data-stat="melaStats.phase_4.total_mela_venues">
                                    0
                                </strong>
                            </div>
                        </div>

                        <div class="stat-icon primary">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Phase 5 -->
            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/mela-dashboard-phase-4?mela_phase=6'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card mela-card">
                        <div>
                            <span class="stat-title">Mela Phase 5</span>
                            <h3 class="stat-value">1</h3>
                            <small>Total Mela Days</small>

                            <div class="mt-2">
                                <span class="stat-subtitle">Total Mela Venues:</span>
                                <strong>1</strong>
                            </div>
                        </div>

                        <div class="stat-icon info">
                            <i class="bi bi-flag"></i>
                        </div>
                    </div>
                </a>
            </div>

        </div>
        <div class="row g-3 mt-2">

            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/search-by-family-id?v=1'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Employment through HKRN</span>
                             <h3 class="stat-value" data-stat="administration.joined_hkrn_applications">0</h3>
                            <small>एच.के.आर.एन. के माध्यम से रोजगार</small>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/search-by-family-id?v=1'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Harhit Approved Applications</span>
                             <h3 class="stat-value" data-stat="administration.approved_harhit_applications">0</h3>
                            <small>हरहित स्वीकृत आवेदन</small>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/search-by-family-id?v=1'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Pending for decision regarding approve/disapprove of rejected cases</span>
                             <h3 class="stat-value" data-stat="administration.rejection_pending">0</h3>
                            <small>अस्वीकृत मामलों के अनुमोदन/अस्वीकृति के संबंध में निर्णय के लिए लंबित</small>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/search-by-family-id?v=1'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Rejected cases approved by ADC</span>
                             <h3 class="stat-value" data-stat="administration.rejection_approved">0</h3>
                            <small>एडीसी द्वारा स्वीकृत किये गए अस्वीकृत मामले</small>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/search-by-family-id?v=1'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Rejected cases disapproved by ADC</span>
                             <h3 class="stat-value" data-stat="administration.rejection_disapproved">0</h3>
                            <small>एडीसी द्वारा अस्वीकृत किये गए अस्वीकृत मामले</small>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/search-by-family-id?v=1'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Pending for decision regarding approve/disapprove of Not interested cases</span>
                             <h3 class="stat-value" data-stat="administration.not_interested_pending">0</h3>
                            <small>अनिच्छुक मामलों को स्वीकृत/अस्वीकृत करने के संबंध में निर्णय हेतु लंबित</small>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/search-by-family-id?v=1'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Not interested cases approved by ADC</span>
                             <h3 class="stat-value" data-stat="administration.not_interested_approved">0</h3>
                            <small>एडीसी द्वारा स्वीकृत गैर इच्छुक मामले</small>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-hand-thumbs-up-fill"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/search-by-family-id?v=1'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Not interested cases disapproved by ADC</span>
                             <h3 class="stat-value" data-stat="administration.not_interested_disapproved">0</h3>
                            <small>एडीसी द्वारा अस्वीकृत मामलों में कोई दिलचस्पी नहीं है</small>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-hand-thumbs-down-fill"></i>
                        </div>
                    </div>
                </a>
            </div>
            
        </div>

        <div class="row g-3 mt-2">

            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/search-by-family-id?v=1'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Search Family</span>
                            <small>Search family details</small>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-search"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/schemewise-report?v=1'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Reports</span>
                            <small>Scheme-wise reports</small>
                        </div>
                        <div class="stat-icon success">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/calling-status?v=1'); ?>"
                    class="dashboard-card-link" >
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Calling Status</span>
                            <small>View calling progress</small>
                        </div>
                        <div class="stat-icon warning">
                            <i class="bi bi-telephone"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/historical-data'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Historical Data</span>
                            <small>View historical records</small>
                        </div>
                        <div class="stat-icon info">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6">
                <a href="<?= base_url('super-admin/docs-requests-dashboard?v=1'); ?>"
                    class="dashboard-card-link">
                    <div class="stat-card navigation-card">
                        <div>
                            <span class="stat-title">Docs Requests Count</span>
                            <small>View document requests</small>
                        </div>
                        <div class="stat-icon danger">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card navigation-card">
                    <div>
                        <span class="stat-title">NPA Mark Count</span>
                        <h3 class="stat-value">
                            <?= esc($dashboardStats['administration']['npa_marked'] ?? 0); ?>
                        </h3>
                    </div>
                    <div class="stat-icon warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>

</div>


<?php if ($dashboardVersion === '1'): ?>

    <!-- Family statistics -->

    <div class="dashboard-section mt-4">

        <h4 class="dashboard-section-title">
            Family Statistics
        </h4>

        <div class="row g-4">

            <?php
            $familyCards = [
                'eligible_familiess' => 'Total eligible families  (Below 18 and above 60 are excluded)',
                'profiled_families' => 'Total Families Surveyed ',
                'application_families' => 'Total families surveyed (forwarded to department)',
                'noschemeopted_families' => 'Families opting for no scheme',
                'unattended_families' => 'Total families unattended',
                'inprogress_families' => 'Applications In Progress',
               
                'sanctioned_families' => 'Total applications approved',
                'forwarded_tobank_families' => 'Total families applications forwarded to bank',
                'loan_sanction_families' => 'Total loan applications sanctioned',
                'loan_disbursed_families' => 'Total loan applications disbursed',
                'rejected_family' => 'Total applications rejected',
                'not_interested_family' => 'Total applications not interested',
                'not_trace_family' => 'Total applications not traceable',
                'rejected_bybank_family' => 'Total applications rejected by bank',
                'wage_employment_family' => 'Total applications of wage employment',
                'total_private_sector' => 'Total job joined private sector employment applications',
                

            ];
            ?>

            <?php foreach ($familyCards as $key => $label): ?>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card mela-card">
                        <div>
                            <span class="stat-title"><?= esc($label) ?></span>
                            <h3 class="stat-value" data-stat="families.<?= esc($key) ?>">--</h3>
                            <small>MMAPUY 1.0</small>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    </div>


    <!-- Member statistics -->

    <div class="dashboard-section mt-4">

        <h4 class="dashboard-section-title">
            Member Statistics
        </h4>

        <div class="row g-4">

            <?php
            $memberCards = [
                'eligible_members' => 'Eligible Members',
                'profiled_members' => 'Total Member Surveyed ',

                'inprogress_members' => 'Total applications in-progress members',
                'unattended_members' => 'Total members unattended',
                'applications_received' => 'Total members surveyed (forwarded to department)',
                'noscheme_members' => 'Members opting for no scheme',
                'sanctioned_members' => 'Total members applications approved',
                'forwarded_to_bank_members' => 'Total members applications forwarded to bank',
                'loan_sanction_members' => 'Total member loan applications sanctioned',
                'loan_disbursed_members' => 'Total member loan applications disbursed',
                'rejected_member' => 'Total members applications rejected',
                'not_interested_member' => 'Total members applications not interested',
                'not_trace_member' => 'Total members applications not traceable',
            ];
            ?>

            <?php foreach ($memberCards as $key => $label): ?>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card mela-card">
                        <div>
                            <span class="stat-title"><?= esc($label) ?></span>
                            <h3 class="stat-value" data-stat="members.<?= esc($key) ?>">--</h3>
                            <small>MMAPUY 1.0</small>
                        </div>
                        <div class="stat-icon info">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    </div>


    <!-- Application statistics -->

    <div class="dashboard-section mt-4">

        <h4 class="dashboard-section-title">
            Application Statistics
        </h4>

        <div class="row g-4">

            <?php
            $applicationCards = [
                'applications_received' => 'Applications Received',
                'family_applications_received' => 'Family Applications Received',
                'no_scheme_families' => 'Families with No Scheme',
            ];
            ?>

            <?php foreach ($applicationCards as $key => $label): ?>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card mela-card">
                        <div>
                            <span class="stat-title"><?= esc($label) ?></span>
                            <h3 class="stat-value" data-stat="applications.<?= esc($key) ?>">--</h3>
                            <small>MMAPUY 1.0</small>
                        </div>
                        <div class="stat-icon success">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    </div>

<?php endif; ?>


<!-- Reported families -->

<div class="dashboard-section mt-4">

    <h4 class="dashboard-section-title">
        Reported Families
    </h4>

    <div class="row g-4">

        <div class="col-xl-3 col-md-6">
            <div class="stat-card mela-card">
                <div>
                    <span class="stat-title">Income Mismatch</span>
                    <h3 class="stat-value" data-stat="reported.income_mismatch">--</h3>
                    <small>Reported families</small>
                </div>
                <div class="stat-icon warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6">
            <div class="stat-card mela-card">
                <div>
                    <span class="stat-title">Splitted Families</span>
                    <h3 class="stat-value" data-stat="reported.splitted_families">--</h3>
                    <small>Reported families</small>
                </div>
                <div class="stat-icon danger">
                    <i class="bi bi-diagram-2"></i>
                </div>
            </div>
        </div>


        <?php if ($dashboardVersion === '2'): ?>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card mela-card">
                    <div>
                        <span class="stat-title">NPA Marked</span>
                        <h3 class="stat-value" data-stat="reported.npa_marked">--</h3>
                        <small>NPA applications</small>
                    </div>
                    <div class="stat-icon info">
                        <i class="bi bi-bank"></i>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>


<!-- Developer toolkit - MMAPUY 2.0 -->

<?php if ($dashboardVersion === '2'): ?>

    <div class="dashboard-section mt-4">

        <h4 class="dashboard-section-title">
            <i class="bi bi-code-slash"></i>
            Developer Toolkit
        </h4>

        <p class="text-muted">
            Internal utilities for development, configuration and data integration.
        </p>

        <div class="row g-4">

            <div class="col-xl-3 col-md-6">
                <a href="<?= site_url('super-admin/constituency-ward-village?v=2') ?>"
                    class="dashboard-tool-card">
                    <i class="bi bi-geo-alt"></i>
                    <strong>Constituency Setup</strong>
                    <small>Manage constituency, ward and village mappings.</small>
                </a>
            </div>


            <div class="col-xl-3 col-md-6">
                <a href="<?= site_url('super-admin/block-push?v=2') ?>"
                    class="dashboard-tool-card">
                    <i class="bi bi-cloud-upload"></i>
                    <strong>Block Push (SSA)</strong>
                    <small>Internal block-level data push utility.</small>
                </a>
            </div>


            <div class="col-xl-3 col-md-6">
                <a href="<?= site_url('super-admin/get-ssb-families?v=2') ?>"
                    class="dashboard-tool-card">
                    <i class="bi bi-database"></i>
                    <strong>SSA Families Data</strong>
                    <small>Access the SSA family data utility.</small>
                </a>
            </div>

        </div>
    </div>

<?php endif; ?>


<!-- API loading JavaScript -->

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const apiUrl = <?= json_encode($statsApiUrl) ?>;
        const version = <?= json_encode($dashboardVersion) ?>;

        const messageBox = document.getElementById('dashboardMessage');

        function formatNumber(value) {
            return Number(value ?? 0).toLocaleString('en-IN');
        }

        function getNestedValue(object, path) {

            return path.split('.').reduce(function(current, key) {

                return current && current[key] !== undefined ?
                    current[key] :
                    null;

            }, object);
        }

        function renderStats(data) {

            document.querySelectorAll('[data-stat]').forEach(function(element) {

                const path = element.dataset.stat;
                const value = getNestedValue(data, path);

                element.textContent = value === null ?
                    '0' :
                    formatNumber(value);

            });
        }

        async function loadDashboardStats() {

            messageBox.className = 'alert alert-info';

            messageBox.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>' +
                'Loading dashboard statistics...';

            try {

                const response = await fetch(
                    apiUrl + '?v=' + encodeURIComponent(version), {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );

                if (response.status === 401) {
                    window.location.href = <?= json_encode(site_url('login')) ?>;
                    return;
                }

                const result = await response.json();

                if (!response.ok || !result.status) {
                    throw new Error(result.message || 'Unable to load statistics.');
                }

                renderStats(result.data);

                messageBox.className = 'alert alert-success';
                messageBox.textContent = 'Dashboard statistics loaded successfully.';

                setTimeout(function() {
                    messageBox.style.display = 'none';
                }, 2000);

            } catch (error) {

                console.error('Dashboard API error:', error);

                messageBox.className = 'alert alert-danger';
                messageBox.textContent =
                    error.message || 'Unable to load dashboard statistics.';

            }
        }

        loadDashboardStats();

    });
</script>


<?= $this->endSection() ?>