<?php
// Api Endpoints

#### Middleware
/**
 * @param  \Slim\Http\Request $request PSR7 request
 * @param  \Slim\Http\Response $response
 * @param  callable $next Next middleware
 *
 * @return \Slim\Http\Response
 */
$ensureSession = function ($request, $response, $next) {
    $username = $this->session->get('username', null);

    if (isset($username)) {
        $response = $next($request, $response);
    } else {
        $response = $response->withJson(['ok' => false], 403);
    }

    return $response;
};

#### Route handlers

$app->get('/api/user/{username}', function ($request, $response, $args) {
    /** @var $response \Slim\Http\Response */
    return $response->withJson([], 200);
})->add($ensureSession);
$app->put('/api/user/{username}', function ($request, $response, $args) {
    /** @var $response \Slim\Http\Response */
    return $response->withJson([], 200);
})->add($ensureSession);
$app->put('/api/user/{username}/password', function ($request, $response, $args) {
    /** @var $response \Slim\Http\Response */
    return $response->withJson([], 200);
})->add($ensureSession);
