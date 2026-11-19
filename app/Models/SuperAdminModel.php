<?php

namespace App\Models;

use CodeIgniter\Model;

class SuperAdminModel extends Model
{
    protected $db;


    private const MS_FROM = 'member_schemes ms INNER JOIN district d ON ms.district_id = d.id';

    private const ACTIVE = "ms.member_scheme_active = '1'";

    /** Columns of the family-wise report (order = order of the generated UNION). */
    private const FAMILY_COLUMNS = [
        'DistrictCount',
        'EligibleFamilycount',
        'FamilySurveyed',
        'ApplicationsReceived',
        'Applicationsunattended',
        'ApplicationsInprogress',
        'ApplicationsRejected',
        'ApplicationsSanction',
        'noscheme',
        'LoanApplication',
        'LoanDisbursed',
        'forwardtobank',
        'wage_emp',
        'training',
        'honorarium',
        'apporved_no_action',
        'forwarded_to_bank_no_action',
    ];

    private const MEMBER_COLUMNS = [
        'DistrictCount',
        'FamilyCount',
        'FamilySurveyed',
        'MembersSurveyed',
        'Applicationsunattended',
        'ApplicationsReceived',
        'ApplicationsInprogress',
        'ApplicationsSanction',
        'ZoneName',
        'noscheme',
        'LoanApplication',
        'LoanDisbursed',
        'LoanApplicationByDept',
        'LoanDisbursedByDept',
        'Rejected_by_dept',
        'not_interested_by_dept',
        'apporved_no_action',
        'wage_emp',
        'training_given',
        'honorarium_given',
        'forwarded_to_bank',
        'forwarded_to_bank_no_action',
        'pending_disbursal',
        'recjeted_by_bank',
    ];

    public function __construct()
    {
        parent::__construct();
        helper('version');
        $this->db = \Config\Database::connect();
    }

    /**
     * Main dashboard statistics.
     */
    public function getDashboardStats(string $version): array
    {
        if (!in_array($version, ['1', '2'], true)) {
            throw new \InvalidArgumentException('Invalid dashboard version.');
        }

        $administration = $this->getAdministrationStats($version);

        // These reported-family counts are shown in both versions.
        $reported = $this->getReportedStats($version);

        if ($version === '2') {
            return [
                'version'         => $version,
                'administration'  => $administration,
                'reported'        => $reported,
            ];
        }

        return [
            'version'         => $version,
            'administration'  => $administration,
            'families'        => $this->getFamilyStats($version),
            'members'         => $this->getMemberStats($version),
            'applications'    => $this->getApplicationStats($version),
            'reported'        => $reported,
        ];
    }

