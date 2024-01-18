<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    header("Content-Type: text/javascript");
    http_response_code(200);
    if (isset($_GET["minified"])) {
        die(file_get_contents("js/authentication.min.js"));
    }
    die(file_get_contents("js/authentication.js"));
} else if ($_SERVER['REQUEST_METHOD'] === "POST") {
    header("Content-Type: application/json");
    require_once "inc/Authentication.inc.php";
    $auth = new Authentication();

    $headers = apache_request_headers();


    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $token = $auth->login($username, $password);
        if ($token) {
            setcookie("token", $token, time() + (86400 * 30), "/");
            http_response_code(200);
            die(json_encode(["success" => true, "message" => "Logged in.", "token" => $token]));
        } else {
            http_response_code(400);
            die(json_encode(["success" => false, "message" => "Invalid username or password."]));
        }
    } else if (isset($_COOKIE['token']) || isset($headers["Authorization"])) {
        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : $headers["Authorization"];
        if ($auth->loginWithToken($token)) {
            http_response_code(200);
            die(json_encode(["success" => true, "message" => "Logged in with token.", "token" => $token]));
        } else {
            http_response_code(400);
            die(json_encode(["success" => false, "message" => "Invalid token."]));
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    die();
} else {
    header("Content-Type: application/json");
    http_response_code(400);
    die(json_encode(["success" => false, "message" => "Invalid request."]));
}
