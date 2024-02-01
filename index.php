<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

// Allow Cross Origin Requests (CORS)
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

$app->add(function (Request $request, RequestHandler $handler): Response {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
});

$app->get('/', function (Request $request, Response $response, $args) {
    $queryParams = $request->getQueryParams();
    if (isset($queryParams["time"])) {
        $time = filemtime("js/authentication.js");
        $response->getBody()->write($time);
        return $response->withHeader('Content-Type', 'text/plain');
    } else {
        $jsFile = isset($queryParams["minified"]) ? "js/authentication.min.js" : "js/authentication.js";
        $response->getBody()->write(file_get_contents($jsFile));
        return $response->withHeader('Content-Type', 'application/javascript');
    }
});

$app->get("/js/time", function (Request $request, Response $response, $args) {
    $time = filemtime("js/authentication.js");
    $response->getBody()->write($time);
    return $response->withHeader('Content-Type', 'text/plain');
});

$app->get("/js/minified", function (Request $request, Response $response, $args) {
    $response->getBody()->write(file_get_contents("js/authentication.min.js"));
    return $response->withHeader('Content-Type', 'application/javascript');
});

$app->post('/', function (Request $request, Response $response, $args) {
    require_once "inc/Authentication.inc.php";
    $auth = new Authentication();
    $params = (array)$request->getParsedBody();

    if (isset($params['username'], $params['password'])) {
        $token = $auth->login($params['username'], $params['password']);
    } elseif (isset($params['token'])) {
        $token = $auth->loginWithToken($params['token']);
    }

    if(!empty($token)) {
        setcookie("token", $token, time() + (86400 * 30), "/");
        $response->getBody()->write(json_encode(["success" => true, "message" => isset($params['username']) ? "Logged in." : "Logged in with token.", "token" => $token]));
    } else {
        $message = isset($params['username']) ? "Invalid username or password." : "Invalid token.";
        $response->getBody()->write(json_encode(["success" => false, "message" => $message]));
        return $response->withStatus(400);
    }

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