    /**
     * Execute a count query and return an integer.
     */
    private function countQuery(string $sql, array $binds = []): int
    {
        $query = $this->db->query($sql, $binds);

        $row = $query->getRowArray();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Validate version values and return an array.
     *
     * Supports "1", "2", or "1,2".
     */
    private function getVersions(string $version): array
    {
        $versions = array_map('trim', explode(',', $version));

        $versions = array_filter(
            $versions,
            static fn($value) => in_array($value, ['1', '2'], true)
        );

        $versions = array_values(array_unique($versions));

        if (empty($versions)) {
            throw new \InvalidArgumentException('Invalid app version.');
        }

        return $versions;
    }

    /**
     * Administration statistics.
     */
    private function getAdministrationStats($version): array
    {
        $departmentCount = $this->countQuery(
            "SELECT COUNT(*) AS total
             FROM departments_list
             WHERE `status` = ?",
            ['1']
        );

        $schemeCount = $this->countQuery(
            "SELECT COUNT(*) AS total
             FROM scheme_list
             WHERE `status` = ?",
            ['1']
        );

        $formedZones = $this->countQuery(
            "SELECT COUNT(*) AS total
             FROM alloted_zones
             WHERE district_id != ?",
            ['25']
        );

        $totalZones = $this->countQuery(
            "SELECT COUNT(DISTINCT Zonecode) AS total
             FROM new_masters
             WHERE DistrictSeccode != ?",
            ['25']
        );

        return [
            'districts'         => 22,
            'departments'       => $departmentCount,
            'schemes'           => $schemeCount,
            'formed_zones'      => $formedZones,
            'total_zones'       => $totalZones,
            'rejection_pending'          => $this->getRejectionRequestCount($version, 1),
            'rejection_approved'         => $this->getRejectionRequestCount($version, 2),
            'rejection_disapproved'      => $this->getRejectionRequestCount($version, 3),

            'not_interested_pending'     => $this->getRejectionRequestCount($version, 1, 5),
            'not_interested_approved'    => $this->getRejectionRequestCount($version, 2, 5),
            'not_interested_disapproved' => $this->getRejectionRequestCount($version, 3, 5),

            'joined_hkrn_applications'   => $this->getJoinedHkrnApplications(),
            'approved_harhit_applications' => $this->getApprovedHarhitApplications(),
            'npa_marked'                 => $this->getNpaMarkedCount($version),
        ];
    }

    /**
     * Family statistics.
     */
    private function getFamilyStats(string $version): array
    {
        $versions = $this->getVersions($version);
        $placeholders = implode(',', array_fill(0, count($versions), '?'));

        $familyCount = $this->countQuery(
            "SELECT COUNT(DISTINCT family_id) AS total
             FROM member_schemes
             WHERE member_scheme_active = ?
             AND app_version IN ($placeholders)",
            array_merge(['1'], $versions)
        );

        $applicationFamilies = $this->countQuery(
            "SELECT COUNT(DISTINCT family_id) AS total
             FROM member_schemes
             WHERE member_scheme_active = ?
             AND noscheme = ?
             AND app_version IN ($placeholders)",
            array_merge(['1', '0'], $versions)
        );

        $inProgressFamilies = $this->countQuery(
            "SELECT COUNT(DISTINCT msa.family_id) AS total
             FROM member_schemes msa
             WHERE msa.member_scheme_active = '1'
             AND msa.sent_approve = '1'
             AND msa.sention_status = '0'
             AND msa.app_version IN ($placeholders)
             AND msa.family_id NOT IN (
                 SELECT DISTINCT ms.family_id
                 FROM member_schemes ms
                 WHERE ms.member_scheme_active = '1'
                 AND ms.sent_approve = '1'
                 AND ms.sention_status = '1'
             )",
            $versions
        );
        // Eligible families
        $eligibleFamilies = $this->getEligibleFamilies();
        return [
            'profiled_families'       => $familyCount,
            'application_families'    => $applicationFamilies,
            'inprogress_families'     => $inProgressFamilies,
            'eligible_familiess'        => $eligibleFamilies,
            'unattended_families'          => $this->getUnattendedFamilies($version),
            'noschemeopted_families'       => $this->getNoSchemeOptedFamilies(),
            'sanctioned_families'          => $this->getSanctionedFamilies($version),
            'forwarded_tobank_families'    => $this->getForwardedToBankFamilies($version),
            'loan_sanction_families'       => $this->getLoanSanctionFamilies($version),
            'loan_disbursed_families'      => $this->getLoanDisbursedFamilies($version),
            'rejected_family'              => $this->getRejectedFamilies(1, $version),
            'not_interested_family'        => $this->getRejectedFamilies(2, $version),
            'not_trace_family'             => $this->getRejectedFamilies(3, $version),
            'rejected_bybank_family'       => $this->getRejectedByBankFamilies(),
            'wage_employment_family'       => $this->getWageEmploymentFamilies(),
            'total_private_sector'         => $this->getTotalPrivateSectorFamilies(),
        ];
    }

    /**
     * Member statistics.
     */
    private function getMemberStats(string $version): array
    {
        $versions = $this->getVersions($version);
        $placeholders = implode(',', array_fill(0, count($versions), '?'));

        // Profiled members
        $profiledMembers = $this->countQuery(
            "SELECT COUNT(DISTINCT member_id) AS total
         FROM member_schemes
         WHERE member_scheme_active = ?
           AND app_version IN ($placeholders)",
            array_merge(['1'], $versions)
        );

        // Eligible members (original CI3 method)
        $eligibleMembers = $this->getEligibleMembers($version);

        // Members in progress
        $inProgressMembers = $this->countQuery(
            "SELECT COUNT(DISTINCT msa.member_id) AS total
         FROM member_schemes msa
         WHERE msa.member_scheme_active = '1'
           AND msa.sent_approve = '1'
           AND msa.sention_status = '0'
           AND msa.app_version IN ($placeholders)
           AND msa.member_id NOT IN (
               SELECT DISTINCT ms.member_id
               FROM member_schemes ms
               WHERE ms.member_scheme_active = '1'
                 AND ms.sent_approve = '1'
                 AND ms.sention_status = '1'
           )",
            $versions
        );

        /*
     * The following methods preserve the conditions
     * from the original CI3 Mission_model.
     */
        $applicationsReceived = $this->getApplicationsReceived($version);
        $unattendedMembers    = $this->getUnattendedMembers($version);
        $noSchemeMembers     = $this->getNoSchemeMembers($version);

        $sanctionedMembers   = $this->getSanctionedMembers($version);
        $forwardedToBank     = $this->getForwardedToBankMembers($version);
        $loanSanctionMembers = $this->getLoanSanctionMembers($version);

        $rejectedMembers     = $this->getRejectedMembers(1, $version);
        $notInterested       = $this->getRejectedMembers(2, $version);
        $notTraced           = $this->getRejectedMembers(3, $version);

        $loanDisbursed       = $this->getLoanDisbursedMembers($version);

        return [
            'profiled_members'          => $profiledMembers,
            'eligible_members'          => $eligibleMembers,
            'inprogress_members'        => $inProgressMembers,

            // Application workflow
            'applications_received'     => $applicationsReceived,
            'unattended_members'        => $unattendedMembers,
            'noscheme_members'          => $noSchemeMembers,

            // Sanction / bank workflow
            'sanctioned_members'        => $sanctionedMembers,
            'forwarded_to_bank_members' => $forwardedToBank,
            'loan_sanction_members'     => $loanSanctionMembers,
            'rejected_member'           => $rejectedMembers,
            'not_interested_member'     => $notInterested,
            'not_trace_member'          => $notTraced,
            'loan_disbursed_members'    => $loanDisbursed,
        ];
    }
    /**
     * Applications received.
     * Preserves the original CI3 first-record-per-member logic.
     */
    private function getApplicationsReceived(string $version): int
    {
        $verAnd = build_version_condition($version);

        $sql = "SELECT COUNT(DISTINCT ms.member_id) AS total
            FROM member_schemes ms
            INNER JOIN district d ON ms.district_id = d.id
            WHERE ms.id IN (
                SELECT MIN(ms2.id)
                FROM member_schemes ms2
                WHERE ms2.member_scheme_active = '1'
                  AND ms2.noscheme = '0'
                GROUP BY ms2.member_id
            )
            AND ms.member_scheme_active = '1'
            AND ms.noscheme = '0'
            {$verAnd}";

        return $this->countQuery($sql);
    }


    /**
     * Unattended members.
     */
    private function getUnattendedMembers(string $version): int
    {
        $verAnd = build_version_condition($version);

        $sql = "SELECT SUM(Applicationsunattended) AS total
            FROM (
                SELECT COUNT(DISTINCT ms.member_id) AS Applicationsunattended
                FROM member_schemes ms
                INNER JOIN district d ON ms.district_id = d.id
                LEFT JOIN (
                    SELECT MIN(id) AS min_id
                    FROM member_schemes
                    WHERE member_scheme_active = '1'
                      AND noscheme = '0'
                    GROUP BY member_id
                ) min_schemes ON ms.id = min_schemes.min_id
                WHERE ms.member_scheme_active = '1'
                  AND ms.sent_approve = '0'
                  AND ms.reject_status = '0'
                  AND ms.noscheme = '0'
                  AND min_schemes.min_id IS NOT NULL
                  {$verAnd}
            ) AS t";

        return $this->countQuery($sql);
    }


    /**
     * Members having no scheme.
     */
    private function getNoSchemeMembers(string $version): int
    {
        $verAnd = build_version_condition($version);

        $sql = "SELECT COUNT(DISTINCT ms.member_id) AS total
            FROM member_schemes ms
            WHERE ms.member_id NOT IN (
                SELECT DISTINCT dms.member_id
                FROM member_schemes dms
                WHERE dms.noscheme = '0'
                  AND dms.member_scheme_active = '1'
            )
            AND ms.member_scheme_active = '1'
            AND ms.noscheme = '1'
            {$verAnd}";

        return $this->countQuery($sql);
    }


    /**
     * Sanctioned / approved members.
     */
    private function getSanctionedMembers(string $version): int
    {
        $verAnd = build_version_condition($version);
        $cond = get_ms_conditions($version);

        $sql = "SELECT COUNT(DISTINCT member_id) AS total
            FROM member_schemes ms
            WHERE member_scheme_active = '1'
              AND (
                  ms.sention_status = '1'
                  {$cond['rejected_after_approved_or']}
              )
              {$verAnd}";

        return $this->countQuery($sql);
    }


    /**
     * Members forwarded to bank.
     */
    private function getForwardedToBankMembers(string $version): int
    {
        $verAnd = build_version_condition($version);

        $sql = "SELECT COUNT(DISTINCT member_id) AS total
            FROM member_schemes
            WHERE forwarded_to_bank IS NOT NULL
            {$verAnd}";

        return $this->countQuery($sql);
    }


    /**
     * Loan sanctioned members.
     */
    private function getLoanSanctionMembers(string $version): int
    {
        $verAnd = build_version_condition($version);
        $cond = get_ms_conditions($version);

        $sql = "SELECT COUNT(DISTINCT member_id) AS total
            FROM member_schemes
            WHERE radiobuttonStatus {$cond['loan_sanction_in']}
            {$verAnd}";

        return $this->countQuery($sql);
    }


    /**
     * Rejected / not interested / not traced members.
     *
     * status:
     * 1 = Rejected
     * 2 = Not Interested
     * 3 = Not Traced
     */
    private function getRejectedMembers(int $status, string $version): int
    {
        $verAnd = build_version_condition($version);
        $cond = get_ms_conditions($version);

        if ($status === 2) {
            $sql = "SELECT SUM(total) AS total
                FROM (
                    SELECT COUNT(DISTINCT ms.member_id) AS total
                    FROM member_schemes ms
                    WHERE ms.member_scheme_active = '1'
                      {$cond['rejected_after_approved_and']}
                      AND ms.noscheme = '0'
                      {$verAnd}
                ) AS t";
        } else {
            $sql = "SELECT SUM(total) AS total
                FROM (
                    SELECT COUNT(DISTINCT ms.member_id) AS total
                    FROM member_schemes ms
                    WHERE ms.member_scheme_active = '1'
                      AND ms.reject_status = ?
                      AND ms.noscheme = '0'
                      {$verAnd}

                    UNION

                    SELECT COUNT(DISTINCT member_id) AS total
                    FROM member_schemes
                    WHERE sention_status = '1'
                      AND radiobuttonStatus = '5'
                      {$verAnd}
                ) AS t";

            return $this->countQuery($sql, [$status]);
        }

        return $this->countQuery($sql);
    }


    /**
     * Loan disbursed members.
     */
    private function getLoanDisbursedMembers(string $version): int
    {
        $verAnd = build_version_condition($version);
        $cond = get_ms_conditions($version);

        $sql = "SELECT COUNT(DISTINCT member_id) AS total
            FROM member_schemes
            WHERE radiobuttonStatus {$cond['loan_disbursed_in']}
            {$verAnd}";

        return $this->countQuery($sql);
    }

    /**
     * Application statistics.
     *
     * The first-record-per-member rule from the old application_received()
     * method is preserved.
     */
    private function getApplicationStats(string $version): array
    {
        $versions = $this->getVersions($version);
        $placeholders = implode(',', array_fill(0, count($versions), '?'));

        $applicationsReceived = $this->countQuery(
            "SELECT COUNT(DISTINCT ms.member_id) AS total
             FROM member_schemes ms
             INNER JOIN district d
                 ON ms.district_id = d.id
             WHERE ms.id IN (
                 SELECT MIN(ms2.id)
                 FROM member_schemes ms2
                 WHERE ms2.member_scheme_active = '1'
                 AND ms2.noscheme = '0'
                 GROUP BY ms2.member_id
             )
             AND ms.member_scheme_active = '1'
             AND ms.noscheme = '0'
             AND ms.app_version IN ($placeholders)",
            $versions
        );

        $familyApplicationsReceived = $this->countQuery(
            "SELECT COUNT(DISTINCT family_id) AS total
             FROM member_schemes
             WHERE member_scheme_active = '1'
             AND noscheme = '0'
             AND app_version IN ($placeholders)",
            $versions
        );

        $noSchemeFamilies = $this->countQuery(
            "SELECT COUNT(DISTINCT ms.family_id) AS total
             FROM member_schemes ms
             WHERE ms.member_scheme_active = '1'
             AND ms.noscheme = '1'
             AND ms.family_id NOT IN (
                 SELECT DISTINCT dms.family_id
                 FROM member_schemes dms
                 WHERE dms.noscheme = '0'
                 AND dms.member_scheme_active = '1'
             )"
        );

        return [
            'applications_received'        => $applicationsReceived,
            'family_applications_received'  => $familyApplicationsReceived,
            'no_scheme_families'            => $noSchemeFamilies,
        ];
    }

    /**
     * Reported-family statistics.
     */
    private function getReportedStats(string $version): array
    {
        $versions = $this->getVersions($version);
        $placeholders = implode(',', array_fill(0, count($versions), '?'));

        $incomeMismatch = $this->countQuery(
            "SELECT COUNT(DISTINCT family_id) AS total
             FROM report_member
             WHERE income_mismatch != ''
             AND app_version IN ($placeholders)",
            $versions
        );

        $splittedFamilies = $this->countQuery(
            "SELECT COUNT(DISTINCT family_id) AS total
             FROM report_member
             WHERE splitted_family != ''
             AND app_version IN ($placeholders)",
            $versions
        );

        $npaMarked = $this->countQuery(
            "SELECT COUNT(*) AS total
             FROM member_schemes
             WHERE radiobuttonStatus = '21'
             AND app_version IN ($placeholders)",
            $versions
        );

        return [
            'income_mismatch'  => $incomeMismatch,
            'splitted_families' => $splittedFamilies,
            'npa_marked'       => $npaMarked,
        ];
    }

    /**
     * Eligible Families
     *
     * Counts records from all four Mela eligibility tables.
     * Preserves the original UNION behavior.
     */
    private function getEligibleFamilies(): int
    {
        $sql = "SELECT SUM(total) AS total
        FROM (
            SELECT COUNT(*) AS total
            FROM tbl_mela_schedule_agewise_rec_new

            UNION

            SELECT COUNT(*) AS total
            FROM march_mela_schedule_eligible_famlies

            UNION

            SELECT COUNT(*) AS total
            FROM june_mela_families_scheduled

            UNION

            SELECT COUNT(*) AS total
            FROM mela_phase_4_eligible_families
        ) AS t
    ";

        $row = $this->db->query($sql)->getRowArray();

        return (int) ($row['total'] ?? 0);
    }


    /**
     * Eligible Members
     *
     * Excludes district code 25.
     * Age between 18 and 59.
     * Filters by dashboard version.
     */
    private function getEligibleMembers(string $version): int
    {
        $versions = $this->getVersions($version);

        $placeholders = implode(
            ',',
            array_fill(0, count($versions), '?')
        );

        $sql = "SELECT COUNT(memberID) AS total
        FROM family_details_new
        WHERE seccdCode != ?
          AND age BETWEEN ? AND ?
          AND app_version IN ($placeholders)
    ";

        $binds = array_merge(
            ['25', 18, 59],
            $versions
        );

        $row = $this->db->query($sql, $binds)->getRowArray();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Get unattended families
     */
    private function getUnattendedFamilies(string $version): int
    {
        $verAnd = build_version_condition($version, 't.app_version');

        $sql = "
        SELECT SUM(Applicationsunattended) AS totalunatt
        FROM (
            SELECT COUNT(DISTINCT t.family_id) AS Applicationsunattended
            FROM (
                SELECT
                    ms.family_id,
                    ms.member_id,
                    ms.district_id,
                    ms.reject_status,
                    ms.app_version
                FROM member_schemes ms
                WHERE ms.family_id NOT IN (
                    SELECT ms2.family_id
                    FROM member_schemes ms2
                    WHERE ms2.member_scheme_active = '1'
                      AND ms2.noscheme = '0'
                      AND (
                          ms2.reject_status = '1'
                          OR ms2.sent_approve = '1'
                      )
                )
                AND ms.member_scheme_active = '1'
                AND ms.noscheme = '0'
            ) t
            INNER JOIN district d ON t.district_id = d.id
            WHERE t.reject_status = '0'
            {$verAnd}
            GROUP BY d.dist_name
        ) AS result
    ";

        $query = $this->db->query($sql);
        $row = $query->getRowArray();
        return (int) ($row['totalunatt'] ?? 0);
    }


    /**
     * Get families that opted for no scheme
     */
    private function getNoSchemeOptedFamilies(): int
    {
        $sql = "
        SELECT COUNT(DISTINCT ms.family_id) AS total
        FROM member_schemes ms
        WHERE ms.family_id NOT IN (
            SELECT DISTINCT dms.family_id
            FROM member_schemes dms
            WHERE dms.noscheme = '0'
              AND dms.member_scheme_active = '1'
        )
        AND ms.member_scheme_active = '1'
        AND ms.noscheme = '1'
    ";

        $query = $this->db->query($sql);
        $row = $query->getRowArray();

        return (int) ($row['total'] ?? 0);
    }


    /**
     * Get sanctioned families
     */
    private function getSanctionedFamilies(string $version): int
    {
        $verAnd = build_version_condition($version, 'ms.app_version');
        $cond = get_ms_conditions($version);

        $sql = "
        SELECT COUNT(DISTINCT ms.family_id) AS total
        FROM member_schemes ms
        WHERE ms.member_scheme_active = '1'
          AND (
              ms.sention_status = '1'
              {$cond['rejected_after_approved_or']}
          )
          {$verAnd}
    ";

        $query = $this->db->query($sql);
        $row = $query->getRowArray();

        return (int) ($row['total'] ?? 0);
    }


    /**
     * Get families forwarded to bank
     */
    private function getForwardedToBankFamilies(string $version): int
    {
        $verAnd = build_version_condition($version, 'app_version');

        $sql = "
        SELECT COUNT(DISTINCT family_id) AS total
        FROM member_schemes
        WHERE forwarded_to_bank IS NOT NULL
        {$verAnd}
    ";

        $query = $this->db->query($sql);
        $row = $query->getRowArray();

        return (int) ($row['total'] ?? 0);
    }


    /**
     * Get loan sanctioned families
     */
    private function getLoanSanctionFamilies(string $version): int
    {
        $verAnd = build_version_condition($version, 'app_version');
        $cond = get_ms_conditions($version);

        $sql = "
        SELECT COUNT(DISTINCT family_id) AS total
        FROM member_schemes
        WHERE radiobuttonStatus {$cond['loan_sanction_in']}
        {$verAnd}
    ";

        $query = $this->db->query($sql);
        $row = $query->getRowArray();

        return (int) ($row['total'] ?? 0);
    }


    /**
     * Get loan disbursed families
     */
    private function getLoanDisbursedFamilies(string $version): int
    {
        $verAnd = build_version_condition($version, 'app_version');
        $cond = get_ms_conditions($version);

        $sql = "
        SELECT COUNT(DISTINCT family_id) AS total
        FROM member_schemes
        WHERE radiobuttonStatus {$cond['loan_disbursed_in']}
        {$verAnd}
    ";

        $query = $this->db->query($sql);
        $row = $query->getRowArray();

        return (int) ($row['total'] ?? 0);
    }


    /**
     * Get rejected / not interested / not traced families
     *
     * Status:
     * 1 = Rejected
     * 2 = Not Interested
     * 3 = Not Traced
     */
    private function getRejectedFamilies(int $status, string $version): int
    {
        $verAnd = build_version_condition($version, 'ms.app_version');
        $cond = get_ms_conditions($version);

        $sql = "
        SELECT COUNT(DISTINCT ms.family_id) AS total
        FROM member_schemes ms
        WHERE ms.family_id NOT IN (
            SELECT ms2.family_id
            FROM member_schemes ms2
            WHERE ms2.member_scheme_active = '1'
              AND ms2.reject_status = '0'
              AND ms2.noscheme = '0'
              AND (
                  ms2.sention_status = '1'
                  OR ms2.sent_approve = '1'
              )
        )
        AND ms.member_scheme_active = '1'
        AND ms.reject_status = ?
        AND ms.noscheme = '0'
        {$verAnd}
    ";

        if ($status === 2) {
            $sql = "
            SELECT COUNT(DISTINCT ms.family_id) AS total
            FROM member_schemes ms
            WHERE ms.family_id NOT IN (
                SELECT ms2.family_id
                FROM member_schemes ms2
                WHERE ms2.member_scheme_active = '1'
                  AND ms2.reject_status = '0'
                  AND ms2.noscheme = '0'
                  AND (
                      ms2.sention_status = '1'
                      OR ms2.sent_approve = '1'
                  )
            )
            AND ms.member_scheme_active = '1'
            {$cond['rejected_after_approved_and']}
            AND ms.noscheme = '0'
            {$verAnd}
        ";

            $query = $this->db->query($sql);
        } else {
            $query = $this->db->query($sql, [$status]);
        }

        $row = $query->getRowArray();

        return (int) ($row['total'] ?? 0);
    }


    /**
     * Get families rejected by bank
     */
    private function getRejectedByBankFamilies(): int
    {
        $sql = "
        SELECT COUNT(DISTINCT ms.family_id) AS total
        FROM member_schemes ms
        WHERE ms.member_scheme_active = '1'
          AND ms.reject_status = '1'
          AND ms.radiobuttonStatus = '5'
    ";

        $query = $this->db->query($sql);
        $row = $query->getRowArray();

        return (int) ($row['total'] ?? 0);
    }


    /**
     * Get wage employment families
     */
    private function getWageEmploymentFamilies(): int
    {
        $sql = "
        SELECT COUNT(DISTINCT ms.family_id) AS total
        FROM member_schemes ms
        WHERE ms.member_scheme_active = '1'
          AND ms.sent_approve = '1'
          AND ms.sention_status = '1'
          AND ms.radiobuttonStatus IN ('2', '9')
    ";

        $query = $this->db->query($sql);
        $row = $query->getRowArray();

        return (int) ($row['total'] ?? 0);
    }


    /**
     * Get private sector families
     *
     * Preserves original CI3 COUNT(family_id) behavior.
     */
    private function getTotalPrivateSectorFamilies(): int
    {
        $sql = "
        SELECT COUNT(ms.family_id) AS total
        FROM member_schemes ms
        WHERE ms.newschm_id = '69'
          AND ms.newdepart_id = '13'
          AND ms.member_scheme_active = '1'
          AND ms.sent_approve = '1'
          AND ms.radiobuttonStatus = '9'
          AND ms.sention_status = '1'
          AND ms.noscheme = '0'
    ";

        $query = $this->db->query($sql);
        $row = $query->getRowArray();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Phase 1 Mela statistics
     */
    public function getMelaStatsPhase1(): array
    {
        $sql = "
        SELECT SUM(dayDate) AS dayDate, SUM(Total) AS Total
        FROM (
            SELECT SUM(dayDate) AS dayDate, SUM(melaVenue) AS Total
            FROM (
                SELECT
                    COUNT(DISTINCT dayDate) AS dayDate,
                    COUNT(DISTINCT melaVenue) AS melaVenue
                FROM tbl_mela_schedule
                WHERE part < '50'
                GROUP BY melaVenue
            ) a

            UNION

            SELECT SUM(dayDate) AS dayDate, 0 AS Total
            FROM (
                SELECT
                    COUNT(DISTINCT dayDate) AS dayDate,
                    COUNT(DISTINCT melaVenue) AS melaVenue
                FROM tbl_follow_up_sechedule
                WHERE part < '50'
                GROUP BY melaVenue
            ) b
        ) t
    ";

        return $this->db->query($sql)->getRowArray() ?? [
            'dayDate' => 0,
            'Total'   => 0
        ];
    }


    /**
     * Phase 2 Mela statistics
     */
    public function getMelaStatsPhase2(): array
    {
        $sql = "
        SELECT
            SUM(days) AS mela_days,
            SUM(melaVenue) AS total_mela_venues,
            SUM(melas) AS melas
        FROM (
            SELECT
                0 AS days,
                COUNT(DISTINCT melaVenue) AS melaVenue,
                COUNT(DISTINCT start_date, melaVenue) AS melas
            FROM mela_schedule
            WHERE phase = '2'

            UNION

            SELECT
                SUM(start_date) AS days,
                0 AS melaVenue,
                0 AS melas
            FROM (
                SELECT DATEDIFF(end_date, start_date) + 1 AS start_date
                FROM mela_schedule
                WHERE phase = '2'
            ) g
        ) t
    ";

        return $this->db->query($sql)->getRowArray() ?? [
            'mela_days' => 0,
            'total_mela_venues' => 0,
            'melas' => 0
        ];
    }


    /**
     * Phase 3 Mela statistics
     */
    public function getMelaStatsPhase3(): array
    {
        $sql = "
        SELECT
            SUM(days) AS mela_days,
            SUM(melaVenue) AS total_mela_venues,
            SUM(melas) AS melas
        FROM (
            SELECT
                0 AS days,
                COUNT(DISTINCT melaVenue) AS melaVenue,
                COUNT(DISTINCT start_date, melaVenue) AS melas
            FROM mela_schedule
            WHERE phase = '3'
              AND start_date <= ?

            UNION

            SELECT
                SUM(start_date) AS days,
                0 AS melaVenue,
                0 AS melas
            FROM (
                SELECT DATEDIFF(end_date, start_date) + 1 AS start_date
                FROM mela_schedule
                WHERE phase = '3'
                  AND start_date <= ?
                GROUP BY start_date, melavenue
            ) g
        ) t
    ";

        $today = date('Y-m-d');

        return $this->db->query($sql, [$today, $today])->getRowArray() ?? [
            'mela_days' => 0,
            'total_mela_venues' => 0,
            'melas' => 0
        ];
    }


    /**
     * Harhit Mela statistics
     */
    public function getHarhitMelaStats(): array
    {
        $today = date('Y-m-d');

        $sql = "
        SELECT
            SUM(days) AS mela_days,
            SUM(melaVenue) AS total_mela_venues,
            SUM(melas) AS melas
        FROM (
            SELECT
                0 AS days,
                COUNT(DISTINCT location) AS melaVenue,
                COUNT(DISTINCT meladate, location) AS melas
            FROM december_mela_2022
            WHERE mela_phase != '1'
              AND meladate <= ?

            UNION

            SELECT
                SUM(start_date) AS days,
                0 AS melaVenue,
                0 AS melas
            FROM (
                SELECT DATEDIFF(meladate, meladate) + 1 AS start_date
                FROM december_mela_2022
                WHERE mela_phase != '1'
                  AND meladate <= ?
                GROUP BY meladate, location
            ) g
        ) t
    ";

        return $this->db->query($sql, [$today, $today])->getRowArray() ?? [
            'mela_days' => 0,
            'total_mela_venues' => 0,
            'melas' => 0
        ];
    }


    /**
     * Phase 4 Mela statistics
     */
    public function getMelaStatsPhase4(): array
    {
        $sql = "
        SELECT
            SUM(days) AS mela_days,
            SUM(melaVenue) AS total_mela_venues,
            SUM(melas) AS melas
        FROM (
            SELECT
                0 AS days,
                COUNT(DISTINCT melaVenue) AS melaVenue,
                COUNT(DISTINCT start_date, melaVenue) AS melas
            FROM mela_phase_4_schedule_id
            WHERE PHASE = '4'

            UNION

            SELECT
                SUM(start_date) AS days,
                0 AS melaVenue,
                0 AS melas
            FROM (
                SELECT DATEDIFF(end_date, start_date) + 1 AS start_date
                FROM mela_phase_4_schedule_id
                WHERE PHASE = '4'
                GROUP BY start_date, melavenue
            ) g
        ) t
    ";

        return $this->db->query($sql)->getRowArray() ?? [
            'mela_days' => 0,
            'total_mela_venues' => 0,
            'melas' => 0
        ];
    }

    /**
     * HKRN approved applications
     */
    public function getJoinedHkrnApplications(): int
    {
        $sql = "
        SELECT COUNT(DISTINCT member_id) AS total
        FROM member_schemes ms
        WHERE ms.member_scheme_active = '1'
          AND ms.sent_approve = '1'
          AND ms.sention_status = '1'
          AND ms.newdepart_id = '25'
          AND ms.newschm_id = '70'
    ";

        return (int) ($this->db->query($sql)->getRowArray()['total'] ?? 0);
    }


    /**
     * Approved Harhit applications
     */
    public function getApprovedHarhitApplications(): int
    {
        $sql = "
        SELECT COUNT(DISTINCT member_id) AS total
        FROM member_schemes ms
        WHERE ms.member_scheme_active = '1'
          AND ms.sent_approve = '1'
          AND ms.sention_status = '1'
          AND ms.newdepart_id = '19'
          AND ms.newschm_id = '42'
    ";

        return (int) ($this->db->query($sql)->getRowArray()['total'] ?? 0);
    }


    /**
     * Rejection request statistics
     *
     * $status:
     * 1 = Pending
     * 2 = Approved
     * 3 = Disapproved
     *
     * $requestType:
     * 5 = Not Interested
     * null = Normal rejection request (request_type != 5)
     */
    private function getRejectionRequestCount(
        string $version,
        int $status,
        ?int $requestType = null
    ): int {
        $versionCondition = build_version_condition($version, 'ms.app_version');

        $requestCondition = ($requestType === 5)
            ? "rd.request_type = '5'"
            : "rd.request_type != '5'";

        $sql = "
        SELECT SUM(total) AS total
        FROM (
            SELECT COUNT(*) AS total
            FROM (
                SELECT MAX(rr.id) AS max_id
                FROM reject_requests rr
                GROUP BY rr.ms_id
            ) max_ids
            INNER JOIN reject_requests rd
                ON max_ids.max_id = rd.id
               AND {$requestCondition}
            INNER JOIN member_schemes ms
                ON rd.ms_id = ms.id
               {$versionCondition}
            WHERE rd.status = ?
            GROUP BY rd.ms_id
        ) t
    ";

        $row = $this->db->query($sql, [$status])->getRowArray();

        return (int) ($row['total'] ?? 0);
    }


    /**
     * NPA marked count
     * Preserves original COUNT(*) behavior.
     */
    public function getNpaMarkedCount(string $version): int
    {
        $versionCondition = build_version_condition($version, 'ms.app_version');

        $sql = "
        SELECT COUNT(*) AS total
        FROM member_schemes ms
        WHERE ms.radiobuttonStatus = '21'
        {$versionCondition}
    ";

        return (int) ($this->db->query($sql)->getRowArray()['total'] ?? 0);
    }


    private function getHighlightStats(string $version): array
    {
        return [
            'joined_hkrn_applications'     => $this->getJoinedHkrnApplications(),
            'approved_harhit_applications' => $this->getApprovedHarhitApplications(),
            'npa_marked'                   => $this->getNpaMarkedCount($version),
        ];
    }

    //  new cloude code
    public function getGroupStats(string $group, string $version): array
    {
        if (!in_array($version, ['1', '2'], true)) {
            throw new \InvalidArgumentException('Invalid dashboard version.');
        }

        return match ($group) {
            'overview'     => ['administration' => $this->getOverviewStats()],
            'highlights' => ['administration' => $this->getHighlightStats($version)],
            'adc'          => ['administration' => $this->getAdcStats($version)],
            'mela'         => ['melaStats' => [
                'phase_1' => $this->getMelaStatsPhase1(),
                'phase_2' => $this->getMelaStatsPhase2(),
                'phase_3' => $this->getMelaStatsPhase3(),
                'harhit'  => $this->getHarhitMelaStats(),
                'phase_4' => $this->getMelaStatsPhase4(),
            ]],
            'families'     => ['families'     => $this->getFamilyStats($version)],
            'members'      => ['members'      => $this->getMemberStats($version)],
            'applications' => ['applications' => $this->getApplicationStats($version)],
            'reported'     => ['reported'     => $this->getReportedStats($version)],
            default        => throw new \InvalidArgumentException('Invalid group.'),
        };
    }

    /** Light counts: shown immediately on page load. */
    private function getOverviewStats(): array
    {
        return [
            'districts'    => 22,
            'departments'  => $this->countQuery("SELECT COUNT(*) AS total FROM departments_list WHERE `status` = ?", ['1']),
            'schemes'      => $this->countQuery("SELECT COUNT(*) AS total FROM scheme_list WHERE `status` = ?", ['1']),
            'formed_zones' => $this->countQuery("SELECT COUNT(*) AS total FROM alloted_zones WHERE district_id != ?", ['25']),
            'total_zones'  => $this->countQuery("SELECT COUNT(DISTINCT Zonecode) AS total FROM new_masters WHERE DistrictSeccode != ?", ['25']),
        ];
    }

    /** ADC submissions + HKRN/Harhit + NPA: heavy, loaded on tab click. */
    private function getAdcStats(string $version): array
    {
        return [
            'rejection_pending'            => $this->getRejectionRequestCount($version, 1),
            'rejection_approved'           => $this->getRejectionRequestCount($version, 2),
            'rejection_disapproved'        => $this->getRejectionRequestCount($version, 3),
            'not_interested_pending'       => $this->getRejectionRequestCount($version, 1, 5),
            'not_interested_approved'      => $this->getRejectionRequestCount($version, 2, 5),
            'not_interested_disapproved'   => $this->getRejectionRequestCount($version, 3, 5),
        ];
    }




    // ------------------------------------------------------------------
    // FAMILY WISE
    // ------------------------------------------------------------------

    /**
     * @return list<array<string,mixed>> district rows (sorted) followed by one "Total" row
     */
    public function getFamilyWise(string $version = '1'): array
    {
        $cond = get_ms_conditions($version);
        $act  = self::ACTIVE;

        $ms = static fn(string $expr, string $where): array => [
            'district' => 'd.dist_name',
            'expr'     => $expr,
            'from'     => self::MS_FROM,
            'where'    => $where,
            'group'    => 'd.dist_name',
        ];

        // Applications of a family that has no approved / rejected scheme (old "unattended" logic).
        $unattendedFrom = "(SELECT ms.family_id, ms.district_id, ms.reject_status
                            FROM member_schemes ms
                            WHERE NOT EXISTS (
                                SELECT 1 FROM member_schemes x
                                WHERE x.family_id = ms.family_id
                                  AND ((x.member_scheme_active = '1' AND x.noscheme = '0' AND x.reject_status = '1')
                                       OR x.sent_approve = '1')
                            )
                            AND ms.member_scheme_active = '1'
                            AND ms.noscheme = '0') t
                           INNER JOIN district d ON t.district_id = d.id";

        $branches = [
            'DistrictCount' => [
                'district' => 'd.dist_name',
                'id'       => 'MAX(d.id)',
                'expr'     => 'COUNT(*)',
                'from'     => 'district d',
                'group'    => 'd.dist_name',
            ],
            'EligibleFamilycount' => [
                'district' => 'p.districtName',
                'expr'     => 'SUM(p.cnt)',
                'from'     => $this->eligibleFamiliesFrom(),
                'group'    => 'p.districtName',
            ],
            'FamilySurveyed'       => $ms('COUNT(DISTINCT ms.family_id)', $act),
            'ApplicationsReceived' => $ms('COUNT(DISTINCT ms.family_id)', "{$act} AND ms.noscheme = '0'"),
            'Applicationsunattended' => [
                'district' => 'd.dist_name',
                'expr'     => 'COUNT(DISTINCT t.family_id)',
                'from'     => $unattendedFrom,
                'where'    => "t.reject_status = '0'",
                'group'    => 'd.dist_name',
            ],
            'ApplicationsInprogress' => $ms(
                'COUNT(DISTINCT ms.family_id)',
                "NOT EXISTS (SELECT 1 FROM member_schemes x
                             WHERE x.family_id = ms.family_id
                               AND x.member_scheme_active = '1'
                               AND x.sent_approve = '1'
                               AND x.sention_status = '1')
                 AND {$act} AND ms.sent_approve = '1' AND ms.sention_status = '0'"
            ),
            'ApplicationsRejected' => $ms(
                'COUNT(DISTINCT ms.family_id)',
                "NOT EXISTS (SELECT 1 FROM member_schemes x
                             WHERE x.family_id = ms.family_id
                               AND x.member_scheme_active = '1'
                               AND x.reject_status = '0'
                               AND x.noscheme = '0'
                               AND (x.sention_status = '1' OR x.sent_approve = '1'))
                 AND {$act} AND ms.reject_status IN ('1','2') AND ms.noscheme = '0'"
            ),
            'ApplicationsSanction' => $ms(
                'COUNT(DISTINCT ms.family_id)',
                "{$act} AND ms.sent_approve = '1' AND ms.sention_status = '1'"
            ),
            'noscheme' => $ms(
                'COUNT(DISTINCT ms.family_id)',
                "NOT EXISTS (SELECT 1 FROM member_schemes dms
                             WHERE dms.family_id = ms.family_id
                               AND dms.noscheme = '0'
                               AND dms.member_scheme_active = '1')
                 AND {$act} AND ms.noscheme = '1'"
            ),
            'LoanApplication' => $ms(
                'COUNT(DISTINCT ms.family_id)',
                "ms.radiobuttonStatus {$cond['loan_sanction_in']} AND {$act} AND ms.noscheme = '0'"
            ),
            'LoanDisbursed' => $ms(
                'COUNT(DISTINCT ms.family_id)',
                "ms.radiobuttonStatus {$cond['loan_disbursed_in']} AND {$act} AND ms.noscheme = '0'"
            ),
            'forwardtobank' => $ms(
                'COUNT(DISTINCT ms.family_id)',
                "ms.forwarded_to_bank IS NOT NULL AND {$act}"
            ),
            'wage_emp' => $ms(
                'COUNT(DISTINCT ms.family_id)',
                "{$act} AND ms.sent_approve = '1' AND ms.sention_status = '1'
                 AND ms.radiobuttonStatus IN ('2','9')"
            ),
            'training' => $ms(
                'COUNT(DISTINCT ms.family_id)',
                "{$act} AND ms.sent_approve = '1' AND ms.sention_status = '1'
                 AND (ms.radiobuttonStatus = '4' OR ms.training_honorarium_status = '2')"
            ),
            'honorarium' => $ms(
                'COUNT(DISTINCT ms.family_id)',
                "{$act} AND ms.sent_approve = '1' AND ms.sention_status = '1'
                 AND (ms.radiobuttonStatus = '6' OR ms.training_honorarium_status = '3')"
            ),
            'apporved_no_action' => $ms(
                'COUNT(DISTINCT ms.family_id)',
                "{$act} AND ms.sention_status = '1' AND ms.radiobuttonStatus IS NULL
                 AND ms.forwarded_to_bank IS NULL"
            ),
            'forwarded_to_bank_no_action' => $ms(
                'COUNT(DISTINCT ms.family_id)',
                "{$act} AND ms.sention_status = '1' AND ms.radiobuttonStatus IS NULL
                 AND ms.forwarded_to_bank IS NOT NULL"
            ),
        ];

