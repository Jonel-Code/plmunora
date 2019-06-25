<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
require_once __DIR__ . '/AppInstance.php';
// use AppInstance;
$app = AppInstance\AppInstance::get_app_instance();

// Set up dependencies
$dependencies = require __DIR__ . '/../src/dependencies.php';
$dependencies($app);

// Register middleware
$middleware = require __DIR__ . '/../src/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../src/routes.php';
$routes($app);

$temp_folder =  __DIR__ . '\\temp';

$print_hello_world = require __DIR__ . '/../src/controllers/print-hello-world.php';
$print_hello_world($app,$temp_folder);

$test_call = require __DIR__ . '/../src/controllers/test-call.php';
$test_call($app);


// public apis

$student_register = require __DIR__ . '/../src/controllers/student-register.php';
$student_register($app);

$student_login = require __DIR__ . '/../src/controllers/student-login.php';
$student_login($app);

$admin_login = require __DIR__ . '/../src/controllers/admin-login.php';
$admin_login($app);

$docs_listing = require __DIR__ . '/../src/controllers/document-listing.php';
$docs_listing($app);

$submit_req_form = require __DIR__ . '/../src/controllers/submit-req-form.php';
$submit_req_form($app, $temp_folder);

// get-req-pdf.php
$get_req_pdf = require __DIR__ . '/../src/controllers/get-req-pdf.php';
$get_req_pdf($app, $temp_folder);

// $mysql_connection = require __DIR__ . '/../src/models/mysql-connection.php';
// $mysql_connection();


// Run app
$app->run();
