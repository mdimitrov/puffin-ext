<?php
use Puffin\Helper\Session;
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO(
        "mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']};charset=utf8",
        $db['user'],
        $db['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['session'] = function () {
    return new Session;
};