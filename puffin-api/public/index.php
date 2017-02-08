<?php
##
# Setup defaults
##
error_reporting(-1); // Display ALL errors
ini_set('display_errors', '1');
ini_set("session.cookie_httponly", '1'); // Mitigate XSS javascript cookie attacks for browers that support it
ini_set("session.use_only_cookies", '1'); // Don't allow session_id in URLs

// Throw Exceptions for everything so we can see the errors
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");
// Start user session (we start it in the session instance)
//session_start();

##
# Setup bullet tiny micro framework
##
define('BULLET_ROOT', dirname(__DIR__));
define('BULLET_APP_ROOT', BULLET_ROOT . '/app');
define('BULLET_SRC_ROOT', BULLET_APP_ROOT . '/src');
define('BULLET_ROUTES_ROOT', BULLET_APP_ROOT . '/routes');
$loader = require BULLET_ROOT . '/vendor/autoload.php';

$config = require BULLET_APP_ROOT . '/config/local.php';
$app = new Bullet\App([
    'template.cfg' => ['path' => BULLET_ROOT . '/public/assets/html/']
]);
$request = new Bullet\Request();

##
# Load configs into the app
##
// $app is a dependency injection container
$app['config'] = $config;
$app['session'] = function($app) {
    // initialize session and start it
    return new Puffin\Session($app['config']['session']);
};

// will get a new instance on every call
$app['user_mapper'] = function() {
    return new \Puffin\Model\DataMapper\UserMapper();
};

##
# Load route handlers
##
require BULLET_ROUTES_ROOT . '/login.php';

echo $app->run($request);


