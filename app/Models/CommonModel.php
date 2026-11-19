<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Shared helpers for reports (converted from CI3 Common_model).
 */
class CommonModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        helper('version');
    }

    /**
     * Mela phases available for the given version(s).
     *
     * @return list<array{mela_phase_number:int|string,is_ssa:int|string,mela_phase_name:string}>
     */
    public function getMelaPhases(string $version): array
    {
        // build_version_condition() casts every value with intval(), so it is safe to interpolate.
        $verAnd = build_version_condition($version, 'app_version');

        $sql = "SELECT
                    mela_phase AS mela_phase_number,
                    is_ssa,
                    CASE
                        WHEN mela_phase IN (3) THEN 'Phase 3'
                        WHEN mela_phase IN (4) THEN 'Phase 3 (Harhit)'
                        WHEN mela_phase > 4 AND mela_phase < 7
                            THEN CONCAT('Phase ', mela_phase - 1)
                        WHEN is_ssa = 1 THEN CONCAT('Seva Setu ', '(SSA)')
                        WHEN mela_phase > 6
                            THEN CONCAT('Round ', mela_phase - 6)
                        ELSE CONCAT('Phase ', mela_phase)
                    END AS mela_phase_name
                FROM member_schemes
                WHERE mela_phase IS NOT NULL
                {$verAnd}
                GROUP BY mela_phase, is_ssa
                ORDER BY mela_phase ASC";

        return $this->db->query($sql)->getResultArray();
    }

    /**
     * "6" => phase 6, "6_ssa" => phase 6 + SSA.
     *
     * @return array{mela_phase:int,is_ssa:int}
     */
    public function parseMelaPhaseSelection(?string $value): array
    {
        $result = ['mela_phase' => 0, 'is_ssa' => 0];

        if ($value === null || $value === '' || $value === '0') {
            return $result;
        }

        if (str_ends_with($value, '_ssa')) {
            $result['mela_phase'] = (int) str_replace('_ssa', '', $value);
            $result['is_ssa']     = 1;
        } else {
            $result['mela_phase'] = (int) $value;
        }

        return $result;
    }
}