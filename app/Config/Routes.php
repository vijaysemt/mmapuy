<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');



// AJAX routes
$routes->post('Home/allScheme_list', 'Home::allScheme_list');
$routes->post('Home/allScheme_list_byId', 'Home::allScheme_list_byId');
$routes->post('Home/SingleSchemelistbyId', 'Home::SingleSchemelistbyId');
$routes->get('/download-documents', 'Home::downloadDocuments');

$routes->get('/login', 'Login::index');
$routes->get('/password-login', 'Login::passwordLogin');

// Mobile OTP journey
$routes->post('/show-otp', 'Login::showOtp');
$routes->get('/mobile-otp', 'Login::mobileOtp');
$routes->post('/demo-verify-otp', 'Login::demoVerifyOtp');

// Password login journey
$routes->post('/demo-password-login', 'Login::demoPasswordLogin');

// Dashboard
$routes->get('/dashboard', 'Login::dashboard');