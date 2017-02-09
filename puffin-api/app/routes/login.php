<?php

use Puffin\Model\DataMapper\UserMapper;
use Puffin\Model\User;
use Puffin\Session;

// PATH /api
$app->path('api', function($request) use ($app) {

    // PATH /api/login
    $app->path('login', function($request) use ($app) {

        // POST /api/login
        $app->post(function(Bullet\Request $request) use ($app) {
            $username = $request->username;
            $pass = $request->password;

            if ($username !== 'atanas' || $pass !== '123abv') {
                return $app->response(401, [
                    'ok' => false,
                    'message' => 'Invalid username or password'
                ]);
            } else {
                /** @var Session $session */
                $session = $app['session'];
                $session->username = $username;

                return [
                    'ok' => true
                ];
            }

        });
    });
});

// PATH /login
$app->path('login', function($request) use ($app) {
    // GET /login
    $app->get(function($request) use ($app) {
        return $app->template('login');
    });
});
