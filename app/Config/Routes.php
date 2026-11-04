
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