        $rows = $this->runReport(self::FAMILY_COLUMNS, $branches, '');

        return $this->finalise(
            $rows,
            self::FAMILY_COLUMNS,
            static function (array $r): array {
                $r['pending_disbursal'] = ($r['LoanApplication'] ?? 0) - ($r['LoanDisbursed'] ?? 0);

                // These four columns are shown by the old view but were never calculated
                // by the old family-wise query. Kept as null so the view prints "—".
                $r['not_interested_by_dept'] = null;
                $r['LoanApplicationByDept']  = null;
                $r['LoanDisbursedByDept']    = null;
                $r['Rejected_by_bank']       = null;

                return $r;
            }
        );
    }

    /** Eligible families come from four mela tables, grouped by district name. */
    private function eligibleFamiliesFrom(): string
    {
        return "(
            SELECT fn.districtName AS districtName, COUNT(DISTINCT fn.familyid) AS cnt
            FROM tbl_mela_schedule_agewise_rec_new fn
            GROUP BY fn.districtName

            UNION ALL
            SELECT mf.districtName, COUNT(DISTINCT mf.familyID)
            FROM march_mela_schedule_eligible_famlies mf
            GROUP BY mf.districtName

            UNION ALL
            SELECT mf.districtName, COUNT(DISTINCT mf.familyID)
            FROM june_mela_scheduling_eligible_famlies_with_venue_copy2 mf
            WHERE mf.is_ladd = '1' AND mf.is_scheduled = '1'
            GROUP BY mf.districtName

            UNION ALL
            SELECT mf.districtName, COUNT(DISTINCT mf.familyID)
            FROM mela_phase_4_eligible_families mf
            GROUP BY mf.districtName
        ) p";
    }

    // ------------------------------------------------------------------
    // MEMBER WISE
    // ------------------------------------------------------------------

    /**
     * @return list<array<string,mixed>> district rows (sorted) followed by one "Total" row
     */
    public function getMemberWise(string $version, int $melaPhase = 0, int $isSsa = 0): array
    {
        $verAnd = build_version_condition($version);   // values are intval()'d
        $cond   = get_ms_conditions($version, $melaPhase);
        $act    = self::ACTIVE;

        // Version / phase conditions for family_details_new (fn) – same rules as the old code.
        $verFdn = $verAnd;

        if ($melaPhase > 0) {
            $verAnd .= " AND ms.mela_phase = {$melaPhase} AND ms.is_ssa = {$isSsa}";

            if ($isSsa !== 0) {
                $verFdn = " AND is_ssa_pushed = {$isSsa}";
            } else {
                $verFdn .= " {$cond['fdn_phase_and']} AND is_ssa_pushed = {$isSsa}";
            }
        } else {
            $verFdn = ' AND ((fn.app_version = 2 AND fn.PHASE IN (14, 16) AND fn.is_ssa_pushed = 0)'
                . ' OR fn.is_ssa_pushed = 1)';
        }

        $ms = static fn(string $expr, string $where): array => [
            'district' => 'd.dist_name',
            'expr'     => $expr,
            'from'     => self::MS_FROM,
            'where'    => $where,
            'group'    => 'd.dist_name',
        ];

        $cm = 'COUNT(DISTINCT ms.member_id)';

        $branches = [
            'DistrictCount' => [
                'district' => 'd.dist_name',
                'id'       => 'MAX(d.id)',
                'expr'     => 'COUNT(*)',
                'from'     => 'district d',
                'group'    => 'd.dist_name',
            ],
            'FamilyCount' => [
                'district' => 'fn.districtName',
                'expr'     => 'COUNT(DISTINCT fn.familyID)',
                'from'     => 'family_details_new fn',
                'where'    => "fn.hofName = 'H' {$verFdn}",
                'group'    => 'fn.districtName',
            ],
            'FamilySurveyed' => $ms('COUNT(DISTINCT ms.family_id)', "{$act} {$verAnd}"),
            'MembersSurveyed' => $ms($cm, "{$act} {$cond['no_schm_and']} {$verAnd}"),

            'ApplicationsReceived' => $ms(
                $cm,
                "ms.id IN (SELECT MIN(m.id) FROM member_schemes m
                           WHERE m.member_scheme_active = '1' AND m.noscheme = '0'
                           GROUP BY m.member_id)
                 AND {$act} AND ms.noscheme = '0' {$verAnd}"
            ),

            'Applicationsunattended' => $ms(
                $cm,
                "ms.id IN (SELECT MIN(m.id) FROM member_schemes m
                           WHERE NOT EXISTS (SELECT 1 FROM member_schemes x
                                             WHERE x.member_id = m.member_id
                                               AND x.member_scheme_active = '1'
                                               AND x.sent_approve = '1')
                             AND m.member_scheme_active = '1' AND m.noscheme = '0'
                           GROUP BY m.member_id)
                 AND {$act} AND ms.sent_approve = '0' AND ms.noscheme = '0'
                 AND ms.reject_status = '0' {$verAnd}"
            ),

            'ApplicationsInprogress' => $ms(
                $cm,
                "{$act} AND ms.sent_approve = '1' AND ms.sention_status = '0' {$verAnd}"
            ),

            'ApplicationsSanction' => $ms(
                $cm,
                "{$act} AND (ms.sention_status = '1' {$cond['rejected_after_approved_or']}) {$verAnd}"
            ),

            'ZoneName' => [
                'district' => 'dname',
                'expr'     => 'COUNT(DISTINCT Zone)',
                'from'     => 'new_masters',
                'group'    => 'dname',
            ],

            'noscheme' => $ms(
                $cm,
                "NOT EXISTS (SELECT 1 FROM member_schemes dms
                             WHERE dms.member_id = ms.member_id
                               AND dms.noscheme = '0'
                               AND dms.member_scheme_active = '1')
                 AND {$act} AND ms.noscheme = '1' {$verAnd}"
            ),

            'LoanApplication' => $ms(
                $cm,
                "ms.radiobuttonStatus {$cond['loan_sanction_bank_in']}
                 AND {$act} AND ms.noscheme = '0' {$verAnd}"
            ),
            'LoanDisbursed' => $ms(
                $cm,
                "ms.radiobuttonStatus {$cond['loan_disbursed_bank_in']}
                 AND {$act} AND ms.noscheme = '0' {$verAnd}"
            ),

            // Loan sanctioned / disbursed at department level (mostly dept id 9 and 21)
            'LoanApplicationByDept' => $ms(
                $cm,
                "ms.radiobuttonStatus {$cond['loan_sanction_dept_in']}
                 AND {$act} AND ms.noscheme = '0' {$verAnd}"
            ),
            'LoanDisbursedByDept' => $ms(
                $cm,
                "ms.radiobuttonStatus {$cond['loan_disbursed_in_dept']} AND ms.forwarded_to_bank IS NULL
                 AND {$act} AND ms.noscheme = '0' {$verAnd}"
            ),

            'Rejected_by_dept' => $ms(
                $cm,
                "{$act} AND ms.noscheme = '0' {$cond['rejected']} {$verAnd}"
            ),
            'not_interested_by_dept' => $ms(
                $cm,
                "{$act} {$cond['rejected_after_approved_and']} {$verAnd}"
            ),

            'apporved_no_action' => $ms(
                $cm,
                "{$act} AND ms.sention_status = '1' AND ms.radiobuttonStatus IS NULL
                 AND ms.forwarded_to_bank IS NULL {$verAnd}"
            ),
            'wage_emp' => $ms(
                $cm,
                "{$act} AND ms.sention_status = '1' {$cond['wage_in']} {$verAnd}"
            ),
            'training_given' => $ms(
                $cm,
                "{$act} AND ms.sention_status = '1' {$cond['training_given']} {$verAnd}"
            ),
            'honorarium_given' => $ms(
                $cm,
                "{$act} AND ms.sention_status = '1' {$cond['honorarium_given']} {$verAnd}"
            ),

            'forwarded_to_bank' => $ms(
                $cm,
                "{$act} AND ms.noscheme = '0' AND ms.forwarded_to_bank IS NOT NULL
                 {$cond['excluded_case_from_fwd_bank']} {$verAnd}"
            ),
            'forwarded_to_bank_no_action' => $ms(
                $cm,
                "{$act} AND ms.sention_status = '1' AND ms.radiobuttonStatus IS NULL
                 AND ms.forwarded_to_bank IS NOT NULL {$verAnd}"
            ),
            'pending_disbursal' => $ms(
                $cm,
                "{$act} AND ms.sention_status = '1'
                 AND ms.radiobuttonStatus {$cond['pending_disbursal_bank_in']} {$verAnd}"
            ),
            'recjeted_by_bank' => $ms(
                $cm,
                "{$act} AND ms.noscheme = '0' AND ms.forwarded_to_bank IS NOT NULL
                 {$cond['rejected_after_fwd_to_bank_and']} {$cond['and_not']} {$verAnd}"
            ),
        ];

        $rows = $this->runReport(self::MEMBER_COLUMNS, $branches, "WHERE District <> 'TEST DISTRICT'");

        return $this->finalise(
            $rows,
            self::MEMBER_COLUMNS,
            static function (array $r): array {
                $r['percentage'] = ($r['FamilyCount'] ?? 0) > 0
                    ? round(($r['FamilySurveyed'] / $r['FamilyCount']) * 100, 2)
                    : null;

                return $r;
            }
        );
    }

    // ------------------------------------------------------------------
    // Query builder helpers
    // ------------------------------------------------------------------

    /**
     * @param list<string>                $columns
     * @param array<string,array<string,string>> $branches metric => definition
     */
    private function runReport(array $columns, array $branches, string $outerWhere): array
    {
        $parts = [];

        foreach ($branches as $metric => $b) {
            $select = [
                "{$b['district']} AS District",
                ($b['id'] ?? 'NULL') . ' AS id',
            ];

            foreach ($columns as $col) {
                $select[] = ($col === $metric ? $b['expr'] : '0') . " AS {$col}";
            }

            $sql = 'SELECT ' . implode(', ', $select) . ' FROM ' . $b['from'];

            if (! empty($b['where'])) {
                $sql .= ' WHERE ' . $b['where'];
            }

            $parts[] = $sql . ' GROUP BY ' . $b['group'];
        }

        $sums = implode(', ', array_map(static fn(string $c): string => "SUM({$c}) AS {$c}", $columns));

        $sql = "SELECT District, MAX(id) AS id, {$sums}
                FROM (" . implode("\nUNION ALL\n", $parts) . ") tbb
                {$outerWhere}
                GROUP BY District";

        return $this->db->query($sql)->getResultArray();
    }

    /**
     * Cast numbers, build the Total row (before district 25 is removed, like the old SQL),
     * hide district 25, sort by name and append Total.
     *
     * @param list<string> $columns
     */
    private function finalise(array $rows, array $columns, callable $derive): array
    {
        $total = ['District' => 'Total', 'id' => ''];

        foreach ($columns as $c) {
            $total[$c] = 0;
        }

        foreach ($rows as &$row) {
            foreach ($columns as $c) {
                $row[$c]    = (int) ($row[$c] ?? 0);
                $total[$c] += $row[$c];
            }
        }
        unset($row);

        // District id 25 is never shown (same as the old view).
        $rows = array_values(array_filter(
            $rows,
            static fn(array $r): bool => (string) ($r['id'] ?? '') !== '25'
        ));

        usort($rows, static fn(array $a, array $b): int => strcasecmp((string) $a['District'], (string) $b['District']));

        $rows   = array_map($derive, $rows);
        $rows[] = $derive($total);

        return $rows;
    }



    private const DEPARTMENT_COLUMNS = [
        'fwdbank',
        'applicationreceived',
        'applicationinprogress',
        'applicationinrejected',
        'applicationinnotinterested',
        'applicationinrejectedbybank',
        'ApplicationsSanction',
        'honorariumGivenList',
        'loanapplication',
        'trainingapplication',
        'employmentapplication',
        'UnattendedAppli',
        'LoanDisbursed',
        'LoanApplicationByDept',
        'LoanDisbursedByDept',
        'ApplicationsSanction_no_action',
        'fwdbank_no_action',
        'pending_disbursal',
    ];

    /**
     * @return list<array<string,mixed>> department rows (sorted by name) followed by one "Total" row.
     *                                   Keys: departmentid, department, + the DEPARTMENT_COLUMNS.
     */
    public function getDepartmentWise(string $version, int $melaPhase = 0, int $isSsa = 0): array
    {
        $verAnd = build_version_condition($version);   // values are intval()'d
        $cond   = get_ms_conditions($version);
        $act    = self::ACTIVE;

        if ($melaPhase > 0) {
            $verAnd .= " AND ms.mela_phase = {$melaPhase} AND ms.is_ssa = {$isSsa}";
        }

        $fromDept = 'member_schemes ms INNER JOIN departments_list dl ON ms.newdepart_id = dl.id';

        $dept = static fn(string $where): array => [
            'district' => 'dl.name',          // runReport() aliases this as "District"; renamed below
            'id'       => 'MAX(dl.id)',
            'expr'     => 'COUNT(DISTINCT ms.member_id)',
            'from'     => $fromDept,
            'where'    => $where,
            'group'    => 'dl.name',
        ];

        $branches = [
            // every active department, so departments with no applications still appear
            '_all' => [
                'district' => 'dl.name',
                'id'       => 'dl.id',
                'expr'     => '0',
                'from'     => 'departments_list dl',
                'where'    => "dl.status = '1'",
                'group'    => 'dl.id, dl.name',
            ],

            'fwdbank' => $dept(
                "{$act} AND ms.noscheme = '0' AND ms.forwarded_to_bank IS NOT NULL
             {$cond['excluded_case_from_fwd_bank']} {$verAnd}"
            ),

            'applicationreceived' => $dept(
                "ms.id IN (SELECT MIN(m.id) FROM member_schemes m
                       WHERE m.member_scheme_active = '1' AND m.noscheme = '0'
                       GROUP BY m.member_id)
             AND {$act} AND ms.noscheme = '0' {$verAnd}"
            ),

            'applicationinprogress' => $dept(
                "{$act} AND ms.sent_approve = '1' AND ms.sention_status = '0' {$verAnd}"
            ),

            'applicationinrejected' => $dept("{$act} {$cond['rejected']} {$verAnd}"),

            'applicationinnotinterested' => $dept("{$act} {$cond['rejected_after_approved_and']} {$verAnd}"),

            'ApplicationsSanction' => $dept(
                "{$act} AND (ms.sention_status = '1' {$cond['rejected_after_approved_or']}) {$verAnd}"
            ),

            'loanapplication' => $dept(
                "{$act} AND ms.sent_approve = '1' AND ms.sention_status = '1'
             AND ms.radiobuttonStatus {$cond['loan_sanction_bank_in']} {$verAnd}"
            ),

            'honorariumGivenList' => $dept(
                "{$act} AND ms.sent_approve = '1' AND ms.sention_status = '1'
             {$cond['honorarium_given']} {$verAnd}"
            ),

            'trainingapplication' => $dept(
                "{$act} AND ms.sent_approve = '1' AND ms.sention_status = '1'
             {$cond['training_given']} {$verAnd}"
            ),

            'employmentapplication' => $dept(
                "{$act} AND ms.sent_approve = '1' AND ms.sention_status = '1'
             {$cond['wage_in']} {$verAnd}"
            ),

            // Old query joined `district` and the "first scheme per member" list; kept the same.
            'UnattendedAppli' => [
                'district' => 'dl.name',
                'id'       => 'MAX(dl.id)',
                'expr'     => 'COUNT(DISTINCT ms.member_id)',
                'from'     => $fromDept . ' INNER JOIN district d ON ms.district_id = d.id',
                'where'    => "ms.id IN (SELECT MIN(m.id) FROM member_schemes m
                                     WHERE m.member_scheme_active = '1' AND m.noscheme = '0'
                                     GROUP BY m.member_id)
                           AND {$act} AND ms.sent_approve = '0' AND ms.reject_status = '0'
                           AND ms.noscheme = '0' {$verAnd}",
                'group'    => 'dl.name',
            ],

            'LoanDisbursed' => $dept(
                "{$act} AND ms.radiobuttonStatus {$cond['loan_disbursed_bank_in']} {$verAnd}"
            ),

            // Loan sanctioned / disbursed at department level (mostly dept id 9 and 21)
            'LoanApplicationByDept' => $dept(
                "ms.radiobuttonStatus {$cond['loan_sanction_dept_in']}
             AND {$act} AND ms.noscheme = '0' {$verAnd}"
            ),
            'LoanDisbursedByDept' => $dept(
                "ms.radiobuttonStatus {$cond['loan_disbursed_in_dept']}
             AND {$act} AND ms.noscheme = '0' {$verAnd}"
            ),

            'applicationinrejectedbybank' => $dept(
                "{$act} AND ms.forwarded_to_bank IS NOT NULL
             {$cond['rejected_after_fwd_to_bank_and']} {$cond['and_not']} {$verAnd}"
            ),

            'ApplicationsSanction_no_action' => $dept(
                "{$act} AND ms.sention_status = '1' AND ms.radiobuttonStatus IS NULL
             AND ms.forwarded_to_bank IS NULL {$verAnd}"
            ),

            'fwdbank_no_action' => $dept(
                "{$act} AND ms.sention_status = '1' AND ms.forwarded_to_bank IS NOT NULL
             AND ms.radiobuttonStatus IS NULL {$verAnd}"
            ),

            'pending_disbursal' => $dept(
                "{$act} AND ms.sention_status = '1'
             AND ms.radiobuttonStatus {$cond['pending_disbursal_bank_in']} {$verAnd}"
            ),
        ];

        $rows = $this->runReport(self::DEPARTMENT_COLUMNS, $branches, '');

        // Build output rows with department keys + Total row.
        $total = ['departmentid' => '', 'department' => 'Total'];

        foreach (self::DEPARTMENT_COLUMNS as $c) {
            $total[$c] = 0;
        }

        $out = [];

        foreach ($rows as $r) {
            $row = [
                'departmentid' => (string) ($r['id'] ?? ''),
                'department'   => (string) ($r['District'] ?? ''),
            ];

            foreach (self::DEPARTMENT_COLUMNS as $c) {
                $row[$c]    = (int) ($r[$c] ?? 0);
                $total[$c] += $row[$c];
            }

            $out[] = $row;
        }

        usort($out, static fn(array $a, array $b): int => strcasecmp($a['department'], $b['department']));

        $out[] = $total;

        return $out;
    }



    private const BANK_COLUMNS = [
        'Applicationsforwardedtobank',
        'Applicationsloansanction',
        'Applicationsloandisbursed',
        'ApplicationsRejectedbybank',
        'disbursal_pending',
        'adc_pending',
        'ApplicationsPending',          // "No action taken by bank"
    ];

    private const BRANCH_COLUMNS = [
        'Applications_forwarded_to_bank',
        'Applications_loan_sanctioned',
        'Applications_loan_disbursed',
        'Applications_Rejected_by_bank',
        'Applications_forwarded_to_bank_no_action',
        'Applications_loan_disbursed_no_action',   // "Pending for disbursal"
        'adc_pending',
    ];

    /** @return list<array{id:string,dist_name:string}> */
    public function getDistrictOptions(): array
    {
        return $this->db->query('SELECT DISTINCT id, dist_name FROM district ORDER BY dist_name')->getResultArray();
    }

    /** Version + district + mela phase conditions shared by the bank and branch reports. */
    private function bankReportConditions(string $version, string $districtId, int $melaPhase, int $isSsa): string
    {
        $and = build_version_condition($version, 'ms.app_version');   // intval()'d values

        if ($districtId !== '') {
            $and .= ' AND ms.district_id = ' . $this->db->escape($districtId);
        }

        if ($melaPhase > 0) {
            $and .= " AND ms.mela_phase = {$melaPhase} AND ms.is_ssa = {$isSsa}";
        }

        return $and;
    }

    /**
     * Metric definitions shared by both reports: [column => [expr, where]].
     * Note: loan sanction / disbursed use COUNT(member_id) (not DISTINCT) exactly like the old SQL.
     *
     * @return array<string,array{0:string,1:string}>
     */
    private function bankMetrics(array $cond, string $ver, array $names): array
    {
        $act = self::ACTIVE;
        $d   = 'COUNT(DISTINCT ms.member_id)';

        return [
            $names['fwd'] => [
                $d,
                "ms.forwarded_to_bank IS NOT NULL AND {$act} {$cond['excluded_case_from_fwd_bank']} {$ver}"
            ],

            $names['sanction'] => [
                'COUNT(ms.member_id)',
                "{$act} AND ms.sention_status = '1' AND ms.radiobuttonStatus {$cond['loan_sanction_bank_in']} {$ver}"
            ],

            $names['disbursed'] => [
                'COUNT(ms.member_id)',
                "{$act} AND ms.sention_status = '1' AND ms.forwarded_to_bank IS NOT NULL
             AND ms.radiobuttonStatus {$cond['loan_disbursed_in']} {$ver}"
            ],

            $names['rejected'] => [
                $d,
                "{$act} AND ms.forwarded_to_bank IS NOT NULL {$cond['rejected_after_fwd_to_bank_and']} {$ver}"
            ],

            $names['pending'] => [
                $d,
                "{$act} AND ms.sention_status = '1' AND ms.forwarded_to_bank IS NOT NULL
             AND ms.radiobuttonStatus {$cond['pending_disbursal_bank_in']} {$ver}"
            ],

            'adc_pending' => [
                $d,
                "{$act} AND ms.is_reject_requesting = '1' AND ms.reject_status = '0'
             AND ms.forwarded_to_bank IS NOT NULL {$ver}"
            ],

            $names['no_action'] => [
                $d,
                "{$act} AND ms.radiobuttonStatus IS NULL AND ms.sention_status = '1'
             AND ms.forwarded_to_bank IS NOT NULL {$ver}"
            ],
        ];
    }

