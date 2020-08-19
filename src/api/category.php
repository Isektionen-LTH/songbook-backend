<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/database.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/vendor/autoload.php';

use Ramsey\Uuid\Uuid;

define('FIELD_NAME_MAX_LENGTH', 250);


function createSlug($name) {
  $slug =  str_replace(['å', 'ä'], 'a', mb_strtolower($name));
  $slug =  str_replace('ö', 'o', $slug);
  $slug =  str_replace(' ', '_', $slug);
  return $slug;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (!isset($_GET['uuid'])) {
    http_response_code(400);
    die('Uuid must be specified');
  }

  $db = Database::getConnection();
  $stmt = $db->prepare('SELECT `uuid`, `name`, `slug` FROM `categories` WHERE `uuid` = :uuid;');
  $stmt->execute(['uuid' => $_GET['uuid']]);

  if ($stmt->rowCount() === 0) {
    http_response_code(404);
    die('Category not found');
  }

  header('Content-Type: application/json');
  echo json_encode($stmt->fetch());
}
else if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
  require_once $_SERVER['DOCUMENT_ROOT'] . '/api/authenticate.php';

  $_POST = json_decode(file_get_contents('php://input'), true);

  if (!isset($_GET['uuid']) || $_GET['uuid'] === 'new') {
    if (!isset($_POST['name']) || strlen($_POST['name']) > FIELD_NAME_MAX_LENGTH) {
      http_response_code(400);
      die('Category \'name\' must be set and can only be ' . FIELD_NAME_MAX_LENGTH . ' characters long.');
    }

    $category = [];
    $category['uuid'] = Uuid::uuid4()->toString();
    $category['name'] = $_POST['name'];
    $category['slug'] = createSlug($category['name']);

    $db = Database::getConnection();
    $stmt = $db->prepare('INSERT INTO `categories` (`uuid`, `name`, `slug`) VALUES (:uuid, :name, :slug);');
    try {
      $stmt->execute($category);
    } catch (PDOException $e) {
      if ($e->errorInfo[1] === 1062) {
        http_response_code(409);
        die('The generated slug \'' . $category['slug'] . '\' already exists, use another name');
      }
      else {
        http_response_code(500);
        die();
      }
    }

    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode($category);
    die();
  }
  else {
    $db = Database::getConnection();
    $stmt = $db->prepare('SELECT `uuid`, `name`, `slug` FROM `categories` WHERE `uuid` = :uuid;');
    $stmt->execute(['uuid' => $_GET['uuid']]);

    if ($stmt->rowCount() !== 1) {
      http_response_code(404);
      die('Category not found');
    } 

    if (!isset($_POST['name']) || strlen($_POST['name']) > FIELD_NAME_MAX_LENGHT) {
      http_response_code(400);
      die('Category \'name\' must be set and can only be ' . FIELD_NAME_MAX_LENGTH . ' characters long.');
    }

    $category = $stmt->fetch();
    $category['name'] = $_POST['name'];
    $category['slug'] = createSlug($category['name']);

    $db = Database::getConnection();
    $stmt = $db->prepare('UPDATE `categories` SET `name` = :name, `slug` = :slug) WHERE `uuid` = :uuid;');
    try {
      $stmt->execute($category);
    } catch (PDOException $e) {
      if ($e->errorInfo[1] === 1062) {
        http_response_code(409);
        die('The generated slug \'' . $slug . '\' already exists, use another name');
      }
      else {
        http_response_code(500);
        die();
      }
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($category);
  }
}
else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  require_once $_SERVER['DOCUMENT_ROOT'] . '/api/authenticate.php';

  if (!isset($_GET['uuid'])) {
    http_response_code(400);
    die('Uuid must be specified');
  }

  $db = Database::getConnection();
  $stmt = $db->prepare('DELETE FROM `categories` WHERE `uuid` = :uuid;');
  $stmt->execute(['uuid' => $_GET['uuid']]);

  if ($stmt->rowCount() === 0) {
    http_response_code(404);
    die('Category not found');
  }

  http_response_code(204);
  die();
}
else {
  http_response_code(400);
  die('Request method not supported');
}
