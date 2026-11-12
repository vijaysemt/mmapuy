<?php


function build_version_condition($v, $column = 'app_version')
{
    if (empty($v)) {
        return '';
    }

    if (strpos($v, ',') !== false) {
        $v_array = array_filter(array_map('trim', explode(',', $v)));
        $v_array = array_map('intval', $v_array);

        if (!empty($v_array)) {
            return "AND {$column} IN (" . implode(',', $v_array) . ")";
        }
    }

    return "AND {$column} = " . intval($v);
}

function build_ms_version_condition($v, $column = 'ms.app_version')
{
    if (empty($v)) {
        return '';
    }

    if (strpos($v, ',') !== false) {
        $v_array = array_filter(array_map('trim', explode(',', $v)));
        $v_array = array_map('intval', $v_array);

        if (!empty($v_array)) {
            return "AND {$column} IN (" . implode(',', $v_array) . ")";
        }
    }

    return "AND {$column} = " . intval($v);
}


function get_ms_conditions($v=2,$mela_phase=0)
{
    // defaults
    $conditions = [
        'fdn_phase_and' => '',
        'group_by' => '',
        'no_schm_and' => '',

        'loan_sanction_in' => "IN('1','7','12','13','14','15','16','21')",
        'purchased_done_1' => "IN ('1','12','13','14','15','16','21') AND  ms.`newschm_id` = '12'",
        'purchased_done_2' => "IN ('14','15','16') AND  ms.`newschm_id` = '12'",
        'loan_disbursed_2' => "IN ('15','16') AND  ms.`newschm_id` = '12'",
        'loan_disbursed_1' => "IN ('1','13','14','15','16','21')",
        'npa' => "IN ('21')",

        'purchases_done_first' => "AND ms.forwarded_to_bank IS NOT NULL AND ms.`newschm_id` = '12' AND ms.`radiobuttonStatus` IN ('1','12','13','14','15','16','21')",

        'purchases_done_second' => "AND ms.forwarded_to_bank IS NOT NULL AND ms.`newschm_id` = '12' AND ms.`radiobuttonStatus` IN ('14','15','16')",

        'pending_first_purchases' => "AND ms.forwarded_to_bank IS NOT NULL AND ms.`newschm_id` = '12' AND ms.`radiobuttonStatus` IN ('7')",

        'pending_second_purchases' => "AND ms.forwarded_to_bank IS NOT NULL AND ms.`newschm_id` = '12' AND ms.`radiobuttonStatus` IN ('13')",

        'purchases_done_disburse_pending' => "AND ms.forwarded_to_bank IS NOT NULL AND ms.`newschm_id` = '12' AND ms.`radiobuttonStatus` IN ('12')",

        'subsidy_released_first' => "AND ms.forwarded_to_bank IS NOT NULL AND ms.`radiobuttonStatus` IN ('13','14','15','16')",

        'subsidy_released_second' => "AND ms.forwarded_to_bank IS NOT NULL AND ms.`radiobuttonStatus` IN ('16')",

        'loan_disbursed_second' => "AND ms.forwarded_to_bank IS NOT NULL AND ms.`radiobuttonStatus` IN ('15','16')",

        'loan_sanction_bank_in' => "IN('1','7','12','13','14','15','16','21') AND ms.forwarded_to_bank IS NOT NULL",
        
        //Self-Employment (Asset/Subsidy/Store Opening/Loan)
        'loan_sanction_dept_in' => "IN('1','7','12','13','14','15','16','21','18','19','20') AND ms.forwarded_to_bank IS NULL", //+ harhit store opened

        'loan_disbursed_in' => "IN('1','13','14','15','16','21')",
        'loan_disbursed_bank_in' => "IN('1','13','14','15','16','21') AND ms.forwarded_to_bank IS NOT NULL",
        'loan_disbursed_in_dept' => "IN('1','13','14','15','16','21') AND ms.forwarded_to_bank IS NULL",

        'only_sanctioned_in' => "IN('7','12')",
        'pending_disbursal_bank_in' => "IN('7','12') AND ms.forwarded_to_bank IS NOT NULL",
        'pending_disbursal_dept_in' => "IN('7','12') AND ms.forwarded_to_bank IS NULL",

        'training_given' => "AND ms.radiobuttonStatus != '6' AND (`radiobuttonStatus`= '4' OR ms.`training_honorarium_status` = '2'  OR ms.`training_honorarium_status` = '6')",
        'honorarium_given' => "AND ms.radiobuttonStatus != '4' AND (`radiobuttonStatus`= '6' OR ms.`training_honorarium_status` = '3')",
        
        'rejected' => '',
        'rejected_after_approved_and' => '',
        'rejected_after_fwd_to_bank_and' => '',
        'rejected_after_approved_or' => '',
        
        'wage_in' => "AND radiobuttonStatus IN ('2','9','17')", // wage emp+job joined+ backyard AHD asset released 
        'and_not' => " AND NOT (ms.reject_status = '2' AND ms.radiobuttonStatus IS NULL)",
        'excluded_case_from_fwd_bank' => '',

        'rejected_when' => '',
        'rejected_after_approved_when' => '',
        'rejected_after_fwd_to_bank_when' => '',
        'rejected_by_bank' => "AND (ms.reject_status = 1 || ms.reject_status = 2) AND ms.radiobuttonStatus = '5'",
        'rejected_by_bank_when' => "WHEN (ms.reject_status = 1 || ms.reject_status = 2) AND ms.radiobuttonStatus = '5'",
        'all_dept_in'=> "IN('1','2','3','4','5','7','8','9','10','11','12','13','14','16','18','19','20','21','22','23','24')",
        'mela_phase'=> "CASE
            WHEN mela_phase IN (3, 4) THEN 'Phase 3'
            WHEN mela_phase > 4 AND mela_phase < 7 THEN CONCAT('Phase ', mela_phase - 1)
            WHEN is_ssa = 1  THEN CONCAT('Seva Setu ','(SSA)')
            WHEN mela_phase > 6 THEN CONCAT('Round ', mela_phase - 6)
            ELSE CONCAT('Phase ', mela_phase)
        END AS mela_phase",
        'mela_phase_2'=> "CASE
            WHEN mela_phase IN (3, 4) THEN 'Phase 3'
            WHEN mela_phase > 4 AND mela_phase < 7 THEN CONCAT('Phase ', mela_phase - 1)
            WHEN mela_phase > 6 THEN CONCAT('Round ', mela_phase - 6)
            ELSE CONCAT('Phase ', mela_phase)
        END AS phase",

        'fdn_phase'=> "CASE
            WHEN phase = 14 THEN CONCAT('Phase ', '5')
            WHEN phase >= 16 THEN CONCAT('Round ', phase - 15)
            ELSE ''
        END AS mela_phase",
    ];

    if ($v == '2') {
       $conditions['group_by'] = "bankname,branch_name";
       $conditions['group_by_bank_branch'] = "bankname,branch_name";

        $conditions['fdn_phase_and'] = "AND phase >= 14";
        if($mela_phase != 0 || !empty($mela_phase)) {
            if ($mela_phase == 6) {
                $conditions['fdn_phase_and'] = "AND phase = 14";
                // phase 15 have not any family
            } elseif ($mela_phase == 7) {
                $conditions['fdn_phase_and'] = "AND phase IN (14,16)"; //  phase 17 is set for round 2(except is_ssa_pushed) but postpond, 14 for sonipat meal phase 5, 16 for round 1 mela(becuse both merged into round 1 so in (14,16))
            } else {
                $conditions['fdn_phase_and'] = "AND phase = " . (10 + $mela_phase);
            } 
   
        }
        $conditions['no_schm_and']   = "AND ms.noscheme IN ('0','1')";

        $conditions['rejected'] = "AND ms.reject_status IN ('1')
            AND ms.rejected_after_approved = '0'
            AND ms.forwarded_to_bank IS NULL";

        // not interested before fwd to bank and after approved, not interested + job not joined + job inactive
        $conditions['rejected_after_approved_and'] = "AND (
            (ms.reject_status IN ('2')
                AND ms.rejected_after_approved = '1'
                AND ms.radiobuttonStatus IS NULL)
            OR ms.radiobuttonStatus IN ('10','11')
        )";

        $conditions['rejected_after_fwd_to_bank_and'] = "AND ms.reject_status IN ('1','2','3')
            AND ms.radiobuttonStatus='5'
            AND ms.rejected_after_approved = '1'";

        // not interested after fwd to bank and after approved
        $conditions['rejected_after_approved_or'] = "OR (
            ms.reject_status IN ('1','2')
            AND ms.rejected_after_approved = '1'
        )";

         $conditions['rejected_when'] = "WHEN ms.reject_status IN ('1') AND ms.rejected_after_approved = '0' AND ms.`radiobuttonStatus` IS NULL";
         $conditions['rejected_after_approved_when'] = "WHEN ms.reject_status IN ('2') AND ms.rejected_after_approved = '1' AND ms.`radiobuttonStatus` IS NULL";
         $conditions['rejected_after_fwd_to_bank_when'] = "WHEN ms.reject_status IN ('1','2')  AND ms.radiobuttonStatus='5' AND ms.rejected_after_approved = '1'";

    } else {
        $conditions['group_by'] = "bankname,branch_name,mela_phase";
        $conditions['fdn_phase_and'] = "AND phase <= 13";

        $conditions['rejected'] = "AND ms.reject_status = '1'
            AND ms.forwarded_to_bank IS NULL";

        // not interested + job not joined + job inactive
        $conditions['rejected_after_approved_and'] = "AND (
            (ms.reject_status IN ('2')
                -- AND ms.rejected_after_approved = '0'
                AND ms.radiobuttonStatus IS NULL)
            OR ms.radiobuttonStatus IN ('10','11')
        )";

        $conditions['rejected_after_fwd_to_bank_and'] = "AND ms.reject_status IN ('1','2','3')
            AND (ms.radiobuttonStatus='5' OR ms.radiobuttonStatus IS NULL)";

    // approved case+not interested+rejected by bank
        $conditions['rejected_after_approved_or'] = "OR (
            (ms.reject_status IN ('1','2')
                AND ms.radiobuttonStatus = '5'
                AND ms.forwarded_to_bank IS NOT NULL)
            OR (ms.reject_status = '2'
                AND ms.radiobuttonStatus IS NULL)
            OR (ms.reject_status = '1'
                AND ms.radiobuttonStatus IS NULL
                AND ms.forwarded_to_bank IS NOT NULL)
        )";
         $conditions['rejected_when'] = "WHEN ms.reject_status IN ('1','2') AND ms.`radiobuttonStatus` IS NULL";
         $conditions['rejected_after_approved_when'] = "WHEN ms.reject_status IN ('2') AND ms.`radiobuttonStatus` IS NULL";
         $conditions['rejected_after_fwd_to_bank_when'] = "WHEN ms.reject_status IN ('1','2')  AND ms.radiobuttonStatus='5'";

        
    }
    //exclud asset released+training with fwd bank+wage emp with fwd bank+not interested with fwd bank because they already mention in their respective count
    $conditions['excluded_case_from_fwd_bank'] = " AND (ms.radiobuttonStatus NOT IN ('4','17','2') OR ms.radiobuttonStatus IS NULL)" . $conditions['and_not'];
//  print_r( $conditions);
    return $conditions;
}
