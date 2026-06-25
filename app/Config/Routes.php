<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Landing page
$routes->get('/', 'LandingController::index');

// API routes
$routes->group('api', function ($routes) {
    // Auth
    $routes->post('auth/register', 'Api\AuthController::register');
    $routes->post('auth/login', 'Api\AuthController::login');
    $routes->post('auth/firebase-token', 'Api\AuthController::updateFirebaseToken');

    // Check-ins
    $routes->post('checkin', 'Api\CheckInController::create');
    $routes->get('checkin/history', 'Api\CheckInController::history');
    $routes->get('checkin/stats', 'Api\CheckInController::stats');
    $routes->get('checkin/latest', 'Api\CheckInController::latestForGroup');

    // Family groups
    $routes->post('family/create', 'Api\FamilyController::createGroup');
    $routes->post('family/join', 'Api\FamilyController::joinGroup');
    $routes->get('family/groups', 'Api\FamilyController::getGroups');
    $routes->get('family/members', 'Api\FamilyController::getMembers');
    $routes->post('family/leave', 'Api\FamilyController::leaveGroup');

    // Alerts & notifications
    $routes->get('alerts', 'Api\AlertController::getAlerts');
    $routes->get('alerts/active', 'Api\AlertController::getActive');
    $routes->post('alerts/resolve', 'Api\AlertController::resolve');
    $routes->get('notifications', 'Api\AlertController::getNotifications');
    $routes->post('notifications/read', 'Api\AlertController::markRead');
    $routes->post('notifications/read-all', 'Api\AlertController::markAllRead');

    // Settings & profile
    $routes->get('settings', 'Api\SettingsController::getSettings');
    $routes->post('settings', 'Api\SettingsController::updateSettings');
    $routes->get('profile', 'Api\SettingsController::getProfile');
    $routes->post('profile', 'Api\SettingsController::updateProfile');
});

// Web check-in (for users without phones)
$routes->get('checkin/web', 'WebCheckInController::index');
$routes->post('checkin/web', 'WebCheckInController::submit');

// Cron
$routes->get('cron/checkins', 'CronController::checkIns');

// Admin panel
$routes->group('admin', function ($routes) {
    $routes->get('login', 'Admin\AuthController::login');
    $routes->post('login', 'Admin\AuthController::attemptLogin');
    $routes->get('logout', 'Admin\AuthController::logout');

    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/(:num)', 'Admin\UserController::view/$1');
    $routes->post('users/(:num)/toggle', 'Admin\UserController::toggle/$1');
    $routes->get('groups', 'Admin\GroupController::index');
    $routes->get('groups/(:num)', 'Admin\GroupController::view/$1');
    $routes->get('alerts', 'Admin\AlertController::index');
    $routes->get('checkins', 'Admin\CheckInController::index');
    $routes->get('settings', 'Admin\SettingsController::index');
    $routes->post('settings', 'Admin\SettingsController::save');
});
