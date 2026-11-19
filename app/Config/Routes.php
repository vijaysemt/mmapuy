
<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Home
$routes->get('/', 'HomeController::index');

// Scheme AJAX Routes
$routes->group('schemes', static function ($routes) {
    $routes->post('list', 'HomeController::getAllSchemes');
    $routes->post('list-by-id', 'HomeController::getSchemesById');
    $routes->post('details-by-id', 'HomeController::getSchemeDetailsById');
});

// Document Routes
$routes->get('download-documents', 'HomeController::downloadDocuments');

// Authentication Routes
$routes->get('login', 'LoginController::index');
$routes->get('password-login', 'LoginController::passwordLogin');

// OTP Authentication
$routes->post('login/validate-otp', 'LoginController::validateOtp');
// Mobile OTP journey
// $routes->post('/show-otp', 'LoginController::showOtp');
// $routes->get('/mobile-otp', 'LoginController::mobileOtp');
// $routes->post('/demo-verify-otp', 'LoginController::demoVerifyOtp');

// // Password login journey
// $routes->post('/demo-password-login', 'LoginController::demoPasswordLogin');

// // Dashboard
// $routes->get('/dashboard', 'LoginController::dashboard');




$routes->group('auth', ['namespace' => 'App\Controllers\Auth'], static function ($routes) {

    // OTP login
    $routes->get('login', 'AuthController::index');
    $routes->post('send-otp', 'AuthController::sendOtp');

    $routes->get('verify-otp', 'AuthController::otpPage');
    $routes->post('verify-otp', 'AuthController::verifyOtp');

    // Password login
    $routes->get('password-login', 'AuthController::passwordPage');
    $routes->post('password-login', 'AuthController::passwordLogin');

    // Set password
    $routes->get('set-password', 'AuthController::setPasswordPage');
    $routes->post('set-password', 'AuthController::setPassword');

    // Logout
    $routes->get('logout', 'AuthController::logout');
});

// Friendly URLs
// $routes->get('login', '\App\Controllers\Auth\AuthController::index');
// $routes->get('password-login', '\App\Controllers\Auth\AuthController::passwordPage');

$routes->group('', ['filter' => 'auth'], function ($routes) {

    $routes->get('district-user/dashboard', 'Dashboard\DashboardController::index', ['filter' => 'role:1']);
    $routes->get('department-user/view_dashboard', 'Dashboard\DashboardController::index', ['filter' => 'role:3']);
    $routes->get('cm/dashboard', 'Dashboard\DashboardController::index');
    $routes->get('department-district-user/view_dashboard', 'Dashboard\DashboardController::index', ['filter' => 'role:6']);
    $routes->get('adc-user/dashboard', 'Dashboard\DashboardController::index', ['filter' => 'role:8']);
    $routes->get('super-admin/dashboard', 'SuperAdminController::index', ['filter' => 'role:9']);
    $routes->get('slbc-admin/dashboard', 'Dashboard\DashboardController::index', ['filter' => 'role:10']);
    $routes->get('ldm-user/dashboard', 'Dashboard\DashboardController::index', ['filter' => 'role:11']);
    $routes->get('controlling-heads/dashboard', 'Dashboard\DashboardController::index', ['filter' => 'role:18']);
});

// Dashboard statistics API
$routes->get('api/super-admin/dashboard-stats', 'SuperAdminController::stats');




$routes->group('super-admin', static function ($routes) {

    $routes->get('district-report-dashboard',             'SuperAdminController::districtReportDashboard');
    $routes->get('district-report-family-wise', 'SuperAdminController::districtReportFamilyWise');
    $routes->get('district-report-member-wise', 'SuperAdminController::districtReportMemberWise');

    $routes->get('department-wise-report',    'SuperAdminController::departmentWiseReport');

    $routes->get('bank-dashboard',            'SuperAdminController::bankDashboard');
    $routes->get('bank-wise-report',   'SuperAdminController::aplicationsCountBankWise');
    $routes->get('branch-wise-report', 'SuperAdminController::aplicationsCountBranchWise');


    $routes->get('search-by-family-id', 'SuperAdminController::searchByFamilyId');

    $routes->get('schemewise-report', 'SuperAdminController::schemeWiseReport');

    $routes->get('calling-status', 'SuperAdminController::callingStatus');

    $routes->get('historical-data', 'SuperAdminController::historicalData');

    $routes->get('docs-requests-dashboard', 'SuperAdminController::docsRequestsDashboard');
});
