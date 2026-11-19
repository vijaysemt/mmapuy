<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
/*
 * Column config: [header, key, display rule, shown only for these categories (null = always), pending-list type]
 *
 * Display rules (same as the old view):
 *   text     plain value
 *   bank     "N/A" for department 4 / scheme 15
 *   dept4    "N/A" for department 4
 *   training "N/A" for department 4, or for category 4
 *   job      value only for department 13 / scheme 69, otherwise "N/A"
 *   dash     "-" when the value is 0
 */
$loanTypes = [1, 6];

$allColumns = [
    ['Department Name',                 'department_name',               'text',     null,            null],
    ['Schemes Name',                    'scheme_name',                   'text',     null,            null],
    ['Forwarded To Department',         'applicationreceived',           'text',     null,            null],
    ['Unattended',                      'UnattendedAppli',               'text',     null,            null],
    ['InProgress',                      'applicationinprogress',         'text',     null,            null],
    ['Rejected by department',          'applicationinrejected',         'text',     null,            null],
    ['Approved',                        'ApplicationsSanction',          'text',     null,            null],
    ['No action by dept after approval', 'ApplicationsPendingDept',      'text',     null,            1],
    ['Application Not Interested',      'applicationinNotInterested',    'text',     null,            null],
    ['Forwarded To Bank',               'fwdbank',                       'bank',     null,            null],
    ['No action taken by bank',         'bankpending',                   'bank',     null,            2],
    ['Loan Sanctioned',                 'loanapplication',               'bank',     null,            null],
    ['Loan Disbursed' . (in_array($type, $loanTypes, true) ? ' (1st installment)' : ''),
                                        'LoanDisbursed_1st',             'bank',     null,            null],
    ['Pending for disbursal',           'ApplicationsPendingDisburse',   'bank',     null,            3],
    ['Rejected By Bank',                'applicationinrejectedbybank',   'bank',     null,            null],
    ['Wage Employment',                 'employmentapplication',         'dept4',    null,            null],
    ['Training Started',                'trainingstartedapplication',    'text',     [3],             null],
    ['Training Completed',              'trainingcompletedapplication',  'training', null,            null],
    ['Honorarium',                      'honorariumGivenList',           'dept4',    null,            null],
    ['Job Joined',                      'job_joined',                    'job',      null,            null],
    ['Purchasing done 1st',             'purchasing_done_1st',           'dash',     $loanTypes,      null],
    ['Loan Disbursed (2nd installment)', 'LoanDisbursed_2nd',            'dash',     $loanTypes,      null],
    ['Subsidy released 1st',            'subsidy_released_1st',          'dash',     [1, 6, 3],       null],
    ['Purchasing done 2nd',             'purchasing_done_2nd',           'dash',     $loanTypes,      null],
    ['Subsidy released 2nd',            'subsidy_released_2nd',          'dash',     $loanTypes,      null],
    ['Assets released',                 'assets_released',               'dash',     $loanTypes,      null],
    ['Final benefit given',             'finial_benefit_given',          'dash',     $loanTypes,      null],
];

$columns = array_values(array_filter(
    $allColumns,
    static fn(array $c): bool => $c[3] === null || in_array($type, $c[3], true)
));

/** Value to print for one cell. */
$cell = static function (array $row, string $key, string $rule) use ($type): string {
    $dept   = (string) $row['newdepart_id'];
    $scheme = (string) $row['newschm_id'];
    $value  = (string) ($row[$key] ?? '');

    return match ($rule) {
        'bank'     => ($dept === '4' && $scheme === '15') ? 'N/A' : $value,
        'dept4'    => $dept === '4' ? 'N/A' : $value,
        'training' => ($dept === '4' || $type === 4) ? 'N/A' : $value,
        'job'      => ($dept === '13' && $scheme === '69') ? $value : 'N/A',
        'dash'     => ((int) $value) !== 0 ? $value : '-',
        default    => $value,
    };
};

// Page opened from the three "pending" columns (old: Super_admin/view_pending_department_list)
$pendingUrl = site_url('super-admin/view-pending-department-list');

[$english, $hindi] = $title;
?>

<div class="dashboard-heading">
    <div>
        <h2><?= esc($english) ?></h2>
        <p><?= esc($hindi) ?></p>
    </div>

    <div class="d-flex gap-2 align-items-center flex-wrap">
        <?= view('includes/excel_export_button', ['filename' => 'scheme_wise_report_type_' . $type]) ?>

        <a class="btn btn-outline-secondary btn-sm"
            href="<?= current_url() . '?' . http_build_query(['type' => $type, 'v' => $version, 'refresh' => 1]) ?>">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </a>

        <a class="btn btn-outline-primary btn-sm"
            href="<?= site_url('super-admin/scheme-report-dashboard?v=' . esc($version, 'url')) ?>">
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
                    <?php foreach ($columns as [$label]): ?>
                        <th class="text-white text-center"><?= esc($label) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php if ($rows === []): ?>
                    <tr>
                        <td colspan="<?= count($columns) + 1 ?>" class="text-center text-muted py-4">No data found.</td>
                    </tr>
                <?php else: ?>

                    <?php foreach ($rows as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>

                            <?php foreach ($columns as [$label, $key, $rule, $onlyTypes, $pendingType]): ?>
                                <?php $text = $cell($row, $key, $rule); ?>
                                <td>
                                    <?php if ($pendingType !== null): ?>
                                        <a href="<?= $pendingUrl . '?' . http_build_query([
                                                        'de'        => $row['newdepart_id'],
                                                        'type'      => $pendingType,
                                                        'scheme_id' => $row['newschm_id'],
                                                        'v'         => $version,
                                                    ]) ?>">
                                            <?= esc($text) ?>
                                        </a>
                                    <?php else: ?>
                                        <?= esc($text) ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>

        </table>
    </div>
</div>

<?= $this->endSection() ?>