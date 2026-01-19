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

// --------------------------------------------------------------------
// Mobile API
// --------------------------------------------------------------------
$routes->post('api/login', 'Api\Auth::login');
$routes->post('api/register', 'Api\Auth::register');

// --------------------------------------------------------------------
// Protected routes (AUTH REQUIRED + ROLE CHECK)
// --------------------------------------------------------------------
$routes->group('', ['filter' => ['auth', 'role']], function (RouteCollection $routes) {

    // ✅ AVAILABLE TO ALL AUTHENTICATED USERS
    $routes->get('dashboard', 'Dashboard::index');
    
    // Images (view only for users, upload for admins)
    $routes->get('images', 'Images::index');
    $routes->post('images/upload/(:segment)', 'Images::upload/$1'); // Admin only (checked in controller)
    
    // Disease (view only for users, CRUD for admins)
    $routes->get('disease', 'Disease::index');
    $routes->post('diseases/store', 'Disease::store');   // Admin only
    $routes->post('diseases/update', 'Disease::update'); // Admin only
    $routes->post('diseases/delete', 'Disease::delete'); // Admin only
    
    // Diagnosis (read-only for all)
    $routes->get('diagnosis', 'Diagnosis::index');

    // ✅ ADMIN ONLY PAGES (blocked by RoleFilter)
    $routes->get('users', 'Users::index');
    $routes->get('users/create', 'Users::create');
    $routes->post('users/store', 'Users::store');
    $routes->get('users/edit/(:num)', 'Users::edit/$1');
    $routes->post('users/update/(:num)', 'Users::update/$1');
    $routes->post('users/update', 'Users::update');
    $routes->post('users/delete', 'Users::delete');

    $routes->get('activity_log', 'Logs::index');

    $routes->get('pests', 'Pests::index');
    $routes->get('pests/create', 'Pests::create');
    $routes->post('pests/store', 'Pests::store');
    $routes->get('pests/edit/(:num)', 'Pests::edit/$1');
    $routes->post('pests/update/(:num)', 'Pests::update/$1');
    $routes->post('pests/update', 'Pests::update');
    $routes->post('pests/delete', 'Pests::delete');
});