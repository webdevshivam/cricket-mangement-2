<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Trial Registration Routes
$routes->get('/trial-registration', 'TrialRegistrationController::index');
$routes->post('/trial-registration-save', 'TrialRegistrationController::register');
$routes->get('/trial-otp-verification', 'TrialRegistrationController::otpVerification');
$routes->post('/trial-verify-otp', 'TrialRegistrationController::verifyOTP');
$routes->post('/trial-resend-otp', 'TrialRegistrationController::resendOTP');

// League Registration Routes
$routes->get('/league-registration', 'LeagueRegistrationController::index');
$routes->post('/league-registration-save', 'LeagueRegistrationController::register');
$routes->get('/league-otp-verification', 'LeagueRegistrationController::otpVerification');
$routes->post('/league-verify-otp', 'LeagueRegistrationController::verifyOTP');
$routes->post('/league-resend-otp', 'LeagueRegistrationController::resendOTP');
// Frontend grade check routes
$routes->get('grades/check', 'GradeController::checkGrade');
$routes->post('grades/check-mobile', 'GradeController::getGradeByMobile');
$routes->get('/login', 'LoginController::index');
$routes->post('/login', 'LoginController::login');
$routes->get('/logout', 'LoginController::logout');

$routes->get('/admin/dashboard', 'Home::dashboard', ['filter' => 'role:admin']);
$routes->get('/admin/dashboard/stats', 'Home::dashboardStats', ['filter' => 'role:admin']);

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
  $routes->post('weather-analysis', 'TrialCityController::getWeatherAnalysis');
  $routes->get('edit/(:num)', 'TrialCityController::edit/$1');
  $routes->post('update/(:num)', 'TrialCityController::update/$1');
  $routes->get('delete/(:num)', 'TrialCityController::delete/$1');
});

$routes->group('admin/qr-code-setting', ['filter' => 'role:admin'], function ($routes) {
  $routes->get('/', 'QrCodeSettingController::index');
  $routes->post('update-setting', 'QrCodeSettingController::save');
});

$routes->get('send-mail', 'MailTest::send');


$routes->group('admin/grades', ['filter' => 'role:admin'], function ($routes) {
  $routes->get('/', 'GradeController::index');
  $routes->get('add', 'GradeController::create');
  $routes->post('save', 'GradeController::save');
  $routes->get('edit/(:num)', 'GradeController::edit/$1');
  $routes->post('update/(:num)', 'GradeController::update/$1');
  $routes->get('delete/(:num)', 'GradeController::delete/$1');
  $routes->get('assign', 'GradeController::assign');
  $routes->post('assignGrade', 'GradeController::assignGrade');
  $routes->get('assignments', 'GradeController::viewAssignments');
  $routes->post('updateAssignment/(:num)', 'GradeController::updateAssignment/$1');
  $routes->post('deleteAssignment/(:num)', 'GradeController::deleteAssignment/$1');
});

//trial registration routes
$routes->get('/trial-registration', 'TrialRegistrationController::index');
$routes->post('/trial-registration-save', 'TrialRegistrationController::register');

// League Registration Routes
$routes->get('league-registration', 'LeagueRegistrationController::index');
$routes->post('league-registration-save', 'LeagueRegistrationController::register');
$routes->get('league-status', 'LeagueRegistrationController::checkStatus');
$routes->post('league-status-check', 'LeagueRegistrationController::getStatus');

// Trial Status Check Routes
$routes->get('trial-status', 'TrialStatusController::checkStatus');
$routes->post('trial-status-check', 'TrialStatusController::getStatus');