// ----------------------------------------------------------------------
// BANK WISE
// ----------------------------------------------------------------------

    /**
     * @return list<array<string,mixed>> bank rows (sorted) + one "Total" row. Keys: id, bankname + BANK_COLUMNS
     */
    public function getAplicationsCountBankWise(
        string $version,
        string $districtId = '',
        int $melaPhase = 0,
        int $isSsa = 0
    ): array {
        $cond = get_ms_conditions($version);
        $ver  = $this->bankReportConditions($version, $districtId, $melaPhase, $isSsa);
        $from = 'member_schemes ms INNER JOIN m_bank mb ON ms.forwarded_to_bank = mb.id';

        $metrics = $this->bankMetrics($cond, $ver, [
            'fwd'       => 'Applicationsforwardedtobank',
            'sanction'  => 'Applicationsloansanction',
            'disbursed' => 'Applicationsloandisbursed',
            'rejected'  => 'ApplicationsRejectedbybank',
            'pending'   => 'disbursal_pending',
            'no_action' => 'ApplicationsPending',
        ]);

        $branches = [];

        foreach ($metrics as $column => [$expr, $where]) {
            $branches[$column] = [
                'district' => 'mb.Name',          // runReport() aliases this as "District"; renamed below
                'id'       => 'MAX(mb.id)',
                'expr'     => $expr,
                'from'     => $from,
                'where'    => $where,
                'group'    => 'mb.Name',
            ];
        }

        $rows = $this->runReport(self::BANK_COLUMNS, $branches, '');

        $total = ['id' => '', 'bankname' => 'Total'];

        foreach (self::BANK_COLUMNS as $c) {
            $total[$c] = 0;
        }

        $out = [];

        foreach ($rows as $r) {
            $row = ['id' => (string) ($r['id'] ?? ''), 'bankname' => (string) ($r['District'] ?? '')];

            foreach (self::BANK_COLUMNS as $c) {
                $row[$c]    = (int) ($r[$c] ?? 0);
                $total[$c] += $row[$c];
            }

            $out[] = $row;
        }

        usort($out, static fn(array $a, array $b): int => strcasecmp($a['bankname'], $b['bankname']));
        $out[] = $total;

        return $out;
    }

