<?php
// Routes
use Puffin\Helper\Session;
use Puffin\Model\User;
use Puffin\Model\Mapper\UserMapper;
use  Puffin\Model\Mapper\PasswordRecoveryCodeMapper;

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
    } elseif ($request->isGet()) {
        // if the request is get redirect the user to login page
        // important! this is only for the html returning routes
        $response = $response->withRedirect('/login', 302);
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

$app->get('/login', function ($request, $response) {
    if ($username = $this->session->get('user.username', false)) {
        return $response->withRedirect('/users/' . $username, 302);
    }

    return $this->renderer->render($response, 'login.phtml');
});

$app->post('/login', function ($request, $response, $args) {

    /** @var \Slim\Http\Request $request */
    $username = $request->getParam('username');
    $password = $request->getParam('password');

    if (!isset($username) || !isset($password)) {
        $data = [
            'ok' => false,
            'message' => 'Invalid username or password'
        ];
        $status = 401;
    } else {
        $um = new UserMapper($this->db);
        $user = $um->findByUsername($username);

        if (isset($user) && $user instanceof User && md5($password) === $user->password) {

            $this->session->updateWithUserData($user->toAssoc(false));

            $data = [
                'ok' => true,
                'user' => $user->toAssoc(false)
            ];
            $status = 200;
        } else {
            $data = [
                'ok' => false,
                'message' => 'Invalid username or password'
            ];
            $status = 401;
        }
    }

    /** @var $response \Slim\Http\Response */
    return $response->withJson($data, $status);
});

$app->get('/logout', function ($request, $response, $args) {
    Session::destroy();
    /** @var $response \Slim\Http\Response */
    return $response->withRedirect('/login', 302);
});

$app->get('/admin/projects', function ($request, $response, $args) {
    return $this->renderer->render($response, 'admin-projects.phtml');
})->add($ensureAdmin)->add($ensureSession);

$app->get('/admin/users', function ($request, $response, $args) {
    return $this->renderer->render($response, 'admin-users.phtml');
})->add($ensureAdmin)->add($ensureSession);

$app->get('/users/{username}', function ($request, $response, $args) {
    /** @var User $loggedUser */
    $loggedUser = $request->getAttribute('loggedUser');

    if ($loggedUser->username !== $args['username'] && !$loggedUser->isAdmin()) {
//        return $response->withRedirect('/users/' . $loggedUser->username, 302);
        //TODO: redirect to appropriate html page
        return $response->withJson([
            'ok' => false,
            'message' => 'Forbidden'
        ], 403);
    }

    $um = new UserMapper($this->db);

    if ($loggedUser->username === $args['username']) {
        $user = $loggedUser;
    } else {
        $user = $um->findByUsername($args['username']);
    }

    if (!isset($user)) {
        return $response->withJson([
            'ok' => false,
            'message' => 'not found'
        ], 404);
    }

    // Render index view
    return $this->renderer->render($response, 'user-profile.phtml', [
        'user' => $user->toAssoc(false),
        'loggedUser' => $loggedUser->toAssoc(false)
    ]);
})->add($ensureSession);

$app->get('/user/send-password-reset', function ($request, $response, $args) {
    return $this->renderer->render($response, 'send-password-reset.phtml');
})->add($ensureSession);

$app->get('/user/reset-password', function ($request, $response, $args) {
    $userId = $request->getParam('user');
    $code = $request->getParam('code');

    $prcm = new PasswordRecoveryCodeMapper($this->db);
    $savedCode = $prcm->findById($userId)->code;

    if (!isset($userId) || !isset($code) || $code !== $savedCode) {
        return $response->withJson([
            'ok' => false,
            'message' => 'Invalid params'
        ], 400);
    }

    return $this->renderer->render($response, 'reset-password.phtml', [
        'userId' => $userId,
        'code' => $code
    ]);
});

$app->get('/user/reset-email-sent', function ($request, $response, $args) {
    return $this->renderer->render($response, 'reset-email-sent.phtml');
})->add($ensureSession);
