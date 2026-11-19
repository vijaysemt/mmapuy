<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
// [header, key, pending-list type (1/2/3) or null]
if ($version === '2') {
    // Version 2 layout
    $columns = [
        ['Total families invited',                                   'FamilyCount',                 null],
        ['Families Counselled',                                      'MembersSurveyed',             null],
        ['Families opted no scheme',                                 'noscheme',                    null],
        ['Total forwarded to department',                            'ApplicationsReceived',        null],
        ['Applications unattended',                                  'Applicationsunattended',      null],
        ['Applications in-progress',                                 'ApplicationsInprogress',      null],
        ['Applications rejected by department',                      'Rejected_by_dept',            null],
        ['Applications approved',                                    'ApplicationsSanction',        null],
        ['No action by department after approval',                   'apporved_no_action',          1],
        ['Applicant Not Interested',                                 'not_interested_by_dept',      null],
        ['Wage employment given',                                    'wage_emp',                    null],
        ['Training Given',                                           'training_given',              null],
        ['Honorarium Given',                                         'honorarium_given',            null],
        ['Self-Employment (Asset/Subsidy/Store Opening/Loan)',       'LoanApplicationByDept',       null],
        ['Applications Forwarded To Bank',                           'forwarded_to_bank',           null],
        ['No action taken by bank/Pending for loan sanction',        'forwarded_to_bank_no_action', 2],
        ['Loan sanctioned',                                          'LoanApplication',             null],
        ['Pending for loan disbursal',                               'pending_disbursal',           3],
        ['Loan disbursed',                                           'LoanDisbursed',               null],
        ['Applications rejected by bank',                            'recjeted_by_bank',            null],
    ];
} else {
    // Version 1 / all versions layout
    $columns = [
        ['Total zones',                                  'ZoneName',                    null],
        ['Total families',                               'FamilyCount',                 null],
        ['Total families surveyed (attended mela)',      'FamilySurveyed',              null],
        ['Total members surveyed (attended mela)',       'MembersSurveyed',             null],
        ['Total members forwarded to department',        'ApplicationsReceived',        null],
        ['Applications unattended',                      'Applicationsunattended',      null],
        ['Members opted for noscheme',                   'noscheme',                    null],
        ['Applications in-progress',                     'ApplicationsInprogress',      null],
        ['Applications approved',                        'ApplicationsSanction',        null],
        ['Applications rejected by department',          'Rejected_by_dept',            null],
        ['No action by department after approval',       'apporved_no_action',          1],
        ['Applicant Not Interested',                     'not_interested_by_dept',      null],
        ['Wage employment given',                        'wage_emp',                    null],
        ['Training Given',                               'training_given',              null],
        ['Honorarium Given',                             'honorarium_given',            null],
        ['Loan sanctioned by dept.',                     'LoanApplicationByDept',       null],
        ['Loan disbursed by dept.',                      'LoanDisbursedByDept',         null],
        ['Applications Forwarded To Bank',               'forwarded_to_bank',           null],
        ['No Action taken by bank',                      'forwarded_to_bank_no_action', 2],
        ['Loan sanctioned',                              'LoanApplication',             null],
        ['Loan disbursed',                               'LoanDisbursed',               null],
        ['Pending for disbursal',                        'pending_disbursal',           3],
        ['Applications rejected by bank',                'recjeted_by_bank',            null],
    ];
}

$tableUrls = [
    'district' => $urls['district_member'],
    'pending'  => $urls['pending'],
];
?>

<div class="dashboard-heading">
    <div>
        <h2>Member wise list</h2>
    </div>

    <div class="d-flex gap-2 align-items-center">
      
        <?= view('includes/mela_phase_filter', [
            'selected_version'    => $selected_version,
            'selected_mela_phase' => $selected_mela_phase,
            'mela_phases'         => $mela_phases,
        ]) ?>
          <?= view('includes/excel_export_button', ['filename' => 'member_wise_report']) ?>
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
        'fType'   => 2,
    ]) ?>
</div>

<?= $this->endSection() ?>