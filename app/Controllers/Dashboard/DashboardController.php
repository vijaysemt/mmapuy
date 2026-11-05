<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $role = (string) $session->get('roleid');

        $roles = config('AuthRoles');

        $headerOnly = in_array(
            $role,
            $roles->headerOnlyRoles,
            true
        );

        $data = [
            'role' => $role,
            'user' => $session->get('userdata') ?? [],
            'headerOnly' => $headerOnly,
            'dashboardTitle' => 'Dashboard',
            'dashboardStats' => [
                [
                    'title' => 'Total Users',
                    'value' => '1,250',
                    'icon' => 'bi-people',
                    'color' => 'primary',
                ],
                [
                    'title' => 'Total Applications',
                    'value' => '8,540',
                    'icon' => 'bi-file-earmark-text',
                    'color' => 'success',
                ],
                [
                    'title' => 'Pending Applications',
                    'value' => '320',
                    'icon' => 'bi-hourglass-split',
                    'color' => 'warning',
                ],
                [
                    'title' => 'Completed Applications',
                    'value' => '7,890',
                    'icon' => 'bi-check-circle',
                    'color' => 'info',
                ],
            ],
        ];

        return view('dashboard/index', $data);
    }
}
