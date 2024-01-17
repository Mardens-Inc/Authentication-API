<?php
if ($_SERVER['REQUEST_METHOD'] === "GET") {
    header("Content-Type: text/html");
    http_response_code(403);
    die("<h1>Forbidden</h1> <p>You don't have permission to access this page.</p>");
}
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === "POST") {
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
            die(json_encode(["success" => true, "message" => "Logged in with token."]));
        } else {
            http_response_code(400);
            die(json_encode(["success" => false, "message" => "Invalid token."]));
        }
    }
}

http_response_code(400);
die(json_encode(["success" => false, "message" => "Invalid request."]));
