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
    $loggedUser = $request->getAttribute('loggedUser');
    $userId = $loggedUser->id;

    if ($loggedUser->username !== $args['username'] && $loggedUser->role !== 'admin') {
        return $response->withJson([
            'ok' => false,
            'message' => 'Action is not permitted'
        ], 403);
    }

    $username = $request->getParam('username');
    $email = $request->getParam('email');

    $um = new UserMapper($this->db);
    $um->updateInfo($userId, $username, $email);
    $this->session->set('username', $username);

    $data = [
        'ok' => true,
        'username' => $username,
        'email' => $email
    ];
    $status = 200;
    /** @var $response \Slim\Http\Response */
    return $response->withJson($data, $status);
})->add($ensureSession)->add($recognize);
$app->put('/api/user/{username}/password', function ($request, $response, $args) {
    $loggedUser = $request->getAttribute('loggedUser');
    $userId = $loggedUser->id;

    if ($loggedUser->username !== $args['username'] && $loggedUser->role !== 'admin') {
        return $response->withJson([ 'ok' => false ], 403);
    }

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
        $status = 400;
    }
    /** @var $response \Slim\Http\Response */
    return $response->withJson($data, $status);
})->add($ensureSession)->add($recognize);

$app->put('/api/admin/role', function ($request, $response, $args) {
    $loggedUser = $request->getAttribute('loggedUser');
    $username = $request->getParam('username');
    $role = $request->getParam('role');

    if ($loggedUser->role === 'admin') {
        $um = new UserMapper($this->db);
        $user = $um->findByUsername($username);
        $um->updateRole($user->id, $role);

        $data = [
            'ok' => true,
            'message' => 'Success'
        ];
        $status = 200;
    } else {
        $data = [
            'ok' => false,
            'message' => 'Unauthorized'
        ];
        $status = 403;
    }
    /** @var $response \Slim\Http\Response */
    return $response->withJson($data, $status);
})->add($ensureSession)->add($recognize);
