<?php
register_shutdown_function('errorHandler');

function errorHandler() { 
    $error = error_get_last();
    $type = $error['type'];
    $message = $error['message'];
    if ($type == 64 && !empty($message)) {
        echo "
            <strong>
                <font color=\"red\">
                Fatal error captured:
                </font>
            </strong>
        ";
        echo "<pre>";
        print_r($error);
        echo "</pre>";
    }
}
session_start();
//USTAW NAGLOWKI
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

set_time_limit(1800);
// INCLUDUJ POTRZEBNE PLIKI
include_once '../../config/db-meta.php';
include_once '../../config/db-warehouse.php';
include_once '../../model/transformHistory.php'; // dodanie rekordu po kazdej ekstrakcji
require_once '../../model/world.php';
require_once '../../component/load.php';

if($_SESSION['active']) {
    $data = json_decode(file_get_contents("php://input"));

    $stoper_start = microtime(true);

    //OBIEKT POLACZENIA
    $databaseW = new DatabaseW();
    $dbW = $databaseW->getConnection();

    $databaseM = new Database();
    $dbM = $databaseM->getConnection();


    
    $world = new World($dbM);
    $world->setId(intval($data->idWorld));
    $world->readById();

    $worldName = $world->getName();
    $load = new Load($dbW);
    $load->loadAll($worldName);

    $stoper_stop = microtime(true);
    $executionTime = round(($stoper_stop - $stoper_start), 3);

    http_response_code(200);
    exit(json_encode(array("executionTime" => $executionTime)));
} else {
    http_response_code(400);
    exit(json_encode(array("message" => "Nie masz uprawnien")));
}