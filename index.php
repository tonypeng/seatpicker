<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('GRAD_PAGE', true);

require_once __DIR__.'/core/includes.php';

$request = Request::fromRequest();
$sessionManager = new SessionManager($request, new DatabaseBackedSessionDriver());

$act = 'index.php';

if($request->has('__REQ_URL')) {
    $act = $request->getString('__REQ_URL');
}

$controllerMap = array(
    'index.php' => 'IndexController',
    '404' => 'Http404Controller',
    'dashboard' => 'DashboardController',
    'onboarding' => 'OnboardingController',
    'onboarding/submit' => 'OnboardingUpdateController',
    'login' => 'LoginController',
    'login/submit' => 'LoginSubmitController',
    'signout' => 'SignoutController',
    'ajax/post_seat' => 'PostSeatController',
    'intern/create_seats' => 'CreateSeatsController',
    'intern/create_students' => 'CreateStudentsController',
);

$controller_name = $controllerMap['404'];

if(isset($controllerMap[$act])) {
    $controller_name = $controllerMap[$act];
}

require_once __DIR__.'/controllers/'.$controller_name.'.php';

$controller = new $controller_name($request, $sessionManager);

header('Content-Type: text/html; charset=utf-8');

Response::start();

try {
    $output = $controller->render();
    Response::setView($output);
} catch (RedirectException $re) {
    ob_end_clean();
    Response::sendCookies();
    header('Location: ' . $re->getURL());
    die();
}

Response::send();