// ----------------------------------------------------------------------
// BRANCH WISE
// ----------------------------------------------------------------------

    /**
     * One row per bank + branch + district (+ mela phase for version 1 / all versions).
     *
     * @return list<array<string,mixed>> rows + one "Total" row.
     *         Keys: bankname, branch_name, district, mela_phase + BRANCH_COLUMNS
     */
    public function getAplicationsCountBranchWise(
        string $version,
        string $districtId = '',
        int $melaPhase = 0,
        int $isSsa = 0
    ): array {
        $cond      = get_ms_conditions($version);
        $ver       = $this->bankReportConditions($version, $districtId, $melaPhase, $isSsa);
        $withPhase = $version !== '2';   // same as the old group_by: version 2 has no mela phase column

        $from = "member_schemes ms
             INNER JOIN m_bank mb ON ms.forwarded_to_bank = mb.id AND is_status = '1'
             INNER JOIN branch_details_new bdn ON bdn.id = ms.forwarded_to_branch
             INNER JOIN district d ON d.id = ms.district_id";

        $group = 'mb.Name, bdn.Name, d.dist_name' . ($withPhase ? ', ms.mela_phase' : '');

        $metrics = $this->bankMetrics($cond, $ver, [
            'fwd'       => 'Applications_forwarded_to_bank',
            'sanction'  => 'Applications_loan_sanctioned',
            'disbursed' => 'Applications_loan_disbursed',
            'rejected'  => 'Applications_Rejected_by_bank',
            'pending'   => 'Applications_loan_disbursed_no_action',
            'no_action' => 'Applications_forwarded_to_bank_no_action',
        ]);

        $parts = [];

        foreach ($metrics as $metric => [$expr, $where]) {
            $select = [
                'mb.Name AS bankname',
                'bdn.Name AS branch_name',
                'd.dist_name AS district',
                ($withPhase ? 'ms.mela_phase' : '0') . ' AS mela_phase',
            ];

            foreach (self::BRANCH_COLUMNS as $col) {
                $select[] = ($col === $metric ? $expr : '0') . " AS {$col}";
            }

            $parts[] = 'SELECT ' . implode(', ', $select) . " FROM {$from} WHERE {$where} GROUP BY {$group}";
        }

        $sums = implode(', ', array_map(
            static fn(string $c): string => "SUM({$c}) AS {$c}",
            self::BRANCH_COLUMNS
        ));

        $sql = "SELECT bankname, branch_name, district, mela_phase, {$sums}
            FROM (" . implode("\nUNION ALL\n", $parts) . ") t
            GROUP BY bankname, branch_name, district, mela_phase
            ORDER BY bankname, branch_name, district, mela_phase";

        $rows = $this->db->query($sql)->getResultArray();

        $total = ['bankname' => 'Total', 'branch_name' => '-', 'district' => '-', 'mela_phase' => '-'];

        foreach (self::BRANCH_COLUMNS as $c) {
            $total[$c] = 0;
        }

        foreach ($rows as &$row) {
            foreach (self::BRANCH_COLUMNS as $c) {
                $row[$c]    = (int) ($row[$c] ?? 0);
                $total[$c] += $row[$c];
            }

            // Old display rule: phases above 4 are shown minus one.
            if (! $withPhase || $row['mela_phase'] === null) {
                $row['mela_phase'] = '';
            } else {
                $phase             = (int) $row['mela_phase'];
                $row['mela_phase'] = $phase > 4 ? $phase - 1 : $phase;
            }
        }
        unset($row);

        $rows[] = $total;

        return $rows;
    }


    public function getSchemeIdsByType(int $type): array
{
    $rows = $this->db->query('SELECT id FROM scheme_list WHERE `type` = ?', [$type])->getResultArray();

    return array_map(static fn(array $r): string => (string) $r['id'], $rows);
}

