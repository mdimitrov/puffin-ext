<?php
// Api Endpoints

use \Puffin\Model\User;
use Puffin\Model\Mapper\UserMapper;

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

/**
 * @param  \Slim\Http\Request $request PSR7 request
 * @param  \Slim\Http\Response $response
 * @param  callable $next Next middleware
 *
 * @return \Slim\Http\Response
 */
$recognize = function ($request, $response, $next) {
    $sessUsername = $this->session->get('username');

    $um = new UserMapper($this->db);
    $user = $um->findByUsername($sessUsername);

    if (isset($user)) {
        $request = $request->withAttribute('loggedUser', $user);
        $response = $next($request, $response);
    } else {
        $response = $response->withRedirect('/login', 302);
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
})->add($ensureSession)->add($recognize);
$app->put('/api/user/{username}/password', function ($request, $response, $args) {
    $loggedUser = $request->getAttribute('loggedUser');
    $userId = $loggedUser->id;
    $oldPassword = $request->getParam('oldPassword');
    $newPassword = $request->getParam('newPassword');

    if (md5($oldPassword) === $loggedUser->password) {
        $um = new UserMapper($this->db);
        $um->updatePassword($userId, $newPassword);

        $data = [
            'ok' => true,
            'message' => 'Success'
        ];
        $status = 200;
    } else {
        $data = [
            'ok' => false,
            'message' => 'Incorrect password'
        ];
        $status = 401;
    }
    /** @var $response \Slim\Http\Response */
    return $response->withJson($data, $status);
})->add($ensureSession)->add($recognize);
