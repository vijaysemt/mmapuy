<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
// [header, key]
$columns = [
    ['Total applications forwarded to bank',                             'Applicationsforwardedtobank'],
    ['Total applications loan sanction',                                 'Applicationsloansanction'],
    ['Total applications loan disbursed',                                'Applicationsloandisbursed'],
    ['Pending for disbursal',                                            'disbursal_pending'],
    ['Total applications rejected by bank',                              'ApplicationsRejectedbybank'],
    ['No action taken by bank',                                          'ApplicationsPending'],
    ['Total rejected applications pending for approval / disapproval at ADC level', 'adc_pending'],
];

// Page opened when a bank name is clicked (old: Super_admin/branch_wise_count). Adjust to your route.
$bankLink = site_url('super-admin/branch-wise-count');
?>

<div class="dashboard-heading">
    <div>
        <h2>Bank wise count</h2>
    </div>

    <div class="d-flex gap-2 align-items-center flex-wrap">
        <?= view('includes/district_select', [
            'district_list'     => $district_list,
            'selected_district' => $selected_district,
        ]) ?>

        <?= view('includes/mela_phase_filter', [
            'selected_version'    => $selected_version,
            'selected_mela_phase' => $selected_mela_phase,
            'mela_phases'         => $mela_phases,
            'extra'               => ['d' => $selected_district],
        ]) ?>

        <?= view('includes/excel_export_button', ['filename' => 'bank_wise_report']) ?>

        <a class="btn btn-outline-primary btn-sm"
            href="<?= site_url('super-admin/bank-dashboard?v=' . esc($version, 'url')) ?>">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="bg-white rounded-3 shadow-sm p-3">
    <div class="table-responsive table-static-header">
        <table class="table table-bordered table-hover align-middle text-center mt-1" id="table1">

            <thead class="bg-info text-white text-center">
                <tr class="bg-info">
                    <th class="text-white text-center">Sr. No</th>
                    <th class="text-white text-center">Bank Name</th>
                    <?php foreach ($columns as [$label]): ?>
                        <th class="text-white text-center"><?= esc($label) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php if (count($rows) < 2): ?>
                    <tr>
                        <td colspan="<?= count($columns) + 2 ?>" class="text-center text-muted py-4">No data found.</td>
                    </tr>
                <?php else: ?>

                    <?php $sr = 1; ?>
                    <?php foreach ($rows as $row): ?>
                        <?php $isTotal = $row['bankname'] === 'Total'; ?>
                        <tr class="<?= $isTotal ? 'fw-bold table-light' : '' ?>">
                            <td><?= $isTotal ? '' : $sr++ ?></td>

                            <td>
                                <?php if ($row['id'] !== ''): ?>
                                    <a href="<?= $bankLink . '?' . http_build_query(['b' => $row['id'], 'v' => $version]) ?>">
                                        <?= esc($row['bankname']) ?>
                                    </a>
                                <?php else: ?>
                                    <?= esc($row['bankname']) ?>
                                <?php endif; ?>
                            </td>

                            <?php foreach ($columns as [$label, $key]): ?>
                                <td><?= esc((string) $row[$key]) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>

        </table>
    </div>
</div>

<?= $this->endSection() ?>
