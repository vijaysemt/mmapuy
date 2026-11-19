<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
// Text columns first, then number columns: [header, key]
$textColumns = [
    ['Bank Name',   'bankname'],
    ['Branch Name', 'branch_name'],
    ['District',    'district'],
];

// The Mela Phase column is shown for version 1 / all versions only (same as the old view).
if ($version !== '2') {
    $textColumns[] = ['Mela Phase', 'mela_phase'];
}

$numberColumns = [
    ['Total applications forwarded to bank',                    'Applications_forwarded_to_bank'],
    ['Total applications forwarded to bank no action taken',    'Applications_forwarded_to_bank_no_action'],
    ['Total applications loan sanction',                        'Applications_loan_sanctioned'],
    ['Total applications loan disbursed',                       'Applications_loan_disbursed'],
    ['Total applications pending for disbursal',                'Applications_loan_disbursed_no_action'],
    ['Total applications rejected by bank',                     'Applications_Rejected_by_bank'],
    ['Total applications pending at adc',                       'adc_pending'],
];

$colCount = 1 + count($textColumns) + count($numberColumns);
?>

<div class="dashboard-heading">
    <div>
        <h2>Bank Branch wise count</h2>
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

        <?= view('includes/excel_export_button', ['filename' => 'branch_wise_report']) ?>

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
                    <?php foreach ([...$textColumns, ...$numberColumns] as [$label]): ?>
                        <th class="text-white text-center"><?= esc($label) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php if (count($rows) < 2): ?>
                    <tr>
                        <td colspan="<?= $colCount ?>" class="text-center text-muted py-4">No data found.</td>
                    </tr>
                <?php else: ?>

                    <?php $sr = 1; ?>
                    <?php foreach ($rows as $row): ?>
                        <?php $isTotal = $row['bankname'] === 'Total'; ?>
                        <tr class="<?= $isTotal ? 'fw-bold table-light' : '' ?>">
                            <td><?= $isTotal ? '' : $sr++ ?></td>

                            <?php foreach ([...$textColumns, ...$numberColumns] as [$label, $key]): ?>
                                <td><?= esc((string) ($row[$key] ?? '')) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>

        </table>
    </div>
</div>

<?= $this->endSection() ?>
