<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  http_response_code(400);
  die('Endpoint only accepts GET requests');
}

$db = Database::getConnection();

$stmt = $db->query('SELECT `uuid`, `name`, `slug` FROM `categories` ORDER BY `name` COLLATE utf8mb4_swedish_ci;');


// Return the categories
header('Content-Type: application/json');
echo '[';
$currentRow = 1;
while ($category = $stmt->fetch()) {
  echo json_encode($category);
  if ($currentRow < $stmt->rowCount()) {
    echo ',';
  }
  $currentRow++;
}
echo ']';
