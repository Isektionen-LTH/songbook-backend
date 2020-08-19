<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  http_response_code(400);
  die('Endpoint only accepts GET requests');
}

$db = Database::getConnection();

$stmt = $db->query('SELECT `uuid`, `name`, `melody`, `categories` FROM `songs` ORDER BY `name`;');


// Return the songs
header('Content-Type: application/json');
echo '[';
$currentRow = 1;
while ($song = $stmt->fetch()) {
  $song['categories'] = json_decode($song['categories'], true);

  echo json_encode($song);
  if ($currentRow < $stmt->rowCount()) {
    echo ',';
    $currentRow++;
  }
}
echo ']';
