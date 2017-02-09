<?php

use \Puffin\Session;

// PATH /user
$app->path('user', function($request) use ($app) {
    // PATH /user/profile
    $app->path('profile', function($request) use ($app) {
        // GET /user/profile
        $app->get(function($request) use ($app) {
            /** @var Session $session */
            $session = $app['session'];
            $username = $session->username;

            return $app->template('user-profile', ['username' => $username]);
        });
    });
    
    $app->path('edit', function($request) use ($app) {
        // POST /user/edit
        $app->put(function(Bullet\Request $request) use ($app) {
            $username = $request->username;
            $email = $request->email;

            return [
                'ok' => true,
                'username' => $username
            ];
        });
    });
    
    $app->path('password', function($request) use ($app) {
        // POST /user/password
        $app->put(function(Bullet\Request $request) use ($app) {
            $oldPassword = $request->oldPassword;
            $newPassword = $request->newPassword;

            return [
                'ok' => true,
                'oldPassword' => $oldPassword
            ];
        });
    });
});