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
        '1'  => 'district-user/dashboard',
        // '2'  => 'Zone_user/dashboard',
        '3'  => 'department-user/view-dashboard',
        '5'  => 'cm/dashboard',
        '6'  => 'department-district-user/view-dashboard',
        '7'  => 'dswo-user/dashboard',
        '8'  => 'adc-user/dashboard',
        '9'  => 'super-admin/dashboard',
        '10' => 'slbc-admin/dashboard',
        '11' => 'ldm-user/dashboard',
        '12' => 'branch-user/dashboard',
        '13' => 'bank-user/dashboard',
        '14' => 'super-admin/dashboard',
        '16' => 'user-feedback/dashboard',
        '18' => 'controlling-heads/dashboard',
        '19' => 'cooperative-user/C-dashboard',
        '20' => 'user_feedback/family-feedback-list',
        '21' => 'district-Calling-User/feedback-family-list',
        '23' => 'seva-setu-abhiyan-user/dashboard',
        '24' => 'seva-setu-abhiyan-user/admin-dashboard',
    ];

    /**
     * Roles without sidebar.
     */
    public array $headerOnlyRoles = [
       '9', '14',
    ];
}
