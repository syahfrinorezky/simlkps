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
$routes->group('users', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'UserController::index');
    $routes->post('store', 'UserController::store');
    $routes->get('edit/(:num)', 'UserController::edit/$1');
    $routes->post('update/(:num)', 'UserController::update/$1');
    $routes->post('toggle/(:num)', 'UserController::toggleActive/$1');
    $routes->get('delete/(:num)', 'UserController::destroy/$1');
    $routes->get('restore/(:num)', 'UserController::restore/$1');
    $routes->get('purge/(:num)', 'UserController::purge/$1');
});

$routes->group('cooperations', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'CooperationController::index');
    $routes->get('education', 'CooperationController::education');
    $routes->get('research', 'CooperationController::research');
    $routes->get('community', 'CooperationController::community');
    $routes->get('create', 'CooperationController::create');
    $routes->post('store', 'CooperationController::store');
    $routes->get('edit/(:any)', 'CooperationController::edit/$1');
    $routes->post('update/(:any)', 'CooperationController::update/$1');
    $routes->get('delete/(:any)', 'CooperationController::delete/$1');
    $routes->get('detail/(:any)', 'CooperationController::detail/$1');
    $routes->get('download/(:any)', 'CooperationController::download/$1');
});

$routes->group('students', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'StudentController::index');

    $routes->post('store-admission', 'StudentController::storeAdmission');
    $routes->get('detail-admission/(:any)', 'StudentController::detailAdmission/$1');
    $routes->post('update-admission/(:any)', 'StudentController::updateAdmission/$1');
    $routes->get('delete-admission/(:any)', 'StudentController::deleteAdmission/$1');
    $routes->get('export-admission', 'StudentController::exportAdmission');
    $routes->post('import-admission', 'StudentController::importAdmission');

    $routes->post('store-foreign', 'StudentController::storeForeign');
    $routes->get('detail-foreign/(:any)', 'StudentController::detailForeign/$1');
    $routes->post('update-foreign/(:any)', 'StudentController::updateForeign/$1');
    $routes->get('delete-foreign/(:any)', 'StudentController::deleteForeign/$1');
});

$routes->group('lecturers', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function ($routes) {

    $routes->get('permanent', 'LecturerController::permanent');
    $routes->post('permanent/store', 'LecturerController::storePermanent');
    $routes->get('permanent/show/(:any)', 'LecturerController::showPermanent/$1');
    $routes->post('permanent/update/(:any)', 'LecturerController::updatePermanent/$1');
    $routes->get('permanent/delete/(:any)', 'LecturerController::deletePermanent/$1');

    $routes->get('supervisor', 'LecturerController::supervisor');
    $routes->post('supervisor/store', 'LecturerController::storeSupervisor');
    $routes->get('supervisor/show/(:any)', 'LecturerController::showSupervisor/$1');
    $routes->post('supervisor/update/(:any)', 'LecturerController::updateSupervisor/$1');
    $routes->get('supervisor/delete/(:any)', 'LecturerController::deleteSupervisor/$1');

    $routes->get('non-permanent', 'LecturerController::nonPermanent');
    $routes->post('non-permanent/store', 'LecturerController::storeNonPermanent');
    $routes->get('non-permanent/show/(:any)', 'LecturerController::showNonPermanent/$1');
    $routes->post('non-permanent/update/(:any)', 'LecturerController::updateNonPermanent/$1');
    $routes->get('non-permanent/delete/(:any)', 'LecturerController::deleteNonPermanent/$1');

    $routes->get('industry', 'LecturerController::industry');
    $routes->post('industry/store', 'LecturerController::storeIndustry');
    $routes->get('industry/show/(:any)', 'LecturerController::showIndustry/$1');
    $routes->post('industry/update/(:any)', 'LecturerController::updateIndustry/$1');
    $routes->get('industry/delete/(:any)', 'LecturerController::deleteIndustry/$1');

    $routes->get('workload', 'LecturerController::workload');
    $routes->post('workload/store', 'LecturerController::storeWorkload');
    $routes->get('workload/show/(:any)', 'LecturerController::showWorkload/$1');
    $routes->post('workload/update/(:any)', 'LecturerController::updateWorkload/$1');
    $routes->get('workload/delete/(:any)', 'LecturerController::deleteWorkload/$1');

    $routes->get('recognition', 'LecturerController::recognition');
    $routes->post('recognition/store', 'LecturerController::storeRecognition');
    $routes->get('recognition/show/(:any)', 'LecturerController::showRecognition/$1');
    $routes->post('recognition/update/(:any)', 'LecturerController::updateRecognition/$1');
    $routes->get('recognition/delete/(:any)', 'LecturerController::deleteRecognition/$1');

    $routes->get('research-performance', 'LecturerController::researchPerformance');
    $routes->post('research-performance/store', 'LecturerController::storeResearch');
    $routes->get('research-performance/show/(:any)', 'LecturerController::showResearch/$1');
    $routes->post('research-performance/update/(:any)', 'LecturerController::updateResearch/$1');
    $routes->get('research-performance/delete/(:any)', 'LecturerController::deleteResearch/$1');

    $routes->get('community-service', 'LecturerController::communityService');
    $routes->post('community-service/store', 'LecturerController::storePkm');
    $routes->get('community-service/show/(:any)', 'LecturerController::showPkm/$1');
    $routes->post('community-service/update/(:any)', 'LecturerController::updatePkm/$1');
    $routes->get('community-service/delete/(:any)', 'LecturerController::deletePkm/$1');

    $routes->get('publications/scientific', 'LecturerController::publications');
    $routes->post('publications/store', 'LecturerController::storePublication');
    $routes->get('publications/show/(:any)', 'LecturerController::showPublication/$1');
    $routes->post('publications/update/(:any)', 'LecturerController::updatePublication/$1');
    $routes->get('publications/delete/(:any)', 'LecturerController::deletePublication/$1');

    $routes->get('publications/creative-works', 'LecturerController::citations');
    $routes->post('citations/store', 'LecturerController::storeCitation');
    $routes->get('citations/show/(:any)', 'LecturerController::showCitation/$1');
    $routes->post('citations/update/(:any)', 'LecturerController::updateCitation/$1');
    $routes->get('citations/delete/(:any)', 'LecturerController::deleteCitation/$1');

    $routes->get('hki/industry-products', 'LecturerController::products');
    $routes->post('products/store', 'LecturerController::storeProduct');
    $routes->get('products/show/(:any)', 'LecturerController::showProduct/$1');
    $routes->post('products/update/(:any)', 'LecturerController::updateProduct/$1');
    $routes->get('products/delete/(:any)', 'LecturerController::deleteProduct/$1');

    $routes->get('outputs', 'LecturerController::outputs');
    $routes->post('outputs/store', 'LecturerController::storeOutput');
    $routes->get('outputs/show/(:any)', 'LecturerController::showOutput/$1');
    $routes->post('outputs/update/(:any)', 'LecturerController::updateOutput/$1');
    $routes->get('outputs/delete/(:any)', 'LecturerController::deleteOutput/$1');
});
