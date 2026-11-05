<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AuthRoles extends BaseConfig
{
    /**
     * Login role => database mapping.
     *
     * loginRole: value submitted by login form
     * dbRole: actual role stored in database
     */
    public array $otpRoles = [
        '1' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '1',
        ],
        '2' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '2',
        ],
        '3' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '3',
        ],
        '4' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '4',
        ],
        '5' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '5',
        ],
        '6' => [
            'table' => 'department_distrcict_user',
            'column' => 'depart_dist_mob',
        ],
        '7' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '7',
        ],
        '8' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '8',
        ],
        '9' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '9',
        ],
        '10' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '10',
        ],
        '11' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '11',
        ],
        '14' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '14',
        ],
        '15' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '15',
        ],
        '16' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '16',
        ],
        '17' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '17',
        ],
        '18' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '18',
        ],
    ];

    /**
     * Roles that use username instead of mobile.
     */
    public array $usernameRoles = [
        '12', '13', '19', '20', '21', '23', '24',
    ];

    /**
     * Password login role mappings.
     */
    public array $passwordRoles = [
        '1' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '1',
        ],
         '2' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '1',
        ],
        '3' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '3',
        ],
        '5' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '5',
        ],
        '6' => [
            'table' => 'department_distrcict_user',
            'column' => 'depart_dist_mob',
        ],
        '7' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '7',
        ],
        '8' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '8',
        ],
        '9' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '9',
        ],
        '11' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '11',
        ],
        '12' => [
            'table' => 'users',
            'column' => 'user_name',
            'dbRole' => '12',
        ],
        '13' => [
            'table' => 'users',
            'column' => 'user_name',
            'dbRole' => '13',
        ],
        '16' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '16',
        ],
        '18' => [
            'table' => 'users',
            'column' => 'mobile',
            'dbRole' => '18',
        ],
        '19' => [
            'table' => 'users',
            'column' => 'user_name',
            'dbRole' => '19',
        ],
        '20' => [
            'table' => 'users',
            'column' => 'user_name',
            'dbRole' => '20',
        ],
        '21' => [
            'table' => 'users',
            'column' => 'user_name',
            'dbRole' => '21',
        ],
        '23' => [
            'table' => 'users',
            'column' => 'user_name',
            'dbRole' => '23',
        ],
        '24' => [
            'table' => 'users',
            'column' => 'user_name',
            'dbRole' => '24',
        ],
    ];

    /**
     * Role-specific dashboard URLs.
     */
    public array $dashboards = [
        '1'  => 'District_user/dashboard',
        // '2'  => 'Zone_user/dashboard',
        '3'  => 'Department_user/view_dashboard',
        '5'  => 'Cm/dashboard',
        '6'  => 'Department_district_user/view_dashboard',
        '7'  => 'Dswo_user/dashboard',
        '8'  => 'ADC_user/dashboard',
        '9'  => 'Super_admin/dashboard',
        '10' => 'Slbc_admin/dashboard',
        '11' => 'LDM_user/dashboard',
        '12' => 'Branch_user/dashboard',
        '13' => 'Bank_user/dashboard',
        '14' => 'Super_admin/dashboard',
        '16' => 'User_feedback/dashboard',
        '18' => 'Controlling_heads/dashboard',
        '19' => 'Cooperative_user/C_dashboard',
        '20' => 'User_feedback/family_feedback_list',
        '21' => 'District_Calling_User/feedback_family_list',
        '23' => 'Seva_setu_abhiyan_user/dashboard',
        '24' => 'Seva_setu_abhiyan_user/admin_dashboard',
    ];

    /**
     * Roles without sidebar.
     */
    public array $headerOnlyRoles = [
       '9', '14',
    ];
}
