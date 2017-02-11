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
    $um = new UserMapper($this->db);
    $user = $um->findById($this->session->get('user.id'));

    if (isset($user)) {
        $request = $request->withAttribute('loggedUser', $user);
        $response = $next($request, $response);
    } else {
        $response = $response->withJson([
            'ok' => false,
            'message' => 'Access Denied'
        ], 403);
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
$ensureAdmin = function ($request, $response, $next) {
    /** @var User $loggedUser */
    $loggedUser = $request->getAttribute('loggedUser');

    if (isset($loggedUser) && $loggedUser->isAdmin()) {
        $response = $next($request, $response);
    } else {
        $response = $response->withJson([
            'ok' => false,
            'message' => 'Access Denied'
        ], 403);
    }

    return $response;
};

#### Route handlers

/**
 * returns user by id
 */
$app->get('/api/users/{userId}', function ($request, $response, $args) {
    $um = new UserMapper($this->db);
    $user = $um->findById($args['userId']);

    if (!isset($user) || !$user || !($user instanceof User)) {
        return $response->withJson([
            'ok' => false,
            'message' => 'not found'
        ], 404);
    }

    /** @var $response \Slim\Http\Response */
    return $response->withJson([
        'ok' => true,
        'data' =>  $user->toAssoc(false)
    ], 200);
})->add($ensureAdmin)->add($ensureSession);


$getUpdateActionFromBody =  function ($body) {
    if (!array_key_exists('action', $body) || !isset($body['action'])) {
        return null;
    }

    switch ($body['action']) {
        case 'updatePassword':
            $action = 'updatePassword';
            break;
        case 'updateInfo':
            $action = 'updateInfo';
            break;
        case 'updateRole':
            $action = 'updateRole';
            break;
        default:
            $action = null;
    }

    return $action;
};

$app->put('/api/users/{userId}', function ($request, $response, $args) use ($getUpdateActionFromBody) {
    /** @var User $loggedUser */
    $userId = $args['userId'];
    $loggedUser = $request->getAttribute('loggedUser');

    if ($loggedUser->id !== $userId && !$loggedUser->isAdmin()) {
        return $response->withJson([
            'ok' => false,
            'message' => 'Action is not permitted'
        ], 403);
    }

    $um = new UserMapper($this->db);

    if ($loggedUser->id === $userId) {
        $user = $loggedUser;
    } else {
        $user = $um->findById($userId);
    }

    if (!isset($user) || !$user || !($user instanceof User)) {
        return $response->withJson([
            'ok' => false,
            'message' => 'not found'
        ], 404);
    }

    /** @var \Slim\Http\Request $request */
    $updateAction = $getUpdateActionFromBody($request->getParsedBody());
    $updateData = $request->getParam('data');

    if (isset($updateData) && method_exists($um, $updateAction)) {
        $result = $um->{$updateAction}($userId, $updateData);
        $user->setFromAssoc($updateData);
        if ($loggedUser->id === $userId) {
            $this->session->updateWithUserData($user->toAssoc(false));
        }
    }

    /** @var $response \Slim\Http\Response */
    return $response->withJson([ 'ok' => true, 'data' => $user->toAssoc(false)], 200);
})->add($ensureSession);

$app->put('/api/users/{userId}/_changeRole', function ($request, $response, $args) {
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
})->add($ensureAdmin)->add($ensureSession);
