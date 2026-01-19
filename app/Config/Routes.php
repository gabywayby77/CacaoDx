<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// Public routes
// --------------------------------------------------------------------
$routes->get('/', 'Home::index');

// Auth
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::authenticate');

$routes->get('registration', 'Registration::index');
$routes->post('registration', 'Registration::store');

$routes->get('logout', 'Auth::logout');


// Registration
$routes->get('registration', 'Registration::index');
$routes->post('registration', 'Registration::store');

// Diagnosis (PUBLIC – fixes 403)
$routes->get('diagnosis', 'Diagnosis::index');

// --------------------------------------------------------------------
// Mobile API
// --------------------------------------------------------------------
$routes->post('api/login', 'Api\Auth::login');
$routes->post('api/register', 'Api\Auth::register');

// --------------------------------------------------------------------
// Protected routes (AUTH REQUIRED)
// --------------------------------------------------------------------
$routes->group('', ['filter' => 'auth'], function (RouteCollection $routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Users
    $routes->get('users', 'Users::index');
    $routes->get('users/create', 'Users::create');
    $routes->post('users/store', 'Users::store');
    $routes->get('users/edit/(:num)', 'Users::edit/$1');
    $routes->post('users/update/(:num)', 'Users::update/$1');
    $routes->post('users/delete', 'Users::delete');
    $routes->get('users', 'Users::index');

$routes->post('users/update', 'Users::update');


    // Logs
    $routes->get('activity_log', 'Logs::index');

    // Disease
    $routes->get('/disease', 'Disease::index');
    $routes->post('/diseases/store', 'Disease::store');
    $routes->post('/diseases/update', 'Disease::update');
    $routes->post('/diseases/delete', 'Disease::delete');
    

    // Pests
    $routes->get('pests', 'Pests::index');
    $routes->get('pests/create', 'Pests::create');
    $routes->post('pests/store', 'Pests::store');
    $routes->get('pests/edit/(:num)', 'Pests::edit/$1');
    $routes->post('pests/update/(:num)', 'Pests::update/$1');
    $routes->post('pests/delete', 'Pests::delete');
    $routes->get('pests', 'Pests::index');
    $routes->post('pests/store', 'Pests::store');
    $routes->post('pests/update', 'Pests::update');
    
    // Images
// Images
$routes->get('images', 'Images::index');
$routes->post('images/upload/(:segment)', 'Images::upload/$1');

    

    
});
