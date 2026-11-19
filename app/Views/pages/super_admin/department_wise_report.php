<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
/*
 * Column config: [header, key, link path|null, pending type 1/2/3|null]
 * Link paths are the CI4 versions of the old Super_admin/... methods.
 * Change them to match your routes.
 */
$p = [
    'department'  => 'super-admin/department-wise-count',
    'inprogress'  => 'super-admin/inprogress-applications-dep-wise-list',
    'sanction'    => 'super-admin/sanction-applications-dep-wise-list',
    'pending'     => 'super-admin/view-pending-department-list',
    'employment'  => 'super-admin/employment-applications-dep-wise-list',
    'training'    => 'super-admin/training-applications-dep-wise-list',
    'honorarium'  => 'super-admin/honorarium-applications-dep-wise-list',
    'loan'        => 'super-admin/loan-applications-dep-wise-list',
    'disbursed'   => 'super-admin/loan-disbursed-applications-dep-wise-list',
];

if ($version === '2') {
    // Version 2 layout
    $columns = [
        ['Forwarded To Department',                              'applicationreceived',            null,            null],
        ['Unattended',                                           'UnattendedAppli',                null,            null],
        ['InProgress',                                           'applicationinprogress',          $p['inprogress'], null],
        ['Rejected by the Department',                           'applicationinrejected',          null,            null],
        ['Approved',                                             'ApplicationsSanction',           $p['sanction'],  null],
        ['No action after approval',                             'ApplicationsSanction_no_action', $p['pending'],   1],
        ['Applicant Not Interested',                             'applicationinnotinterested',     null,            null],
        ['Wage Employment',                                      'employmentapplication',          $p['employment'], null],
        ['Training',                                             'trainingapplication',            $p['training'],  null],
        ['Honorarium',                                           'honorariumGivenList',            $p['honorarium'], null],
        ['Self-Employment (Asset/Subsidy/Store Opening/Loan)',   'LoanApplicationByDept',          null,            null],
        ['Forwarded To Bank',                                    'fwdbank',                        null,            null],
        ['No action taken by bank/Pending for loan sanction',    'fwdbank_no_action',              $p['pending'],   2],
        ['Loan Sanctioned',                                      'loanapplication',                $p['loan'],      null],
        ['Pending for Disbursal',                                'pending_disbursal',              $p['pending'],   3],
        ['Loan Disbursed',                                       'LoanDisbursed',                  $p['disbursed'], null],
        ['Rejected By Bank',                                     'applicationinrejectedbybank',    null,            null],
    ];
} else {
    // Version 1 / all versions layout
    $columns = [
        ['Forwarded To Department',          'applicationreceived',            null,            null],
        ['Unattended',                       'UnattendedAppli',                null,            null],
        ['InProgress',                       'applicationinprogress',          $p['inprogress'], null],
        ['Rejected by the Department',       'applicationinrejected',          null,            null],
        ['Approved',                         'ApplicationsSanction',           $p['sanction'],  null],
        ['No action after approval',         'ApplicationsSanction_no_action', $p['pending'],   1],
        ['Applicant Not Interested',         'applicationinnotinterested',     null,            null],
        ['Loan sanctioned by dept.',         'LoanApplicationByDept',          null,            null],
        ['Loan disbursed by dept.',          'LoanDisbursedByDept',            null,            null],
        ['Forwarded To Bank',                'fwdbank',                        null,            null],
        ['No action taken by bank',          'fwdbank_no_action',              $p['pending'],   2],
        ['Loan Sanctioned',                  'loanapplication',                $p['loan'],      null],
        ['Loan Disbursed',                   'LoanDisbursed',                  $p['disbursed'], null],
        ['Pending for Disbursal',            'pending_disbursal',              $p['pending'],   3],
        ['Rejected By Bank',                 'applicationinrejectedbybank',    null,            null],
        ['Wage Employment',                  'employmentapplication',          $p['employment'], null],
        ['Training',                         'trainingapplication',            $p['training'],  null],
        ['Honorarium',                       'honorariumGivenList',            $p['honorarium'], null],
    ];
}

/** Builds a link to a department list page. */
$link = static function (string $path, string $deptId, string $version, ?int $type = null): string {
    $query = ['de' => $deptId, 'v' => $version];

    if ($type !== null) {
        $query['type'] = $type;
    }

    return site_url($path) . '?' . http_build_query($query);
};
?>

<div class="dashboard-heading">
    <div>
        <h2>Department List</h2>
    </div>

    <div class="d-flex gap-2 align-items-center flex-wrap">
        <?= view('includes/mela_phase_filter', [
            'selected_version'    => $selected_version,
            'selected_mela_phase' => $selected_mela_phase,
            'mela_phases'         => $mela_phases,
        ]) ?>

        <?= view('includes/excel_export_button', ['filename' => 'department_wise_report']) ?>

        <a class="btn btn-outline-primary btn-sm"
            href="<?= site_url('super-admin/dashboard?v=' . esc($version, 'url')) ?>">
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
                    <th class="text-white text-center">Department</th>
                    <?php foreach ($columns as [$label]): ?>
                        <th class="text-white text-center"><?= esc($label) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php if (count($rows) < 2): ?>
                    <tr>
                        <td colspan="<?= count($columns) + 2 ?>" class="text-center text-muted py-4">
                            No data found.
                        </td>
                    </tr>
                <?php else: ?>

                    <?php $sr = 1; ?>
                    <?php foreach ($rows as $row): ?>
                        <?php
                        $deptId  = (string) $row['departmentid'];
                        $isTotal = $row['department'] === 'Total';
                        ?>
                        <tr class="<?= $isTotal ? 'fw-bold table-light' : '' ?>">

                            <td><?= $isTotal ? '' : $sr++ ?></td>

                            <td>
                                <?php if ($deptId !== ''): ?>
                                    <a href="<?= $link($p['department'], $deptId, $version) ?>">
                                        <?= esc($row['department']) ?>
                                    </a>
                                <?php else: ?>
                                    <?= esc($row['department']) ?>
                                <?php endif; ?>
                            </td>

                            <?php foreach ($columns as [$label, $key, $path, $type]): ?>
                                <td>
                                    <?php if ($path !== null && $deptId !== ''): ?>
                                        <a href="<?= $link($path, $deptId, $version, $type) ?>">
                                            <?= esc((string) $row[$key]) ?>
                                        </a>
                                    <?php else: ?>
                                        <?= esc((string) $row[$key]) ?>
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