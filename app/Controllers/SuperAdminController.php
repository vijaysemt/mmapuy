<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CommonModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SuperAdminModel;
use CodeIgniter\HTTP\RedirectResponse;

class SuperAdminController extends BaseController
{

    protected SuperAdminModel $dashboardModel;
    private const GROUPS = [
        'overview',
        'highlights',
        'families',
        'members',
        'adc',
        'mela',
        'applications',
        'reported',
    ];

    /** Old CI3 $this->all_versions. Change if your value is different. */
    private const ALL_VERSIONS = '1,2';

    /** Cache lifetime in seconds. Use ?refresh=1 to bypass. */
    private const CACHE_TTL = 300;

    /** Pages these report links point to (convert / adjust to your CI4 routes). */
    private const LINKS = [
        'district_family' => 'super-admin/view-district-dashboard',
        'district_member' => 'super-admin/view-district-dashboard-member',
        'pending'         => 'super-admin/view-pending-list',
    ];

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

        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => false,
                'message' => 'Session expired. Please login again.',
            ]);
        }

        // Release the session lock so parallel tab requests are not queued
        session_write_close();

        $version = (string) $this->request->getGet('v');
        $group   = (string) $this->request->getGet('group');
        $refresh = $this->request->getGet('refresh') === '1';

        if (!in_array($version, ['1', '2'], true) || !in_array($group, self::GROUPS, true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => false,
                'message' => 'Invalid version or group.',
            ]);
        }

        try {
            $cache    = cache();
            $cacheKey = "sa_dash_v{$version}_{$group}";
            $data     = $refresh ? null : $cache->get($cacheKey);
            $fromCache = $data !== null;

            if ($data === null) {
                $data = $this->dashboardModel->getGroupStats($group, $version);
                $cache->save($cacheKey, $data, 600); // 10 minutes
            }

            return $this->response->setJSON([
                'status'  => true,
                'cached'  => $fromCache,
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Super Admin Dashboard API Error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status'  => false,
                'message' => 'Unable to load dashboard statistics.',
            ]);
        }
    }


    /**
     * ADD TO: app/Controllers/SuperAdminController.php
     *
     * 1. Add these two lines under the existing `use` statements:
     *        use App\Models\CommonModel;
     *        use CodeIgniter\HTTP\RedirectResponse;
     *
     * 2. Paste everything below inside the class, before the last closing brace.
     *    It uses the existing $this->dashboardModel (SuperAdminModel).
     *
     * Replaces the separate DistrictReportController.
     */



    public function districtReportDashboard(): string|RedirectResponse
    {
        if ($redirect = $this->reportGuard()) {
            return $redirect;
        }

        $v = trim((string) $this->request->getGet('v'));

        return view('pages/super_admin/district_report_dashboard', $this->reportBaseData() + [
            'version' => preg_match('/^[12](,[12])?$/', $v) === 1 ? $v : '1',
        ]);
    }

    public function districtReportFamilyWise(): string|RedirectResponse
    {
        if ($redirect = $this->reportGuard()) {
            return $redirect;
        }

        $version = $this->resolveReportVersion();
        $key     = 'rep_family_' . str_replace(',', '_', $version);

        $rows = $this->cachedReport($key, fn(): array => $this->dashboardModel->getFamilyWise($version));

        // Old behaviour: mela_phase=6 shows only district 08 (the Total row stays).
        if ((string) $this->request->getGet('mela_phase') === '6') {
            $rows = array_values(array_filter(
                $rows,
                static fn(array $r): bool => in_array((string) $r['id'], ['08', ''], true)
            ));
        }

        return view('pages/super_admin/district_report_family_wise', $this->reportBaseData() + [
            'rows'    => $rows,
            'version' => $version,
            'urls'    => $this->reportLinks(),
        ]);
    }

    public function districtReportMemberWise(): string|RedirectResponse
    {
        if ($redirect = $this->reportGuard()) {
            return $redirect;
        }

        $version  = $this->resolveReportVersion();
        $selected = (string) $this->request->getGet('mela_phase');

        $common = new CommonModel();
        $phase  = $common->parseMelaPhaseSelection($selected);

        $key = sprintf(
            'rep_member_%s_%d_%d',
            str_replace(',', '_', $version),
            $phase['mela_phase'],
            $phase['is_ssa']
        );

        $rows = $this->cachedReport(
            $key,
            fn(): array => $this->dashboardModel->getMemberWise(
                $version,
                $phase['mela_phase'],
                $phase['is_ssa']
            )
        );

        return view('pages/super_admin/district_report_member_wise', $this->reportBaseData() + [
            'rows'                => $rows,
            'version'             => $version,
            'selected_version'    => $this->request->getGet('v') ?? '',
            'selected_mela_phase' => $selected,
            'mela_phases'         => $common->getMelaPhases($version),
            'urls'                => $this->reportLinks(),
        ]);
    }

    // ------------------------------------------------------------------

    /** Same session check as SuperAdminController. */
    private function reportGuard(): ?RedirectResponse
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        // Reports can be slow: release the session lock so other tabs are not blocked.
        session_write_close();

        return null;
    }

    /** Data needed by layouts/dashboard (header + footer). */
    private function reportBaseData(): array
    {
        $session = session();
        $role    = (string) $session->get('roleid');

        return [
            'role'       => $role,
            'user'       => $session->get('userdata') ?? [],
            'headerOnly' => in_array($role, config('AuthRoles')->headerOnlyRoles, true),
        ];
    }

    /** "1", "2" or "1,2". Empty / "0" / invalid => all versions (old behaviour). */
    private function resolveReportVersion(): string
    {
        $v = trim((string) $this->request->getGet('v'));

        if ($v === '' || $v === '0') {
            return self::ALL_VERSIONS;
        }

        return preg_match('/^[12](,[12])?$/', $v) === 1 ? $v : self::ALL_VERSIONS;
    }

    private function cachedReport(string $key, \Closure $callback): array
    {
        $cache = cache();

        if ($this->request->getGet('refresh') === '1') {
            $cache->delete($key);
        }

        return $cache->remember($key, self::CACHE_TTL, $callback);
    }

    /** @return array<string,string> */
    private function reportLinks(): array
    {
        return array_map(static fn(string $path): string => site_url($path), self::LINKS);
    }



    public function departmentWiseReport(): string|RedirectResponse
    {
        if ($redirect = $this->reportGuard()) {
            return $redirect;
        }

        $version  = $this->resolveReportVersion();
        $selected = (string) $this->request->getGet('mela_phase');

        $common = new CommonModel();
        $phase  = $common->parseMelaPhaseSelection($selected);

        $key = sprintf(
            'rep_dept_%s_%d_%d',
            str_replace(',', '_', $version),
            $phase['mela_phase'],
            $phase['is_ssa']
        );

        $rows = $this->cachedReport(
            $key,
            fn(): array => $this->dashboardModel->getDepartmentWise(
                $version,
                $phase['mela_phase'],
                $phase['is_ssa']
            )
        );

        return view('pages/super_admin/department_wise_report', $this->reportBaseData() + [
            'rows'                => $rows,
            'version'             => $version,
            'selected_version'    => $this->request->getGet('v') ?? '',
            'selected_mela_phase' => $selected,
            'mela_phases'         => $common->getMelaPhases($version),
        ]);
    }


    public function bankDashboard(): string|RedirectResponse
    {
        if ($redirect = $this->reportGuard()) {
            return $redirect;
        }

        $v = trim((string) $this->request->getGet('v'));

        return view('pages/super_admin/bank_dashboard', $this->reportBaseData() + [
            'version' => preg_match('/^[12](,[12])?$/', $v) === 1 ? $v : '1',
        ]);
    }

    public function bankWiseReport(): string|RedirectResponse
    {
        if ($redirect = $this->reportGuard()) {
            return $redirect;
        }

        $version    = $this->resolveReportVersion();
        $districtId = $this->resolveDistrictParam('');          // default: all districts
        $selected   = (string) $this->request->getGet('mela_phase');

        $common = new CommonModel();
        $phase  = $common->parseMelaPhaseSelection($selected);

        $key = sprintf(
            'rep_bank_%s_%s_%d_%d',
            str_replace(',', '_', $version),
            $districtId === '' ? 'all' : $districtId,
            $phase['mela_phase'],
            $phase['is_ssa']
        );

        $rows = $this->cachedReport(
            $key,
            fn(): array => $this->dashboardModel->getAplicationsCountBankWise(
                $version,
                $districtId,
                $phase['mela_phase'],
                $phase['is_ssa']
            )
        );

        return view('pages/super_admin/bank_wise_report', $this->reportBaseData() + [
            'rows'                => $rows,
            'version'             => $version,
            'district_list'       => $this->dashboardModel->getDistrictOptions(),
            'selected_district'   => (string) $this->request->getGet('d'),
            'selected_version'    => $this->request->getGet('v') ?? '',
            'selected_mela_phase' => $selected,
            'mela_phases'         => $common->getMelaPhases($version),
        ]);
    }

    public function branchWiseReport(): string|RedirectResponse
    {
        if ($redirect = $this->reportGuard()) {
            return $redirect;
        }

        $version    = $this->resolveReportVersion();
        $districtId = $this->resolveDistrictParam('01');        // old default district is 01
        $selected   = (string) $this->request->getGet('mela_phase');

        $common = new CommonModel();
        $phase  = $common->parseMelaPhaseSelection($selected);

        $key = sprintf(
            'rep_branch_%s_%s_%d_%d',
            str_replace(',', '_', $version),
            $districtId === '' ? 'all' : $districtId,
            $phase['mela_phase'],
            $phase['is_ssa']
        );

        $rows = $this->cachedReport(
            $key,
            fn(): array => $this->dashboardModel->getAplicationsCountBranchWise(
                $version,
                $districtId,
                $phase['mela_phase'],
                $phase['is_ssa']
            )
        );

        return view('pages/super_admin/branch_wise_report', $this->reportBaseData() + [
            'rows'                => $rows,
            'version'             => $version,
            'district_list'       => $this->dashboardModel->getDistrictOptions(),
            'selected_district'   => (string) $this->request->getGet('d'),
            'selected_version'    => $this->request->getGet('v') ?? '',
            'selected_mela_phase' => $selected,
            'mela_phases'         => $common->getMelaPhases($version),
        ]);
    }

    /**
     * ?d=all  => '' (all districts); ?d=01 => '01'; missing / invalid => $default.
     */
    private function resolveDistrictParam(string $default): string
    {
        $d = trim((string) $this->request->getGet('d'));

        if ($d === 'all') {
            return '';
        }

        if ($d === '' || $d === '0') {
            return $default;
        }

        return preg_match('/^[A-Za-z0-9_-]{1,10}$/', $d) === 1 ? $d : $default;
    }



    private const SCHEME_TYPES = [
        1 => ['LOAN BASED SCHEMES',                  'ऋण आधारित योजनाएँ',              'bi-cash-coin',        'warning'],
        2 => ['SKILL DEVELOPMENT BASED SCHEMES',     'कौशल विकास आधारित योजनाएँ',       'bi-mortarboard',      'info'],
        3 => ['TRAINING CUM LOAN BASED SCHEMES',     'प्रशिक्षण सह ऋण आधारित योजनाएँ',  'bi-journal-check',    'primary'],
        4 => ['EMPLOYMENT BASED SCHEMES',            'रोजगार आधारित योजनाएँ',           'bi-briefcase',        'success'],
        5 => ['WAGE EMPLOYMENT BASED SCHEMES',       'वेतन रोज़गार आधारित योजनाएँ',      'bi-wallet2',          'info'],
        6 => ['Establishment based scheme',          'स्थापना आधारित योजना',            'bi-shop',             'danger'],
    ];

    /** Opened from the "Schemewise Reports" card on the dashboard. */
    public function schemeReportDashboard(): string|RedirectResponse
    {
        if ($redirect = $this->reportGuard()) {
            return $redirect;
        }

        $v = trim((string) $this->request->getGet('v'));

        return view('pages/super_admin/scheme_type_dashboard', $this->reportBaseData() + [
            'version' => preg_match('/^[12](,[12])?$/', $v) === 1 ? $v : '1',
            'types'   => self::SCHEME_TYPES,
        ]);
    }

    public function schemeWiseReport(): string|RedirectResponse
    {
        if ($redirect = $this->reportGuard()) {
            return $redirect;
        }

        $version = $this->resolveReportVersion();
        $type    = (int) $this->request->getGet('type');

        if (! isset(self::SCHEME_TYPES[$type])) {
            return redirect()->to(site_url('super-admin/scheme-report-dashboard?v=' . $version));
        }

        $key = sprintf('rep_scheme_%s_%d', str_replace(',', '_', $version), $type);

        $rows = $this->cachedReport(
            $key,
            fn(): array => $this->dashboardModel->getSchemeDepartmentWiseReport(
                $this->dashboardModel->getSchemeIdsByType($type),
                $version
            )
        );

        return view('pages/super_admin/scheme_wise_report', $this->reportBaseData() + [
            'rows'    => $rows,
            'version' => $version,
            'type'    => $type,
            'title'   => self::SCHEME_TYPES[$type],
        ]);
    }
}
