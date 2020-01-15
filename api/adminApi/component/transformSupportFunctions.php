<?php
// baza warehouse do slownikow
require_once($root . '/api/adminApi/config/db-warehouse.php');
// slowniki
require_once($root . '/api/clientApi/model/highscore_category.php');
require_once($root . '/api/clientApi/model/world.php');
require_once($root . '/api/clientApi/model/residence.php');
require_once($root . '/api/clientApi/model/vocation.php');


function getWorldsArray() {
    $database = new DatabaseW();
    $db = $database->getConnection();
    
    $world = new WorldW($db);
    $stmt = $world->readAll();
    
    $num = $stmt->rowCount();
    
    if($num > 0) {
        $world_arr = array();
        $i=1;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $world_arr[$id] = $name;
            $i++;
        }
        return $world_arr;
    } else {
        return [];
    }
}
// DO SLOWNIKA REZYDENCJI
function getResidencesArray() {
    $database = new DatabaseW();
    $db = $database->getConnection();
    
    $residence = new Residence($db);
    $stmt = $residence->readAll();
    
    $num = $stmt->rowCount();
    
    if($num > 0) {
        $residence_arr = array();
        $i=1;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $residence_arr[$id] = $name;
            $i++;
        }
        return $residence_arr;
    } else {
        return [];
    }
}

function createResidence($residenceName) {
    $database = new DatabaseW();
    $db = $database->getConnection();
    
    $residence = new Residence($db);
    
    $residence->setName($residenceName);
    $residence->create();
    return $residence->getId();
}

// DO SLOWNIKA VOCATION
function getVocationArray() {
    $database = new DatabaseW();
    $db = $database->getConnection();
    
    $vocation = new Vocation($db);
    $stmt = $vocation->readAll();
    
    $num = $stmt->rowCount();
    
    if($num > 0) {
        $vocation_arr = array();
        $i=1;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $vocation_arr[$id] = $name;
            $i++;
        }
        return $vocation_arr;
    } else {
        return [];
    }
}

function createVocation($vocationName) {
    $database = new DatabaseW();
    $db = $database->getConnection();
    
    $vocation = new Vocation($db);
    
    $vocation->setName($vocationName);
    $vocation->create();
    return $vocation->getId();
}