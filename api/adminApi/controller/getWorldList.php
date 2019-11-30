<?php
session_start();
//USTAW NAGLOWKI
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// INCLUDUJ POTRZEBNE PLIKI
include_once '../config/db-meta.php';
include_once '../model/world.php'; // aktualizacja swiatow przy kazdej ekstrakcji
include_once '../component/extract.php'; // dodanie rekordu po kazdej ekstrakcji


if($_SESSION['active']) {
    $database = new Database();
    $db = $database->getConnection();
    // 1. Pobierz nową listę serwerów
    $extract = new Extract();
    $worldsArr = $extract->getServers();
    // 2. Wprowadź listę do bazy danych (pod klucz unique) oraz zapisz tą liste do pliku (do późniejszego load)
    $world = new World($db);
    foreach($worldsArr as $worldName) {
        $world->setName($worldName['name']);
        $world->setLocation($worldName['location']);
        $world->create();
    }
    $root = $_SERVER['DOCUMENT_ROOT'];
    file_put_contents("{$root}/api/adminApi/data/worldList.json", json_encode($worldsArr));
    // 3. Pobierz wszystkie servery z bazy danych
    $worldsArr = $world->read();
    http_response_code(200);
    exit(json_encode($worldsArr));

} else {
    http_response_code(400);
    exit(json_encode(array("message" => "Nie masz uprawnien")));
}