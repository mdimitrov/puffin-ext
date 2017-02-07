<?php
require __DIR__ . '/vendor/autoload.php';

// Your App
$app = new Bullet\App();

/**
 * /api
 */
$app->path('api', function($request) use ($app) {
    /**
     * /api/login
     */
    $app->path('login', function($request) use ($app) {

         // POST /api/login
        $app->post(function($request) use ($app) {
            $userId = $request->username;
            $pass = $request->password;

            if ($userId !== 'atanas' || $pass !== '123abv') {
                return $app->response(401, [
                    'message' => 'Invalid username or password'
                ]);
            } else {
                return [
                    'status' => 'ok',
                    'user' => [ 'username' => $userId ],
                ];
            }

        });
    });
});

// Run the app! (takes $method, $url or Bullet\Request object)
echo $app->run(new Bullet\Request());

