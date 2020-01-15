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
include_once '../../config/db-warehouse.php';
include_once '../../model/clearHistory.php';

if($_SESSION['active']) {
    $databaseW = new DatabaseW();
    $dbW = $databaseW->getConnection();

    $del = $dbW->prepare('DELETE FROM player_world_history');
    $del->execute();
    $count = $del->rowCount();

    $del = $dbW->prepare('DELETE FROM player_sex_history');
    $del->execute();
    $count += $del->rowCount();

    $del = $dbW->prepare('DELETE FROM player_name_history');
    $del->execute();
    $count += $del->rowCount();

    $del = $dbW->prepare('DELETE FROM players_transaction');
    $del->execute();
    $count += $del->rowCount();

    $del = $dbW->prepare('DELETE FROM highscore_transaction');
    $del->execute();
    $count += $del->rowCount();

    $del = $dbW->prepare('DELETE FROM players');
    $del->execute();
    $count += $del->rowCount();

    // ustaw auto increment na 1
    $setDbTable = $dbW->prepare("ALTER TABLE `player_world_history` auto_increment = 1");
    $setDbTable->execute();
    $setDbTable = $dbW->prepare("ALTER TABLE `player_sex_history` auto_increment = 1");
    $setDbTable->execute();
    $setDbTable = $dbW->prepare("ALTER TABLE `player_name_history` auto_increment = 1");
    $setDbTable->execute();
    $setDbTable = $dbW->prepare("ALTER TABLE `players_transaction` auto_increment = 1");
    $setDbTable->execute();
    $setDbTable = $dbW->prepare("ALTER TABLE `highscore_transaction` auto_increment = 1");
    $setDbTable->execute();
    $setDbTable = $dbW->prepare("ALTER TABLE `players` auto_increment = 1");
    $setDbTable->execute();

    // dodaj do logu ze user usunal

    $database = new Database();
    $db = $database->getConnection();

    $transformHistory = new ClearHistory($db);
    $transformHistory->setIdUser($_SESSION['idUser'])
                    ->setRecordDeleted($count);
    $transformHistory->create();

    http_response_code(200);
    exit(json_encode(array("deletedRecords" => $count)));
} else {
    http_response_code(400);
    exit(json_encode(array("message" => "Nie masz uprawnien")));
}