<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// Public Web Routes
// --------------------------------------------------------------------
$routes->get('/', 'Home::index');

// Auth
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::authenticate');

$routes->get('registration', 'Registration::index');
$routes->post('registration', 'Registration::store');

$routes->get('logout', 'Auth::logout');

// --------------------------------------------------------------------
// Mobile API (NO AUTH REQUIRED)
// --------------------------------------------------------------------
$routes->post('api/login', 'Api\Auth::login');
$routes->post('api/register', 'Api\Auth::register');

// TEMP / PUBLIC (for testing or public data)
$routes->get('api/diseases', 'Api\Diseases::index');
$routes->get('api/diseases/(:num)', 'Api\Diseases::show/$1');

// Test route (optional – remove in prod)
$routes->get('api/diseases/test', function () {
    return 'Diseases route is working!';
});

// --------------------------------------------------------------------
// Mobile API (AUTH REQUIRED – TOKEN)
// --------------------------------------------------------------------
$routes->group('api', ['filter' => 'apiauth'], function ($routes) {

    // Stats
    $routes->get('stats', 'Api\Stats::index');

    // Pests
    $routes->get('pests', 'Api\Pests::index');
    $routes->get('pests/(:num)', 'Api\Pests::show/$1');

    // Diseases
    $routes->get('diseases', 'Api\Diseases::index');
    $routes->get('diseases/(:num)', 'Api\Diseases::show/$1');

    // User / Profile
    $routes->get('user/profile', 'Api\Profile::index');
    $routes->post('user/profile/change-password', 'Api\Profile::changePassword');
    $routes->post('user/update', 'Api\User::updateProfile');

    // Diagnosis
    $routes->post('diagnosis/upload', 'Api\Diagnosis::upload');
    $routes->get('diagnosis/history', 'Api\Diagnosis::history');

    // Feedback
    $routes->post('feedback', 'Api\Feedback::create');
    $routes->get('feedback', 'Api\Feedback::index');
    $routes->get('feedback/user', 'Api\Feedback::user');

    // Logout
    $routes->post('logout', 'Api\Auth::logout');
});

// --------------------------------------------------------------------
// Prediction API (optional / legacy)
// --------------------------------------------------------------------
$routes->group('api/prediction', ['namespace' => 'App\Controllers\API'], function ($routes) {
    $routes->post('save', 'Prediction::save');
    $routes->get('disease/(:num)', 'Prediction::get_disease/$1');
    $routes->get('diseases', 'Prediction::get_all_diseases');
});

// --------------------------------------------------------------------
// Protected Web Routes (SESSION AUTH + ROLE)
// --------------------------------------------------------------------
$routes->group('', ['filter' => ['auth', 'role']], function (RouteCollection $routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('profile', 'Dashboard::profile');
$routes->get('settings', 'Dashboard::settings');
// Settings POST routes
$routes->post('settings/update-personal', 'Dashboard::updatePersonal');
$routes->post('settings/update-password', 'Dashboard::updatePassword');
$routes->post('settings/update-notifications', 'Dashboard::updateNotifications');
$routes->post('settings/update-preferences', 'Dashboard::updatePreferences');
$routes->get('settings/delete-account', 'Dashboard::deleteAccount');

    // Images
    $routes->get('images', 'Images::index');
    $routes->post('images/upload/(:segment)', 'Images::upload/$1'); // Admin only (controller enforced)

    // Diseases
    $routes->get('disease', 'Disease::index');
    $routes->post('diseases/store', 'Disease::store');
    $routes->post('diseases/update', 'Disease::update');
    $routes->post('diseases/delete', 'Disease::delete');

    // Diagnosis (view only)
    $routes->get('diagnosis', 'Diagnosis::index');

    // Users (ADMIN)
    $routes->get('users', 'Users::index');
    $routes->get('users/create', 'Users::create');
    $routes->post('users/store', 'Users::store');
    $routes->get('users/edit/(:num)', 'Users::edit/$1');
    $routes->post('users/update/(:num)', 'Users::update/$1');
    $routes->post('users/update', 'Users::update');
    $routes->post('users/delete', 'Users::delete');

    // Logs
    $routes->get('activity_log', 'Logs::index');

    // Pests (ADMIN)
    $routes->get('pests', 'Pests::index');
    $routes->get('pests/create', 'Pests::create');
    $routes->post('pests/store', 'Pests::store');
    $routes->get('pests/edit/(:num)', 'Pests::edit/$1');
    $routes->post('pests/update/(:num)', 'Pests::update/$1');
    $routes->post('pests/update', 'Pests::update');
    $routes->post('pests/delete', 'Pests::delete');
});
