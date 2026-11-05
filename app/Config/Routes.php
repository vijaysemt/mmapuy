
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

    $routes->get('District_user/dashboard', 'Dashboard\DashboardController::index',['filter' => 'role:1']);
    $routes->get('Department_user/view_dashboard', 'Dashboard\DashboardController::index',['filter' => 'role:3']);
    $routes->get('Cm/dashboard', 'Dashboard\DashboardController::index');
    $routes->get('Department_district_user/view_dashboard', 'Dashboard\DashboardController::index',['filter' => 'role:6']);
    $routes->get('ADC_user/dashboard', 'Dashboard\DashboardController::index',['filter' => 'role:8']);
    $routes->get('Super_admin/dashboard', 'Dashboard\DashboardController::index',['filter' => 'role:9']);
    $routes->get('Slbc_admin/dashboard', 'Dashboard\DashboardController::index',['filter' => 'role:10']);
    $routes->get('LDM_user/dashboard', 'Dashboard\DashboardController::index',['filter' => 'role:11']);
    $routes->get('Controlling_heads/dashboard', 'Dashboard\DashboardController::index',['filter' => 'role:18']);

});

