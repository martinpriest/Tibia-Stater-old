<?php

session_start();
// INCLUDUJ POTRZEBNE PLIKI
include_once '../../config/db-meta.php';
include_once '../../model/user.php';
include_once '../../model/userHistory.php';
//OBIEKT POLACZENIA
$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$user->setId($_SESSION['idUser']);
$user->logout();

$uh = new UserHistory($db);
$uh->setIdUser($_SESSION['idUser'])->setStatus(0);
$uh->create();

$_SESSION = array();
http_response_code(201);
exit(json_encode(array("message" => "Wylogowales sie")));