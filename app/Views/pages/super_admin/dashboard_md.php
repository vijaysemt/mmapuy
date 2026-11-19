<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
/**
 * Renders one stat card.
 * $statKey  : data-stat path (null = no number shown)
 * $url      : if given, the card becomes a link
 */
$renderCard = function (
    string $title,
    ?string $statKey,
    string $sub,
    string $icon,
    string $color = 'primary',
    ?string $url = null,
    string $cardClass = 'mela-card'
): string {
    $html  = '<div class="col-xl-3 col-md-6">';
    if ($url) {
        $html .= '<a href="' . esc($url) . '" class="dashboard-card-link">';
    }
    $html .= '<div class="stat-card ' . $cardClass . '"><div>';
    $html .= '<span class="stat-title">' . esc($title) . '</span>';
    if ($statKey) {
        $html .= '<h3 class="stat-value" data-stat="' . esc($statKey) . '">--</h3>';
    }
    if ($sub !== '') {
        $html .= '<small>' . esc($sub) . '</small>';
    }
    $html .= '</div><div class="stat-icon ' . esc($color) . '"><i class="bi ' . esc($icon) . '"></i></div></div>';
    if ($url) {
        $html .= '</a>';
    }
    return $html . '</div>';
};

/** Mela card (days + venues) */
$renderMela = function (
    string $title,
    string $daysKey,
    string $venuesKey,
    string $icon,
    string $color,
    string $url,
    ?string $staticValue = null
): string {
    $days   = $staticValue === null
        ? '<h3 class="stat-value" data-stat="' . esc($daysKey) . '">--</h3>'
        : '<h3 class="stat-value">' . esc($staticValue) . '</h3>';
    $venues = $staticValue === null
        ? '<strong data-stat="' . esc($venuesKey) . '">--</strong>'
        : '<strong>' . esc($staticValue) . '</strong>';

    return '<div class="col-xl-3 col-md-6">
        <a href="' . esc($url) . '" class="dashboard-card-link">
            <div class="stat-card mela-card">
                <div>
                    <span class="stat-title">' . esc($title) . '</span>
                    ' . $days . '
                    <small>Total Mela Days</small>
                    <div class="mt-2">
                        <span class="stat-subtitle">Total Mela Venues:</span> ' . $venues . '
                    </div>
                </div>
                <div class="stat-icon ' . esc($color) . '"><i class="bi ' . esc($icon) . '"></i></div>
            </div>
        </a>
    </div>';
};
?>

<div class="dashboard-heading">
    <div>
        <h2>Welcome to Dashboard</h2>
        <p>
            मुख्यमंत्री अंत्योदय परिवार उत्थान योजना
            प्रशासनिक पोर्टल
        </p>
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
    <div class="dashboard-breadcrumb">
        <i class="bi bi-house"></i>
        Home / Dashboard
    </div>
</div>





<!-- Tabs -->
<ul class="nav nav-pills dashboard-tabs mb-4" id="dashboardTabs" role="tablist">

    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pane-overview"
            data-group="overview" type="button">Overview</button>
    </li>

    <?php if ($dashboardVersion === '1'): ?>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-adc"
                data-group="adc" type="button">ADC Submissions</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-mela"
                data-group="mela" type="button">Mela Phases</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-families"
                data-group="families" type="button">Families</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-members"
                data-group="members" type="button">Members</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-applications"
                data-group="applications" type="button">Applications</button>
        </li>
    <?php endif; ?>

    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-reported"
            data-group="reported" type="button">Reported Families</button>
    </li>

    <?php if ($dashboardVersion === '1'): ?>
        <!-- Static tab: no data-group, so nothing is loaded from the API -->
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-tools"
                type="button">Reports &amp; Tools</button>
        </li>
    <?php endif; ?>

    <li class="ms-auto">
        <button class="btn btn-outline-secondary btn-sm" id="refreshTab" type="button">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </li>
</ul>


