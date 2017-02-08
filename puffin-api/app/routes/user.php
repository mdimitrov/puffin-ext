<?php

use \Puffin\Session;

// PATH /user
$app->path('user', function($request) use ($app) {
    // PATH /user/profile
    $app->path('profile', function($request) use ($app) {
        // GET /user/profile
        $app->get(function($request) use ($app) {
            echo session_id();

            return $app->template('user-profile', ['username' => $_SESSION['username']]);
        });
    });
});