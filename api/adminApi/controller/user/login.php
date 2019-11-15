<?php

session_start();
//USTAW NAGLOWKI
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// INCLUDUJ POTRZEBNE PLIKI
include_once '../../config/db-meta.php';
include_once '../../model/user.php';
include_once '../../model/userHistory.php';
//OBIEKT POLACZENIA
$database = new Database();
$db = $database->getConnection();
// POBIERZ DANE, UTWORZU OBIEKT USER, PRZYPISZ MU WARTOSCI
$data = json_decode(file_get_contents("php://input"));

$user = new User($db);
$user->setLogin($data->login);
$user->readByLogin();

if(password_verify($data->password, $user->getPassword()) == false) {
    http_response_code(400);
    exit(json_encode(array("message" => "Wpisales zle haslo")));
} else {
    $user->login();
    $_SESSION['idUser'] = $user->getId();
    $_SESSION['active'] = true;

    $userHistory = new UserHistory($db);
    $userHistory->setIdUser($user->getId())->setStatus(1);
    $userHistory->create();
    http_response_code(200);
    exit(json_encode(array("message" => "Zalogowales sie")));
}