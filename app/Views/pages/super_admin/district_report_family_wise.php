<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
// [header, key, pending-list type (1/2/3) or null]
$columns = [
    ['Total Scheduled',                          'EligibleFamilycount',         null],
    ['Families Surveyed',                        'FamilySurveyed',              null],
    ['Families Opted No Scheme',                 'noscheme',                    null],
    ['Applications Forwarded To Department',     'ApplicationsReceived',        null],
    ['Unattended',                               'Applicationsunattended',      null],
    ['Applications InProgress',                  'ApplicationsInprogress',      null],
    ['Applications rejected by department',      'ApplicationsRejected',        null],
    ['Applications Approved',                    'ApplicationsSanction',        null],
    ['No action by department after approval',   'apporved_no_action',          1],
    ['Applicant Not Interested',                 'not_interested_by_dept',      null],
    ['Wage Employment given',                    'wage_emp',                    null],
    ['Training given',                           'training',                    null],
    ['Honorarium given',                         'honorarium',                  null],
    ['Loan sanctioned by dept.',                 'LoanApplicationByDept',       null],
    ['Loan disbursed by dept.',                  'LoanDisbursedByDept',         null],
    ['Applications Forwarded To Bank',           'forwardtobank',               null],
    ['No action taken by bank',                  'forwarded_to_bank_no_action', 2],
    ['Loan Sanctioned',                          'LoanApplication',             null],
    ['Loan Disbursed',                           'LoanDisbursed',               null],
    ['Pending for Disbursal',                    'pending_disbursal',           3],
    ['Rejected By Bank',                         'Rejected_by_bank',            null],
];

$tableUrls = [
    'district' => $urls['district_family'],
    'pending'  => $urls['pending'],
];
?>

<div class="dashboard-heading">
    <div>
        <h2>Family wise list</h2>
    </div>

    <div class="d-flex gap-2 align-items-center">
        <a class="btn btn-outline-secondary btn-sm"
            href="<?= current_url() . '?' . http_build_query(['v' => $version, 'refresh' => 1]) ?>">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </a>
          <?= view('includes/excel_export_button', ['filename' => 'family_wise_report']) ?>
        <a class="btn btn-outline-primary btn-sm"
            href="<?= site_url('super-admin/district-list?v=' . esc($version, 'url')) ?>">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="bg-white rounded-3 shadow-sm p-3">
    <?= view('pages/super_admin/partials/district_report_table', [
        'columns' => $columns,
        'rows'    => $rows,
        'version' => $version,
        'urls'    => $tableUrls,
        'fType'   => 1,
    ]) ?>
</div>

<?= $this->endSection() ?>