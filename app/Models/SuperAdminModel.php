<?php

namespace App\Models;

use CodeIgniter\Model;

class SuperAdminModel extends Model
{
    protected $db;

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
}
