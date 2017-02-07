<?php
require __DIR__ . '/vendor/autoload.php';

// Your App
$app = new Bullet\App([
    'template.cfg' => ['path' => __DIR__ . '/assets/html/']
]);

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
                    'ok' => false,
                    'message' => 'Invalid username or password'
                ]);
            } else {
                return [
                    'ok' => true,
                    'user' => [ 'username' => $userId ],
                ];
            }

        });
    });
});


/**
 * /login
 */
$app->path('login', function($request) use ($app) {
    $app->get(function($request) use ($app) {
        return $app->template('login', [ 'userId' => 'atanas']);
    });
});

// Run the app! (takes $method, $url or Bullet\Request object)
echo $app->run(new Bullet\Request());

