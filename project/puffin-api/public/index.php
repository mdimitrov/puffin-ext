<?php
require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Sofia');
error_reporting(-1); // Display ALL errors
ini_set('display_errors', '1');
ini_set("session.use_only_cookies", '1'); // Don't allow session_id in URLs
ini_set('always_populate_raw_post_data', '-1');

// Throw Exceptions for everything so we can see the errors
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");

// get configs
$settings = require __DIR__ . '/../config/local.php';

// Instantiate session
$session = $settings['settings']['session'];
session_name($session['name']);
session_set_cookie_params(
    $session['lifetime'],
    $session['path'],
    $session['domain'],
    $session['secure'],
    $session['httponly']
);
session_start();

// Instantiate the app
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register routes
require __DIR__.'/../src/routes/routes.php';

// Register API routes
require __DIR__.'/../src/routes/api-routes.php';

// Run app
$app->run();
