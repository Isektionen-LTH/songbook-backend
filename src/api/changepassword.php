<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'api/authenticate.php';

$_POST = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  die('Only POST requests are supported');
}

if (!isset($_POST['new_password'] || !is_string($_POST['new_password']) {
  http_response_code(400);
  die('Field \'new_password\' must be set');
}

$password_hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

$config_contents = file_get_contents( $_SERVER['DOCUMENT_ROOT'] . 'api/config.php');
$updated_contents = str_replace(ADMIN_PASSWORD, $password_hash, $config_contents);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . 'api/config.php', $updated_contents);

http_response_code(204);
