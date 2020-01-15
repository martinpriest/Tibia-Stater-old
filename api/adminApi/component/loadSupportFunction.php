<?php

$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/api/adminApi/config/db-warehouse.php');
require_once($root . '/api/clientApi/model/player.php');
require_once($root . '/api/clientApi/model/player_name_history.php');
require_once($root . '/api/clientApi/model/player_sex_history.php');
require_once($root . '/api/clientApi/model/player_world_history.php');
require_once($root . '/api/clientApi/model/highscore_category.php');
require_once($root . '/api/clientApi/model/world_location.php');
require_once($root . '/api/clientApi/model/time.php');


// wygeneruj sygnature czasowa
// dorobic sprawdzanie czy w danym dniu juz taka nie powstala
function addNewTime() {
    $database = new DatabaseW();
    $db = $database->getConnection();
    
    $time = new Time($db);
    $time->setYear(date('Y'))
        ->setMonth(date('m'))
        ->setDayOfMonth(date('d'))
        ->setDayOfWeek(date('N'));
    
    if(!$time->timeExist()) $time->create();
    
    return $time->getId();
}

// UPDATE WORLD

function addPlayerWorldHistory($idTime, $idPlayer, $idWorld) {
    $database = new DatabaseW();
    $db = $database->getConnection();
    
    $playerWorldHistory = new PlayerWorldHistory($db);
    $playerWorldHistory->setIdTime($idTime)
                        ->setIdPlayer($idPlayer)
                        ->setIdFormerWorld($idWorld)
                        ->create();
}

function updatePlayerWorld($idPlayer, $idWorld) {
    $database = new DatabaseW();
    $db = $database->getConnection();
    
    $player = new Player($db);
    $player->setId($idPlayer)
        ->setIdWorld($idWorld)
        ->updateIdWorld();
}

// UPDATE SEX

function addPlayerSexHistory($idTime, $idPlayer, $sex) {
    $database = new DatabaseW();
    $db = $database->getConnection();
    
    $playerSexHistory = new PlayerSexHistory($db);
    $playerSexHistory->setIdTime($idTime)
                        ->setIdPlayer($idPlayer)
                        ->setSex($sex)
                        ->create();
}

function updatePlayerSex($idPlayer, $sex) {
    $database = new DatabaseW();
    $db = $database->getConnection();
    
    $player = new Player($db);
    $player->setId($idPlayer)
        ->setSex($sex)
        ->updateSex();
}

// UPDATE NAME

function addPlayerNameHistory($idTime, $idPlayer, $formerName) {
    $database = new DatabaseW();
    $db = $database->getConnection();
    $playerNameHistory = new PlayerNameHistory($db);
    $playerNameHistory->setIdTime($idTime)
                        ->setIdPlayer($idPlayer)
                        ->setFormerName($formerName)
                        ->create();
}

function updatePlayerName($idPlayer, $name) {
    $database = new DatabaseW();
    $db = $database->getConnection();
    
    $player = new Player($db);
    $player->setId($idPlayer)
        ->setName($name)
        ->updateName();
}

// INSERT PLAYER


// INSERT ALL FORMER NAMES


// INSERT PLAYER TRANSACTION