/**
 * @param list<string> $schemeIds
 * @return list<array<string,mixed>> one row per scheme, ordered by applications received
 */
public function getSchemeDepartmentWiseReport(array $schemeIds, string $version): array
{
    if ($schemeIds === []) {
        return [];
    }

    $cond      = get_ms_conditions($version);
    $verInner  = build_version_condition($version);                    // single-table subqueries
    $verOuter  = build_version_condition($version, 'ms.app_version');  // main query (has joins)
    $sent      = "ms.sent_approve = '1' AND ms.sention_status = '1'";

    $count = static fn(string $when): string => "COALESCE(SUM(CASE WHEN {$when} THEN 1 ELSE 0 END), 0)";
    $caseWhen = static fn(string $whenFragment): string => "COALESCE(SUM(CASE {$whenFragment} THEN 1 ELSE 0 END), 0)";

    $metrics = [
        'applicationreceived'          => $count('f1.min_id IS NOT NULL'),
        'UnattendedAppli'              => $count("f2.min_id IS NOT NULL AND ms.sent_approve = '0' AND ms.reject_status = '0'"),
        'applicationinprogress'        => $count("ms.sent_approve = '1' AND ms.sention_status = '0'"),
        'ApplicationsSanction'         => $count("ms.sent_approve = '1' AND (ms.sention_status = '1' {$cond['rejected_after_approved_or']})"),
        'ApplicationsPendingDept'      => $count("{$sent} AND ms.radiobuttonStatus IS NULL AND ms.forwarded_to_bank IS NULL"),
        'ApplicationsPendingDisburse'  => $count("{$sent} AND ms.radiobuttonStatus {$cond['pending_disbursal_bank_in']}"),
        'bankpending'                  => $count('ms.forwarded_to_bank IS NOT NULL AND ms.radiobuttonStatus IS NULL'),
        'applicationinrejected'        => $caseWhen($cond['rejected_when']),
        'applicationinNotInterested'   => $caseWhen($cond['rejected_after_approved_when']),
        'applicationinrejectedbybank'  => $caseWhen($cond['rejected_after_fwd_to_bank_when']),
        'fwdbank'                      => $count('ms.forwarded_to_bank IS NOT NULL'),
        'loanapplication'              => $count("{$sent} AND ms.radiobuttonStatus {$cond['loan_sanction_bank_in']}"),
        'LoanDisbursed_1st'            => $count("ms.radiobuttonStatus {$cond['loan_disbursed_bank_in']}"),
        'employmentapplication'        => $count("{$sent} AND ms.radiobuttonStatus IN ('2','9')"),
        'trainingcompletedapplication' => $count("{$sent} AND (ms.radiobuttonStatus = '4' OR ms.training_honorarium_status IN ('2'))"),
        'trainingstartedapplication'   => $count("{$sent} AND ms.training_honorarium_status IN ('1')"),
        'honorariumGivenList'          => $count("{$sent} AND (ms.radiobuttonStatus = '6' OR ms.training_honorarium_status IN ('3'))"),
        'purchasing_done_1st'          => $count("{$sent} AND ms.radiobuttonStatus IN ('1','12','13','14','15','16') AND ms.newdepart_id = '4'"),
        'LoanDisbursed_2nd'            => $count("ms.radiobuttonStatus IN ('15','16')"),
        'subsidy_released_1st'         => $count("ms.radiobuttonStatus IN ('13','14','15','16')"),
        'purchasing_done_2nd'          => $count("ms.radiobuttonStatus IN ('14','15','16')"),
        'subsidy_released_2nd'         => $count("ms.radiobuttonStatus IN ('16')"),
        'assets_released'              => $count("ms.radiobuttonStatus IN ('17')"),
        'finial_benefit_given'         => $count("ms.is_final_benefit = '1'"),
        'job_joined'                   => $count("ms.radiobuttonStatus = '9'"),
    ];

    $select = [];

    foreach ($metrics as $alias => $expr) {
        $select[] = "{$expr} AS {$alias}";
    }

    $placeholders = implode(',', array_fill(0, count($schemeIds), '?'));

    $sql = "SELECT sl.scheme_name,
                   dl.name AS department_name,
                   ms.newschm_id,
                   ms.newdepart_id,
                   " . implode(",\n                   ", $select) . "
            FROM member_schemes ms

            LEFT JOIN (
                SELECT MIN(m.id) AS min_id
                FROM member_schemes m
                WHERE m.member_scheme_active = '1' AND m.noscheme = '0' {$verInner}
                GROUP BY m.member_id
            ) f1 ON f1.min_id = ms.id

            LEFT JOIN (
                SELECT MIN(m.id) AS min_id
                FROM member_schemes m
                WHERE NOT EXISTS (
                        SELECT 1 FROM member_schemes x
                        WHERE x.member_id = m.member_id
                          AND x.member_scheme_active = '1'
                          AND x.sent_approve = '1'
                      )
                  AND m.member_scheme_active = '1' AND m.noscheme = '0' {$verInner}
                GROUP BY m.member_id
            ) f2 ON f2.min_id = ms.id

            INNER JOIN scheme_list sl ON ms.newschm_id = sl.id
            INNER JOIN departments_list dl ON dl.id = ms.newdepart_id

            WHERE ms.newschm_id IN ({$placeholders})
              AND ms.member_scheme_active = '1'
              AND ms.noscheme = '0'
              {$verOuter}

            GROUP BY ms.newschm_id, ms.newdepart_id, sl.scheme_name, dl.name
            ORDER BY applicationreceived DESC";

    $rows = $this->db->query($sql, array_values($schemeIds))->getResultArray();

    foreach ($rows as &$row) {
        foreach (array_keys($metrics) as $alias) {
            $row[$alias] = (int) ($row[$alias] ?? 0);
        }
    }
    unset($row);

    return $rows;
}
}
