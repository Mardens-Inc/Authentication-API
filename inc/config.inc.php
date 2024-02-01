<?php
$path = realpath(__DIR__ . "/../db-config.json");
$json = json_decode(file_get_contents($path), true);

// Database configuration
$DB_HOST = $json["host"];
$DB_USER = $json["user"];
$DB_PASSWORD = $json["password"];
$DB_NAME = $json["database"];