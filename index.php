<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require './vendor/autoload.php';

$app = AppFactory::create();


$app->addErrorMiddleware(true, true, true);
$app->addRoutingMiddleware();

$app->setBasePath("/auth");

$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");


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
    $time = filemtime("./js/authentication.js");
    $response->getBody()->write($time . "");
    return $response->withHeader('Content-Type', 'text/plain');
});

$app->get("/js/minified", function (Request $request, Response $response, $args) {
    $response->getBody()->write(file_get_contents("./js/authentication.min.js"));
    return $response->withHeader('Content-Type', 'application/javascript');
});

$app->post("/profile", function (Request $request, Response $response, $args) {
    require_once "inc/Authentication.inc.php";
    $auth = new Authentication();
    $params = (array)$request->getParsedBody();
    if(!isset($params["username"]) || !isset($params["password"])) {
        $response->getBody()->write(json_encode(["success" => false, "message" => "Missing username or password."]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
    $username = @$params['username'];
    $password = @$params['password'];
    try {
        $result = $auth->getUserInfo($username, $password);
        if ($result) {
            $response->getBody()->write(json_encode(["success" => true, "message" => "Logged in.", "user" => $result]));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(["success" => false, "message" => "Invalid username or password."]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(["success" => false, "message" => "Failed to make request.", "error" => $e->getMessage()]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
});

$app->post('/', function (Request $request, Response $response, $args) {
    require_once "inc/Authentication.inc.php";
    $auth = new Authentication();
    $params = (array)$request->getParsedBody();

    $token = "";

    if (isset($params['username'], $params['password'])) {
        $token = $auth->login($params['username'], $params['password']);
    } else if (isset($params['token'])) {
        $token = $auth->loginWithToken($params['token']);
    }

    if (!empty($token)) {
        setcookie("token", $token, time() + (86400 * 30), "/");
        $response->getBody()->write(json_encode(["success" => true, "message" => isset($params['username']) ? "Logged in." : "Logged in with token.", "token" => $token]));
    } else {
        $message = isset($params['username']) ? "Invalid username or password." : "Invalid token.";
        $response->getBody()->write(json_encode(["success" => false, "message" => $message]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

$app->run();
