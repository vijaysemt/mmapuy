<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
$roleId = (string) session()->get('roleid');

$statistics = [];

if ($roleId === '1') {

    $statistics = [
        [
            'title' => 'Total Cluster Visits',
            'value' => $clustervisitcount[0]['clustervisitcount'] ?? 0,
            'icon'  => 'bi-geo-alt',
            'color' => 'blue'
        ],
        [
            'title' => 'Total Lab Visits',
            'value' => $labvisitcount[0]['labvisitcount'] ?? 0,
            'icon'  => 'bi-hospital',
            'color' => 'orange'
        ]
    ];

} elseif ($roleId === '2') {

    $statistics = [
        [
            'title' => 'Total Survey',
            'value' => $survey_detail_count[0]['survey_detailcount'] ?? 0,
            'icon'  => 'bi-clipboard-data',
            'color' => 'blue'
        ]
    ];

} elseif ($roleId === '3') {

    $statistics = [
        [
            'title' => 'Total Positive Sample',
            'value' => $positivecount[0]['positivecount'] ?? 0,
            'icon'  => 'bi-check-circle',
            'color' => 'green'
        ],
        [
            'title' => 'Total Negative Sample',
            'value' => $negativecount[0]['negativecount'] ?? 0,
            'icon'  => 'bi-x-circle',
            'color' => 'red'
        ],
        [
            'title' => 'Total Rejected Sample',
            'value' => $rejectedcount[0]['rejectedcount'] ?? 0,
            'icon'  => 'bi-slash-circle',
            'color' => 'orange'
        ],
        [
            'title' => 'Total Inconclusive Sample',
            'value' => $inconclusivecount[0]['inconclusivecount'] ?? 0,
            'icon'  => 'bi-question-circle',
            'color' => 'purple'
        ]
    ];
}
?>

<div class="dashboard-page">

    <div class="container-fluid">

        <!-- PAGE HEADING -->
        <div class="dashboard-heading">

            <div>
                <span class="dashboard-eyebrow">
                    MMAPUA SURVEY PORTAL
                </span>

                <h2>Statistics Dashboard</h2>

                <p>
                    Overview of survey and visit statistics.
                </p>
            </div>

            <div class="dashboard-heading-icon">
                <i class="bi bi-bar-chart-line"></i>
            </div>

        </div>


        <!-- STATISTICS -->
        <div class="row g-4">

            <?php if (!empty($statistics)): ?>

                <?php foreach ($statistics as $stat): ?>

                    <div class="col-xl-3 col-lg-4 col-md-6 col-12">

                        <div class="stat-card">

                            <div class="stat-card-top">

                                <div class="stat-icon <?= esc($stat['color']) ?>">

                                    <i class="bi <?= esc($stat['icon']) ?>"></i>

                                </div>

                                <span class="stat-indicator">
                                    Statistics
                                </span>

                            </div>


                            <div class="stat-card-content">

                                <p><?= esc($stat['title']) ?></p>

                                <h3>
                                    <?= number_format((float) $stat['value']) ?>
                                </h3>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="col-12">

                    <div class="dashboard-empty">

                        <i class="bi bi-info-circle"></i>

                        <p>
                            No statistics are currently available for this role.
                        </p>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?= $this->endSection() ?>