<?php
// Api Endpoints

use Puffin\Model\User;
use Puffin\Model\Mapper\UserMapper;

use Puffin\Model\Project;
use Puffin\Model\Mapper\ProjectMapper;

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

########
#### Users route handlers
########
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
        'data' =>  $user->toAssoc()
    ], 200);
})->add($ensureAdmin)->add($ensureSession);

$app->put('/api/users/{userId}', function ($request, $response, $args) {
    /** @var User $loggedUser */
    $userId = $args['userId'];
    $loggedUser = $request->getAttribute('loggedUser');

    if ($loggedUser->id !== $userId && !$loggedUser->isAdmin()) {
        return $response->withJson([
            'ok' => false,
            'message' => 'Access Denied'
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
    $updateAction = $request->getParam('action');
    $updateData = $request->getParam('data');

    if ($updateAction === 'updateRole' && !$loggedUser->isAdmin()) {
        return $response->withJson([
            'ok' => false,
            'message' => 'Access Denied'
        ], 403);
    }

    if (isset($updateData) && method_exists($um, $updateAction)) {
        $um->{$updateAction}($userId, $updateData);
        $user->setFromAssoc($updateData);
        if ($loggedUser->id === $userId) {
            $this->session->updateWithUserData($user->toAssoc());
        }
    }

    /** @var $response \Slim\Http\Response */
    return $response->withJson([ 'ok' => true, 'data' => $user->toAssoc()], 200);
})->add($ensureSession);

$app->delete('/api/users/{userId}', function ($request, $response, $args) {
    /** @var User $loggedUser */
    $userId = $args['userId'];
    $loggedUser = $request->getAttribute('loggedUser');

    if ($loggedUser->id !== $userId && !$loggedUser->isAdmin()) {
        return $response->withJson([
            'ok' => false,
            'message' => 'Access Denied'
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

    $um->delete($user->id);

    /** @var $response \Slim\Http\Response */
    return $response->withJson([ 'ok' => true], 204);
})->add($ensureAdmin)->add($ensureSession);

$app->get('/api/users', function ($request, $response, $args) {
    $originalLimit = $request->getParam('limit', 20);
    $limit = $originalLimit + 1;
    $skip = $request->getParam('skip', 0);
    $um = new UserMapper($this->db);
    $users = $um->findAllAssoc(false, $limit, $skip);

    /** @var $response \Slim\Http\Response */
    return $response->withJson([
        'ok' => true,
        'data' =>  array_slice($users, 0, $originalLimit),
        'pagination' => [
            'hasMore' => count($users) > $originalLimit
        ]
    ], 200);
})->add($ensureAdmin)->add($ensureSession);

########
#### Projects route handlers
########

$app->get('/api/projects', function ($request, $response, $args) {
    $originalLimit = $request->getParam('limit', 20);
    $limit = $originalLimit + 1;
    $skip = $request->getParam('skip', 0);

    $pm = new ProjectMapper($this->db);
    $projects = $pm->findAllAssoc($limit, $skip);

    /** @var $response \Slim\Http\Response */
    return $response->withJson([
        'ok' => true,
        'data' =>  array_slice($projects, 0, $originalLimit),
        'pagination' => [
            'hasMore' => count($projects) > $originalLimit
        ]
    ], 200);
})->add($ensureAdmin)->add($ensureSession);

$app->put('/api/projects/{projectId}', function ($request, $response, $args) {
    /** @var User $loggedUser */
    $projectId = $args['projectId'];
    $pm = new ProjectMapper($this->db);
    $project = $pm->findById($projectId);

    if (!isset($project) || !$project || !($project instanceof Project)) {
        return $response->withJson([
            'ok' => false,
            'message' => 'not found'
        ], 404);
    }

    /** @var \Slim\Http\Request $request */
    $updateAction = $request->getParam('action');
    $updateData = $request->getParam('data');

    if (isset($updateData) && method_exists($pm, $updateAction)) {
        $pm->{$updateAction}($projectId, $updateData);
        $project->setFromAssoc($updateData);
    }

    /** @var $response \Slim\Http\Response */
    return $response->withJson([ 'ok' => true, 'data' => $project->toAssoc()], 200);
})->add($ensureAdmin)->add($ensureSession);