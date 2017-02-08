<?php

use Puffin\Model\User;

// PATH /api
$app->path('api', function($request) use ($app) {

    // PATH /api/login
    $app->path('login', function($request) use ($app) {

        // POST /api/login
        $app->post(function(Bullet\Request $request) use ($app) {
            $userId = $request->username;
            $pass = $request->password;

            if ($userId !== 'atanas' || $pass !== '123abv') {
                return $app->response(401, [
                    'ok' => false,
                    'message' => 'Invalid username or password'
                ]);
            } else {

                return [
                    'ok' => true,
                    'user' => [ 'username' => $userId ]
                ];
            }

        });
    });
});

// PATH /login
$app->path('login', function($request) use ($app) {
    // GET /login
    $app->get(function($request) use ($app) {
        return $app->template('login', [ 'userId' => 'atanas']);
    });
});
