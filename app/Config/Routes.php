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

$routes->post('Zone_user/validate', 'Zone_user::validate');
$routes->post('Login/validateotp', 'Login::validateotp');