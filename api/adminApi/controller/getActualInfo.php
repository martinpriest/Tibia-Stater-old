<?php
session_start();
//USTAW NAGLOWKI
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

set_time_limit(1800);
// INCLUDUJ POTRZEBNE PLIKI
include_once '../config/db-meta.php';
include_once '../model/world.php'; // aktualizacja swiatow przy kazdej ekstrakcji

if($_SESSION['active']) {
    //OBIEKT POLACZENIA
    $database = new Database();
    $db = $database->getConnection();

    // POBIERZ DANE, UTWORZU OBIEKT USER, PRZYPISZ MU WARTOSCI
    $data = json_decode(file_get_contents("php://input"));

    // pobierz dane swiata
    $world = new World($db);
    $world->setId(intval($data->idWorld));
    $world->readById();

    http_response_code(200);
    exit(json_encode(array("fileCreated" => $extract->getFileCreatedCounter(), 
                            "executionTime" => $executionTime)));
} else {
    http_response_code(400);
    exit(json_encode(array("message" => "Nie masz uprawnien")));
}