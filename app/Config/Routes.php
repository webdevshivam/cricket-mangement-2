<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'LoginController::index');
$routes->post('/login', 'LoginController::login');
$routes->get('/logout', 'LoginController::logout');

$routes->get('/admin/dashboard', 'AdminController::dashboard', ['filter' => 'role:admin']);

// Player routes

$routes->group('admin/players', ['filter' => 'role:admin'], function ($routes) {
  $routes->post('update-payment-status', 'AdminPlayerController::updatePaymentStatus');
  $routes->get('/', 'AdminPlayerController::index');
  $routes->get('add', 'AdminPlayerController::create');
  $routes->post('save', 'AdminPlayerController::save');
  $routes->post('delete-multiple', 'AdminPlayerController::deleteMultiple');
  $routes->get('edit/(:num)', 'AdminPlayerController::edit/$1');
  $routes->post('update/(:num)', 'AdminPlayerController::update/$1');
  $routes->get('delete/(:num)', 'AdminPlayerController::delete/$1');
  $routes->get('view/(:num)', 'AdminPlayerController::view/$1');
  $routes->post('assign-grade', 'GradeController::assignGrade');
});

$routes->group('/admin/manage-trial-cities', ['filter' => 'role:admin'], function ($routes) {

  $routes->get('/', 'TrialCityController::index');
  $routes->get('add', 'TrialCityController::create');
  $routes->post('save', 'TrialCityController::save');
  $routes->get('edit/(:num)', 'TrialCityController::edit/$1');
  $routes->post('update/(:num)', 'TrialCityController::update/$1');
  $routes->get('delete/(:num)', 'TrialCityController::delete/$1');
});

$routes->group('admin/qr-code-setting', ['filter' => 'role:admin'], function ($routes) {
  $routes->get('/', 'QrCodeSettingController::index');
  $routes->post('update-setting', 'QrCodeSettingController::save');
});

$routes->get('send-mail', 'MailTest::send');


$routes->group('/admin/grades', ['filter' => 'role:admin'], function ($routes) {
  $routes->get('/', 'GradeController::index');
  $routes->get('add', 'GradeController::create');
  $routes->post('save', 'GradeController::save');
  $routes->get('edit/(:num)', 'GradeController::edit/$1');
  $routes->post('update/(:num)', 'GradeController::update/$1');
  $routes->get('delete/(:num)', 'GradeController::delete/$1');
  $routes->get('assign/(:num)', 'GradeController::assign/$1');
  $routes->get('assign', 'GradeController::assignSave');
});

//trial registration routes
$routes->group('admin/trial-registration', ['filter' => 'role:admin'], function ($routes) {
  $routes->get('/', 'TrialRegistrationController::adminIndex');
});


$routes->get('/manager/dashboard', 'ManagerController::dashboard', ['filter' => 'role:manager']);
$routes->get('/coach/dashboard', 'CoachController::dashboard', ['filter' => 'role:coach']);
$routes->get('/player/dashboard', 'PlayerController::dashboard', ['filter' => 'role:player']);

$routes->get('/unauthorized', function () {
  return view('unauthorized');
});


// frontend routes
$routes->get('/trial-registration', 'TrialRegistrationController::index');
$routes->post('/trial-registration-save', 'TrialRegistrationController::register');
