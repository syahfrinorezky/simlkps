<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'DashboardController::index');
$routes->get('/login', ['App\Controllers\Auth\AuthController', 'login']);
$routes->post('/login/attempt', ['App\Controllers\Auth\AuthController', 'attempt']);
$routes->get('/logout', ['App\Controllers\Auth\AuthController', 'logout']);

$routes->get('/forgot-password', ['App\Controllers\Auth\ResetPasswordController', 'forgotPassword']);
$routes->post('/forgot-password', ['App\Controllers\Auth\ResetPasswordController', 'sendResetLink']);
$routes->get('/reset-password/(:any)', ['App\Controllers\Auth\ResetPasswordController', 'resetPassword/$1']);
$routes->post('/reset-password', ['App\Controllers\Auth\ResetPasswordController', 'updatePassword']);

// user management
$routes->group('users', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'UserController::index');
    $routes->post('store', 'UserController::store');
    $routes->get('edit/(:num)', 'UserController::edit/$1');
    $routes->post('update/(:num)', 'UserController::update/$1');
    $routes->post('toggle/(:num)', 'UserController::toggleActive/$1');
    $routes->get('delete/(:num)', 'UserController::destroy/$1');
    $routes->get('restore/(:num)', 'UserController::restore/$1');
    $routes->get('purge/(:num)', 'UserController::purge/$1');
});
