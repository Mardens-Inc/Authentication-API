<?php

$json = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/db-config.json"), true);

// Database configuration
$DB_HOST     = $json["host"];
$DB_USER     = $json["user"];
$DB_PASSWORD = $json["password"];
$DB_NAME     = $json["database"];