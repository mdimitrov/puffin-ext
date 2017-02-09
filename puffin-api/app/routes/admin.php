<?php

use \Puffin\Session;

// PATH /admin
$app->path('admin', function($request) use ($app) {
    $app->get(function($request) use ($app) {
        return $app->template('admin');
    });
});