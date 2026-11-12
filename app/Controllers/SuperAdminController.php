<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SuperAdminModel;

class SuperAdminController extends BaseController
{

    protected SuperAdminModel $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new SuperAdminModel();
    }
    public function index()
    {
        $session = session();

        // Authentication check
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

        $version = (string) $this->request->getGet('v');

        // Default dashboard version
        if (!in_array($version, ['1', '2'], true)) {
            $version = '2';
        }

        $data = [
            'role'           => (string) $session->get('roleid'),
            'user'           => $session->get('userdata') ?? [],
            'headerOnly'     => $headerOnly,
            'dashboardTitle' => 'Super Admin Dashboard',
            'dashboardVersion' => $version,
            'statsApiUrl'    => site_url('api/super-admin/dashboard-stats'),
        ];

        return view('pages/super_admin/dashboard_md', $data);
    }

     public function stats()
    {
        $session = session();

        // Authentication check
        if (!$session->get('isLoggedIn')) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Session expired. Please login again.',
                ]);
        }

        // Validate dashboard version
        $version = (string) $this->request->getGet('v');

        if (!in_array($version, ['1', '2'], true)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Invalid dashboard version.',
                ]);
        }
        

        try {
            $stats = $this->dashboardModel->getDashboardStats($version);
            $melaStats = [
            'phase_1' => $this->dashboardModel->getMelaStatsPhase1(),
            'phase_2' => $this->dashboardModel->getMelaStatsPhase2(),
            'phase_3' => $this->dashboardModel->getMelaStatsPhase3(),
            'harhit'  => $this->dashboardModel->getHarhitMelaStats(),
            'phase_4' => $this->dashboardModel->getMelaStatsPhase4(),
        ];
            $stats['melaStats'] = $melaStats;
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Dashboard statistics loaded successfully.',
                'data'    => $stats,
            ]);

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Super Admin Dashboard API Error: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Unable to load dashboard statistics.',
                ]);
        }
    }

}
