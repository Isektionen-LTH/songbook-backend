<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/config.php';

// Authenticate
if ($_SERVER['REQUEST_METHOD'] !== 'GET'){
  if (!isset($_SERVER['PHP_AUTH_USER']) ||
      !isset($_SERVER['PHP_AUTH_PW']) ||
      $_SERVER['PHP_AUTH_USER'] !== ADMIN_USER ||
      !password_verify($_SERVER['PHP_AUTH_PW'], ADMIN_PASSWORD)) {
    http_response_code(401);
    die();
  }
}
