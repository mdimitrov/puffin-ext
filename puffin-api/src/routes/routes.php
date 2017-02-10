<?php
// Routes
use Puffin\Helper\Session;
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
        $response = $response->withRedirect('/login', 302);
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

$app->get('/login', function ($request, $response, $args) {
    // Render index view
    return $this->renderer->render($response, 'login.phtml', $args);
});
$app->post('/login', function ($request, $response, $args) {

    /** @var \Slim\Http\Request $request */
    $username = $request->getParam('username');
    $password = $request->getParam('password');

    if (!isset($username)) {
        $data = [
            'ok' => false,
            'message' => 'Invalid username or password'
        ];
        $status = 401;
    } else {
        $um = new UserMapper($this->db);
        $um->save(User::fromState([
            'username' => $username,
            'password' => $password,
            'email' => 'test@test.com',
        ]));
        $this->session->set('username', $username);

        $data = ['ok' => true, 'username' => $username];
        $status = 200;
    }

    /** @var $response \Slim\Http\Response */
    return $response->withJson($data, $status);
});

$app->get('/logout', function ($request, $response, $args) {
    Session::destroy();
    /** @var $response \Slim\Http\Response */
    return $response->withRedirect('/login', 302);
});

$app->get('/admin', function ($request, $response, $args) {
    return $this->renderer->render($response, 'admin.phtml');
})->add($ensureSession);

$app->get('/user/{username}', function ($request, $response, $args) {
    // Render index view
    /** @var User $loggedUser */
    $loggedUser = $request->getAttribute('loggedUser');

    return $this->renderer->render($response, 'user-profile.phtml', $loggedUser->toAssoc());
})->add($ensureSession)->add($recognize);