<div class="tab-content">

    <!-- ===================== OVERVIEW ===================== -->
    <div class="tab-pane fade show active" id="pane-overview" data-pane="overview">

        <div class="pane-status"></div>

        <div class="dashboard-section section-blue">

            <h4 class="dashboard-section-title">Administration</h4>

            <div class="row g-4">
                <?= $renderCard('Districts', 'administration.districts', 'District list', 'bi-geo-alt', 'primary', site_url('super-admin/district-report-dashboard?v=' . $dashboardVersion)) ?>
                <?= $renderCard('Departments', 'administration.departments', 'Active departments', 'bi-building', 'success', site_url('super-admin/department-wise-report?v=' . $dashboardVersion)) ?>

                <?= $renderCard('Bank', 'administration.banks', 'Active banks', 'bi-bank', 'info', site_url('super-admin/bank-dashboard?v=' . $dashboardVersion)) ?>
                <?= $renderCard('Schemes', 'administration.schemes', 'Active schemes', 'bi-list-check', 'info') ?>
                <!-- <?php if ($dashboardVersion === '1'): ?>
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
                            <div class="stat-icon warning"><i class="bi bi-diagram-3"></i></div>
                        </div>
                    </div>
                <?php endif; ?> -->
            </div>

            <?php if ($dashboardVersion === '1'): ?>

                <!-- Highlights: loaded separately so the rest of Overview appears instantly -->
                <div data-pane="highlights" class="mt-3">
                    <div class="pane-status"></div>
                    <div class="row g-3">
                        <?= $renderCard('Search Family', null, 'Search family details', 'bi-search', 'primary', base_url('super-admin/search-by-family-id?v=1'), 'navigation-card') ?>
                        <?= $renderCard('Employment through HKRN', 'administration.joined_hkrn_applications', 'एच.के.आर.एन. के माध्यम से रोजगार', 'bi-briefcase-fill', 'primary', base_url('super-admin/search-by-family-id?v=1'), 'navigation-card') ?>
                        <?= $renderCard('Harhit Approved Applications', 'administration.approved_harhit_applications', 'हरहित स्वीकृत आवेदन', 'bi-patch-check-fill', 'success', base_url('super-admin/search-by-family-id?v=1'), 'navigation-card') ?>
                        <?= $renderCard('NPA Mark Count', 'administration.npa_marked', 'NPA marked applications', 'bi-exclamation-triangle', 'warning', null, 'navigation-card') ?>
                    </div>
                </div>


            <?php endif; ?>
        </div>

        <?php if ($dashboardVersion === '2'): ?>
            <!-- Developer toolkit - MMAPUY 2.0 -->
            <div class="dashboard-section section-slate mt-4">

                <h4 class="dashboard-section-title">
                    <i class="bi bi-code-slash"></i>
                    Developer Toolkit
                </h4>

                <p class="text-muted">
                    Internal utilities for development, configuration and data integration.
                </p>

                <div class="row g-4">
                    <div class="col-xl-3 col-md-6">
                        <a href="<?= site_url('super-admin/constituency-ward-village?v=2') ?>" class="dashboard-tool-card">
                            <i class="bi bi-geo-alt"></i>
                            <strong>Constituency Setup</strong>
                            <small>Manage constituency, ward and village mappings.</small>
                        </a>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <a href="<?= site_url('super-admin/block-push?v=2') ?>" class="dashboard-tool-card">
                            <i class="bi bi-cloud-upload"></i>
                            <strong>Block Push (SSA)</strong>
                            <small>Internal block-level data push utility.</small>
                        </a>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <a href="<?= site_url('super-admin/get-ssb-families?v=2') ?>" class="dashboard-tool-card">
                            <i class="bi bi-database"></i>
                            <strong>SSA Families Data</strong>
                            <small>Access the SSA family data utility.</small>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>


    <?php if ($dashboardVersion === '1'): ?>

        <!-- ===================== ADC SUBMISSIONS ===================== -->
        <div class="tab-pane fade" id="pane-adc" data-pane="adc">

            <div class="pane-status"></div>

            <div class="dashboard-section section-blue">

                <h4 class="dashboard-section-title">ADC Submissions</h4>

                <div class="row g-4">
                    <?php
                    $link = base_url('super-admin/search-by-family-id?v=1');

                    $adcCards = [
                        ['Pending for decision regarding approve/disapprove of rejected cases', 'administration.rejection_pending', 'अस्वीकृत मामलों के अनुमोदन/अस्वीकृति के संबंध में निर्णय के लिए लंबित', 'bi-hourglass-split'],
                        ['Rejected cases approved by ADC', 'administration.rejection_approved', 'एडीसी द्वारा स्वीकृत किये गए अस्वीकृत मामले', 'bi-check-circle-fill'],
                        ['Rejected cases disapproved by ADC', 'administration.rejection_disapproved', 'एडीसी द्वारा अस्वीकृत किये गए अस्वीकृत मामले', 'bi-x-circle-fill'],
                        ['Pending for decision regarding approve/disapprove of Not interested cases', 'administration.not_interested_pending', 'अनिच्छुक मामलों को स्वीकृत/अस्वीकृत करने के संबंध में निर्णय हेतु लंबित', 'bi-clock-history'],
                        ['Not interested cases approved by ADC', 'administration.not_interested_approved', 'एडीसी द्वारा स्वीकृत गैर इच्छुक मामले', 'bi-hand-thumbs-up-fill'],
                        ['Not interested cases disapproved by ADC', 'administration.not_interested_disapproved', 'एडीसी द्वारा अस्वीकृत मामलों में कोई दिलचस्पी नहीं है', 'bi-hand-thumbs-down-fill'],
                    ];
                    ?>

                    <?php foreach ($adcCards as [$title, $key, $sub, $icon]): ?>
                        <?= $renderCard($title, $key, $sub, $icon, 'primary', $link, 'navigation-card') ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>


        <!-- ===================== MELA PHASES ===================== -->
        <div class="tab-pane fade" id="pane-mela" data-pane="mela">

            <div class="pane-status"></div>

            <div class="dashboard-section section-blue">

                <h4 class="dashboard-section-title">Mela Phases</h4>

                <div class="row g-4">
                    <?= $renderMela('Mela Phase 1', 'melaStats.phase_1.dayDate', 'melaStats.phase_1.Total', 'bi-calendar-event', 'primary', base_url('super-admin/mela-dashboard-phase-1')) ?>
                    <?= $renderMela('Mela Phase 2', 'melaStats.phase_2.mela_days', 'melaStats.phase_2.total_mela_venues', 'bi-calendar-check', 'success', base_url('super-admin/mela-dashboard-phase-2')) ?>
                    <?= $renderMela('Mela Phase 3', 'melaStats.phase_3.mela_days', 'melaStats.phase_3.total_mela_venues', 'bi-calendar-week', 'warning', base_url('super-admin/mela-dashboard-phase-3')) ?>
                    <?= $renderMela('Harhit Mela', 'melaStats.harhit.mela_days', 'melaStats.harhit.total_mela_venues', 'bi-shop', 'danger', base_url('super-admin/harhit-mela-dashboard')) ?>
                    <?= $renderMela('Mela Phase 4', 'melaStats.phase_4.mela_days', 'melaStats.phase_4.total_mela_venues', 'bi-geo-alt', 'primary', base_url('super-admin/mela-dashboard-phase-4?mela_phase=5')) ?>
                    <?= $renderMela('Mela Phase 5', '', '', 'bi-flag', 'info', base_url('super-admin/mela-dashboard-phase-4?mela_phase=6'), '1') ?>
                </div>
            </div>
        </div>


        <!-- ===================== FAMILIES ===================== -->
        <div class="tab-pane fade" id="pane-families" data-pane="families">

            <div class="pane-status"></div>

            <div class="dashboard-section section-teal">

                <h4 class="dashboard-section-title">Family Statistics</h4>

                <div class="row g-4">
                    <?php
                    $familyCards = [
                        'eligible_familiess'        => 'Total eligible families (Below 18 and above 60 are excluded)',
                        'profiled_families'         => 'Total Families Surveyed',
                        'application_families'      => 'Total families surveyed (forwarded to department)',
                        'noschemeopted_families'    => 'Families opting for no scheme',
                        'unattended_families'       => 'Total families unattended',
                        'inprogress_families'       => 'Applications In Progress',
                        'sanctioned_families'       => 'Total applications approved',
                        'forwarded_tobank_families' => 'Total families applications forwarded to bank',
                        'loan_sanction_families'    => 'Total loan applications sanctioned',
                        'loan_disbursed_families'   => 'Total loan applications disbursed',
                        'rejected_family'           => 'Total applications rejected',
                        'not_interested_family'     => 'Total applications not interested',
                        'not_trace_family'          => 'Total applications not traceable',
                        'rejected_bybank_family'    => 'Total applications rejected by bank',
                        'wage_employment_family'    => 'Total applications of wage employment',
                        'total_private_sector'      => 'Total job joined private sector employment applications',
                    ];
                    ?>

                    <?php foreach ($familyCards as $key => $label): ?>
                        <?= $renderCard($label, 'families.' . $key, 'MMAPUY 1.0', 'bi-people', 'primary') ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>


        <!-- ===================== MEMBERS ===================== -->
        <div class="tab-pane fade" id="pane-members" data-pane="members">

            <div class="pane-status"></div>

            <div class="dashboard-section section-purple">

                <h4 class="dashboard-section-title">Member Statistics</h4>

                <div class="row g-4">
                    <?php
                    $memberCards = [
                        'eligible_members'          => 'Eligible Members',
                        'profiled_members'          => 'Total Member Surveyed',
                        'inprogress_members'        => 'Total applications in-progress members',
                        'unattended_members'        => 'Total members unattended',
                        'applications_received'     => 'Total members surveyed (forwarded to department)',
                        'noscheme_members'          => 'Members opting for no scheme',
                        'sanctioned_members'        => 'Total members applications approved',
                        'forwarded_to_bank_members' => 'Total members applications forwarded to bank',
                        'loan_sanction_members'     => 'Total member loan applications sanctioned',
                        'loan_disbursed_members'    => 'Total member loan applications disbursed',
                        'rejected_member'           => 'Total members applications rejected',
                        'not_interested_member'     => 'Total members applications not interested',
                        'not_trace_member'          => 'Total members applications not traceable',
                    ];
                    ?>

                    <?php foreach ($memberCards as $key => $label): ?>
                        <?= $renderCard($label, 'members.' . $key, 'MMAPUY 1.0', 'bi-person', 'info') ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>


        <!-- ===================== APPLICATIONS ===================== -->
        <div class="tab-pane fade" id="pane-applications" data-pane="applications">

            <div class="pane-status"></div>

            <div class="dashboard-section section-slate">

                <h4 class="dashboard-section-title">Application Statistics</h4>

                <div class="row g-4">
                    <?php
                    $applicationCards = [
                        'applications_received'        => 'Applications Received',
                        'family_applications_received' => 'Family Applications Received',
                        'no_scheme_families'           => 'Families with No Scheme',
                    ];
                    ?>

                    <?php foreach ($applicationCards as $key => $label): ?>
                        <?= $renderCard($label, 'applications.' . $key, 'MMAPUY 1.0', 'bi-file-earmark-text', 'success') ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    <?php endif; ?>


    <!-- ===================== REPORTED FAMILIES ===================== -->
    <div class="tab-pane fade" id="pane-reported" data-pane="reported">

        <div class="pane-status"></div>

        <div class="dashboard-section section-amber">

            <h4 class="dashboard-section-title">Reported Families</h4>

            <div class="row g-4">
                <?= $renderCard('Income Mismatch', 'reported.income_mismatch', 'Reported families', 'bi-exclamation-triangle', 'warning') ?>
                <?= $renderCard('Splitted Families', 'reported.splitted_families', 'Reported families', 'bi-diagram-2', 'danger') ?>

                <?php if ($dashboardVersion === '2'): ?>
                    <?= $renderCard('NPA Marked', 'reported.npa_marked', 'NPA applications', 'bi-bank', 'info') ?>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <?php if ($dashboardVersion === '1'): ?>
        <!-- ===================== REPORTS & TOOLS (static links) ===================== -->
        <div class="tab-pane fade" id="pane-tools">

            <div class="dashboard-section section-teal">

                <h4 class="dashboard-section-title">Reports &amp; Tools</h4>

                <div class="row g-4">
                    <?= $renderCard('Reports', null, 'Scheme-wise reports', 'bi-file-earmark-bar-graph', 'success', base_url('super-admin/schemewise-report?v=1'), 'navigation-card') ?>
                    <?= $renderCard('Calling Status', null, 'View calling progress', 'bi-telephone', 'warning', base_url('super-admin/calling-status?v=1'), 'navigation-card') ?>
                    <?= $renderCard('Historical Data', null, 'View historical records', 'bi-clock-history', 'info', base_url('super-admin/historical-data'), 'navigation-card') ?>
                    <?= $renderCard('Docs Requests Count', null, 'View document requests', 'bi-file-earmark-text', 'danger', base_url('super-admin/docs-requests-dashboard?v=1'), 'navigation-card') ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>


