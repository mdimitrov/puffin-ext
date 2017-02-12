<?php
// Api Endpoints

use Puffin\Model\User;
use Puffin\Model\Mapper\UserMapper;

use Puffin\Model\Project;
use Puffin\Model\Mapper\ProjectMapper;

use Puffin\Model\Mapper\PasswordRecoveryCodeMapper;
use \Puffin\Model\PasswordRecoveryCode;

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

/** search in users */
$app->get('/api/search/users', function ($request, $response, $args) {
    $query = $request->getParam('q');
    $um = new UserMapper($this->db);
    $users = $um->findAllSearchAssoc(false, $query);

    /** @var $response \Slim\Http\Response */
    return $response->withJson([
        'ok' => true,
        'data' => $users
    ], 200);
})->add($ensureAdmin)->add($ensureSession);

/**
 * get user by id
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

/**
 * update user data
 */
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

/**
 * delete user by id
 */
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

/**
 * get all users
 */
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

/**
 * get all projects
 */
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

/**
 * update project
 */
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

/**
 * search in projects
 */
$app->get('/api/search/projects', function ($request, $response, $args) {
    $query = $request->getParam('q');
    $pm = new ProjectMapper($this->db);
    $projects = $pm->findAllSearchAssoc($query);

    /** @var $response \Slim\Http\Response */
    return $response->withJson([
        'ok' => true,
        'data' => $projects
    ], 200);
})->add($ensureAdmin)->add($ensureSession);

#######
### Forgotten password
#######

$app->post('/api/users/password-reset', function ($request, $response, $args) {
    $email = $request->getParam('email');

    if (!$email) {
        return $response->withJson([
            'ok' => false,
            'message' => 'Invalid Params'
        ], 400);
    }

    $um = new UserMapper($this->db);
    $user = $um->findByEmail($email);

    if (!isset($user) || !$user || !($user instanceof User)) {
        return $response->withJson([
            'ok' => false,
            'message' => 'Invalid Params'
        ], 400);
    }

    $prcm = new PasswordRecoveryCodeMapper($this->db);
    $code = $prcm->save(new PasswordRecoveryCode($user->id))->code;

    $protocol = array_key_exists('HTTPS', $_SERVER) ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $recoveryUrl =  $protocol . $host . '/user/reset-password?' . http_build_query([
        'code' => $code,
        'user' => $user->id
    ]);

    /** @var PHPMailer $mail */
    $mail = $this->mailer;
    $emailConfig = $this->settings['mailer']['emails']['password_recovery'];

    $mail->setFrom($emailConfig['from']['address'], $emailConfig['from']['name']);
    $mail->addAddress($email, $user->fullName);
    $mail->addReplyTo($emailConfig['reply_to']['address'], $emailConfig['reply_to']['name']);
    $mail->isHTML(true);

    $mail->Subject = $emailConfig['subject'];
    $mail->Body    = $emailConfig['body']($recoveryUrl);

    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }

    /** @var $response \Slim\Http\Response */
    return $response->withJson([
        'ok' => true
    ], 200);
});

$app->post('/api/users/{userId}/_reset_password', function ($request, $response, $args) {
    $userId = $args['userId'];
    $code = $request->getParam('code');
    $newPassword = $request->getParam('newPassword');

    if (!isset($userId) || !isset($code) || !isset($newPassword)) {
        return $response->withJson([
            'ok' => false,
            'message' => 'Invalid params'
        ], 400);
    }

    $um = new UserMapper($this->db);
    $prcm = new PasswordRecoveryCodeMapper($this->db);
    $savedCode = $prcm->findById($userId)->code;

    if (!$savedCode || $savedCode !== $code) {
        return $response->withJson([
            'ok' => false,
            'message' => 'Invalid params'
        ], 400);
    }

    $um->updatePassword($userId, ['newPassword' => $newPassword]);

    return $response->withJson([
        'ok' => true
    ], 200);
});