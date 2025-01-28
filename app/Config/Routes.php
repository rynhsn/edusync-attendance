<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AttendanceController::attendance');

// group attendance
$routes->group('attendance', function ($routes) {
    $routes->get('/', 'AttendanceController::index');
    $routes->post('tap', 'AttendanceController::tap');
    $routes->get('show', 'AttendanceController::show');
});

// panel
$routes->get('/panel', 'PanelController::index');

//group student
$routes->group('student', function ($routes) {
    $routes->get('/', 'StudentController::index');
    $routes->get('show', 'StudentController::show');
    // $routes->get('(:num)', 'StudentController::show/$1');
    // $routes->post('new', 'StudentController::new');
    // $routes->post('create', 'StudentController::create');
    // $routes->get('edit/(:num)', 'StudentController::edit/$1');
    // $routes->put('update/(:num)', 'StudentController::update/$1');
    // $routes->delete('delete/(:num)', 'StudentController::delete/$1');
});

//group class
$routes->group('class', function ($routes) {
    $routes->get('/', 'ClassController::index');
    $routes->get('show', 'ClassController::show');
    $routes->post('create', 'ClassController::create');
    $routes->put('update/(:num)', 'ClassController::update/$1');
    $routes->delete('delete/(:num)', 'ClassController::delete/$1');
});

// group major
$routes->group('major', function ($routes) {
    $routes->get('/', 'MajorController::index');
    $routes->get('show', 'MajorController::show');
    $routes->post('create', 'MajorController::create');
    $routes->put('update/(:num)', 'MajorController::update/$1');
    $routes->delete('delete/(:num)', 'MajorController::delete/$1');
});

// group time setting
$routes->group('timesetting', function ($routes) {
    $routes->get('/', 'TimeSettingController::index');
    $routes->get('show', 'TimeSettingController::show');
    $routes->post('create', 'TimeSettingController::create');
    $routes->put('update/(:num)', 'TimeSettingController::update/$1');
    $routes->delete('delete/(:num)', 'TimeSettingController::delete/$1');
});

// group attendance status
$routes->group('attendance-status', function ($routes) {
    $routes->get('/', 'AttendanceStatusController::index');
    // $routes->get('show', 'AttendanceStatusController::show');
    // $routes->post('create', 'AttendanceStatusController::create');
    // $routes->put('update/(:num)', 'AttendanceStatusController::update/$1');
    // $routes->delete('delete/(:num)', 'AttendanceStatusController::delete/$1');
});
