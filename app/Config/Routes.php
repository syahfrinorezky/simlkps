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