<!-- API loading JavaScript (lazy loads each tab) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const apiUrl = <?= json_encode($statsApiUrl) ?>;
        const version = <?= json_encode($dashboardVersion) ?>;
        const loginUrl = <?= json_encode(site_url('login')) ?>;

        const loaded = {};
        let currentGroup = 'overview';

        const formatNumber = v => Number(v ?? 0).toLocaleString('en-IN');

        function getNestedValue(obj, path) {
            return path.split('.').reduce((cur, key) =>
                cur && cur[key] !== undefined ? cur[key] : null, obj);
        }

        function renderStats(data, scope) {
            scope.querySelectorAll('[data-stat]').forEach(el => {
                const value = getNestedValue(data, el.dataset.stat);
                el.textContent = value === null ? '0' : formatNumber(value);
            });
        }

        function setStatus(pane, type, html) {
            const box = pane.querySelector('.pane-status');
            box.innerHTML = html ?
                `<div class="alert alert-${type} py-2">${html}</div>` :
                '';
        }

        async function loadGroup(group, force = false) {

            if (loaded[group] && !force) return;

            const pane = document.querySelector(`[data-pane="${group}"]`);
            if (!pane) return;

            pane.classList.add('is-loading');
            setStatus(pane, 'info',
                '<span class="spinner-border spinner-border-sm me-2"></span>Loading data...');

            try {
                const url = `${apiUrl}?v=${encodeURIComponent(version)}&group=${group}` +
                    (force ? '&refresh=1' : '');

                const response = await fetch(url, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.status === 401) {
                    window.location.href = loginUrl;
                    return;
                }

                const result = await response.json();

                if (!response.ok || !result.status) {
                    throw new Error(result.message || 'Unable to load data.');
                }

                renderStats(result.data, pane);
                loaded[group] = true;
                setStatus(pane, '', '');

            } catch (error) {
                console.error('Dashboard API error:', error);

                setStatus(pane, 'danger',
                    `${error.message} <button type="button" class="btn btn-sm btn-link p-0 ms-2 retry-btn">Retry</button>`);

                const retry = pane.querySelector('.retry-btn');
                if (retry) retry.addEventListener('click', () => loadGroup(group, true));

            } finally {
                pane.classList.remove('is-loading');
            }
        }

        // Load a tab the first time it is shown
        const refreshBtn = document.getElementById('refreshTab');

        document.querySelectorAll('#dashboardTabs [data-bs-toggle="pill"]').forEach(btn => {
            btn.addEventListener('shown.bs.tab', () => {
                currentGroup = btn.dataset.group || null; // static tabs have no group
                refreshBtn.style.display = currentGroup ? '' : 'none';
                if (currentGroup) loadGroup(currentGroup);
            });
        });

        // Refresh the active tab (bypasses cache)
        refreshBtn.addEventListener('click', () => {
            if (!currentGroup) return;
            loadGroup(currentGroup, true);
            if (currentGroup === 'overview') loadGroup('highlights', true);
        });

        // First tab (highlights loads in parallel, only exists in version 1)
        loadGroup('overview');
        loadGroup('highlights');
    });
</script>

<?= $this->endSection() ?>
</document_content>