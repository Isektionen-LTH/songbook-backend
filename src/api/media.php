<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/database.php';

define('FIELD_DESCRIPTION_MAX_LENGTH', 255);
define('FIELD_AUDIOFILE_MAX_SIZE', 8 * 1024 * 1024); // 8 MB
define('MEDIA_FOLDER', $_SERVER['DOCUMENT_ROOT'] . '/api/media/');
define('HASH_ALGO', 'sha256');

// Solves generic redirect parameters
if (isset($_GET['uuid'])) {
  $_GET['hash'] = $_GET['uuid'];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  http_response_code(404);
  die('File not found');
}
else if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
  require_once $_SERVER['DOCUMENT_ROOT'] . '/api/authenticate.php';


  if (!isset($_POST['description']) || strlen($_POST['description']) > FIELD_DESCRIPTION_MAX_LENGTH) {
    http_response_code(400);
    die('Media field \'description\' must be set and can only be ' . FIELD_NAME_MAX_LENGTH . ' characters long.');
  }

  if (!isset($_FILES['audiofile']) || $_FILES['audiofile']['size'] > FIELD_AUDIOFILE_MAX_SIZE) {
    http_response_code(400);
    die('Media file  must be set with a max size of ' . FIELD_AUDIOFILE_MAX_SIZE . ' bytes.');
  }

  if ($_FILES['audiofile']['error']) {
    http_response_code(500);
    die('Error when uploading file');
  }

  $tmpname = $_FILES['audiofile']['tmp_name'];

  $hash = hash_file(HASH_ALGO, $tmpname);

  $db = Database::getConnection();
  $stmt = $db->prepare('SELECT * FROM `media` WHERE `hash` = :hash;');
  $stmt->execute(['hash' => $hash]);

  if ($stmt->rowCount() !== 0) {
    http_response_code(409);
    die('File already exists');
  }

  move_uploaded_file($tmpname, MEDIA_FOLDER . $hash);

  $media = [
    'hash' => $hash,
    'mime' => mime_content_type(MEDIA_FOLDER . $hash),
    'description' => $_POST['description']
  ];

  $stmt = $db->prepare('INSERT INTO `media` (`hash`, `mime`, `description`) VALUES (:hash, :mime, :description);');
  $stmt->execute($media);

  http_response_code(201);
  header('Content-Type: application/json');
  echo json_encode($media);
  die();
}
else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  require_once $_SERVER['DOCUMENT_ROOT'] . '/api/authenticate.php';

  if (!isset($_GET['hash'])) {
    http_response_code(400);
    die('Hash must be specified');
  }

  $hash = $_GET['hash'];

  $db = Database::getConnection();
  $stmt = $db->prepare('DELETE FROM `media` WHERE `hash` = :hash;');
  $stmt->execute(['hash' => $hash]);

  if ($stmt->rowCount() === 0) {
    http_response_code(404);
    die('Media not found');
  }

  unlink(MEDIA_FOLDER . $hash);

  http_response_code(204);
  die();
}
else {
  http_response_code(405);
  die('Request method not supported');
}