$routes->group('admin', ['filter' => 'role:admin'], static function ($routes) {
    $routes->get('dashboard', 'AdminController::index');
    $routes->get('trial-registration', 'TrialRegistrationController::adminIndex');

    // OTP Settings
    $routes->get('otp-settings', 'OtpSettingController::index');
    $routes->post('otp-settings/update', 'OtpSettingController::update');

    // API Settings Routes
    $routes->get('api-settings', 'ApiSettingController::index');
    $routes->post('api-settings/update', 'ApiSettingController::update');
    $routes->post('api-settings/change-password', 'ApiSettingController::changePassword');

    // Team Management Routes
    $routes->get('teams', 'TeamController::index');
    $routes->get('teams/manage/(:num)', 'TeamController::manageTeam/$1');
    $routes->post('teams/update/(:num)', 'TeamController::updateTeam/$1');
    $routes->post('teams/add-player', 'TeamController::addPlayer');
    $routes->post('teams/remove-player', 'TeamController::removePlayer');
    $routes->post('teams/set-captain', 'TeamController::setCaptain');

    // Tournament Management Routes
    $routes->get('tournaments', 'TournamentController::index');
    $routes->get('tournaments/create', 'TournamentController::create');
    $routes->post('tournaments/store', 'TournamentController::store');
    $routes->get('tournaments/manage/(:num)', 'TournamentController::manage/$1');
    $routes->get('tournaments/bracket/(:num)', 'TournamentController::bracket/$1');
    $routes->post('tournaments/update-match', 'TournamentController::updateMatch');
    $routes->post('tournaments/create-match', 'TournamentController::createMatch');
    $routes->post('tournaments/delete-match', 'TournamentController::deleteMatch');
    $routes->post('tournaments/delete/(:num)', 'TournamentController::delete/$1');
  // Trial Registration Routes
  $routes->post('trial-registration/update-payment-status', 'TrialRegistrationController::updatePaymentStatus');
  $routes->post('trial-registration/bulk-update-payment-status', 'TrialRegistrationController::bulkUpdatePaymentStatus');
  $routes->post('trial-registration/collect-payment', 'TrialRegistrationController::collectPayment');
  $routes->get('trial-registration/verification', 'TrialRegistrationController::verification');
  $routes->get('trial-registration/payment-tracking', 'TrialRegistrationController::paymentTracking');
  $routes->post('trial-registration/search-by-mobile', 'TrialRegistrationController::searchByMobile');
  $routes->post('trial-registration/bulk-delete', 'TrialRegistrationController::bulkDelete');
  $routes->post('trial-registration/delete', 'TrialRegistrationController::deleteStudent');
  $routes->get('trial-registration/export-pdf', 'TrialRegistrationController::exportPDF');

  // Trial Verification Routes (separate URL pattern)
  $routes->get('trial-verification', 'TrialRegistrationController::verification');
  $routes->get('trial-verification/export-pdf', 'TrialRegistrationController::exportPDF');
  $routes->post('trial-verification/search-by-mobile', 'TrialRegistrationController::searchByMobile');
  $routes->post('trial-verification/collect-spot-payment', 'TrialRegistrationController::collectSpotPayment');
  $routes->post('trial-verification/mark-trial-completed', 'TrialRegistrationController::markTrialCompleted');

  // League Registration Admin Routes
  $routes->get('league-registration', 'LeagueRegistrationController::adminIndex');
  $routes->post('league-registration/update-payment-status', 'LeagueRegistrationController::updatePaymentStatus');
  $routes->post('league-registration/update-grade', 'LeagueRegistrationController::updateGrade');
  $routes->get('league-registration/view-document/(:num)/(:segment)', 'LeagueRegistrationController::viewDocument/$1/$2');
  $routes->post('league-registration/delete', 'LeagueRegistrationController::deletePlayer');
  $routes->get('league-registration/export-pdf', 'LeagueRegistrationController::exportPDF');
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

// League Registration Routes
$routes->get('/league-registration', 'LeagueRegistrationController::index');
$routes->post('/league-registration-save', 'LeagueRegistrationController::register');
$routes->get('league-status', 'LeagueRegistrationController::checkStatus');
$routes->post('league-status-check', 'LeagueRegistrationController::getStatus');

// Trial Status Check Routes
$routes->get('trial-status', 'TrialStatusController::checkStatus');
$routes->post('trial-status-check', 'TrialStatusController::getStatus');

$routes->get('admin/dashboard', 'Home::dashboard', ['filter' => 'roleFilter:admin,manager,coach']);
$routes->get('admin/trial-players', 'Home::trialPlayers', ['filter' => 'roleFilter:admin,manager,coach']);
$routes->get('admin/trial-registration', 'TrialRegistrationController::adminIndex', ['filter' => 'roleFilter:admin,manager']);
$routes->get('admin/trial-registration/verification', 'TrialRegistrationController::verification', ['filter' => 'roleFilter:admin,manager,coach']);
$routes->get('admin/trial-registration/payment-tracking', 'TrialRegistrationController::paymentTracking', ['filter' => 'roleFilter:admin,manager']);
$routes->post('admin/trial-registration/update-payment', 'TrialRegistrationController::updatePaymentStatus', ['filter' => 'roleFilter:admin,manager,coach']);
$routes->post('admin/trial-registration/bulk-update-payment', 'TrialRegistrationController::bulkUpdatePaymentStatus', ['filter' => 'roleFilter:admin,manager']);
$routes->post('admin/trial-registration/collect-payment', 'TrialRegistrationController::collectPayment', ['filter' => 'roleFilter:admin,manager,coach']);
$routes->post('admin/trial-registration/collect-spot-payment', 'TrialRegistrationController::collectSpotPayment', ['filter' => 'roleFilter:admin,manager,coach']);
$routes->post('admin/trial-registration/mark-trial-completed', 'TrialRegistrationController::markTrialCompleted', ['filter' => 'roleFilter:admin,manager,coach']);
$routes->post('admin/trial-registration/search-mobile', 'TrialRegistrationController::searchByMobile', ['filter' => 'roleFilter:admin,manager,coach']);
$routes->post('admin/trial-registration/bulk-delete', 'TrialRegistrationController::bulkDelete', ['filter' => 'roleFilter:admin']);
$routes->post('admin/trial-registration/delete', 'TrialRegistrationController::deleteStudent', ['filter' => 'roleFilter:admin']);