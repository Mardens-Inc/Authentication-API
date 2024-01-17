<?php
if ($_SERVER['REQUEST_METHOD'] === "GET") {
    die("<h1>Forbidden</h1> <p>You don't have permission to access this page.</p>");
}
if($_SERVER['REQUEST_METHOD'] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
}