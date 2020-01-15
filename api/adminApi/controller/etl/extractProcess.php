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
include_once '../../model/world.php'; // aktualizacja swiatow przy kazdej ekstrakcji
include_once '../../model/extractHistory.php'; // dodanie rekordu po kazdej ekstrakcji
require_once '../../component/extract.php';

if($_SESSION['active']) {
    $stoper_start = microtime(true);
    //OBIEKT POLACZENIA
    $database = new Database();
    $db = $database->getConnection();
    
    // POBIERZ DANE, UTWORZU OBIEKT USER, PRZYPISZ MU WARTOSCI
    // wejsce to: id serwera, czy sciagac online, czy sciagac highscore, czy sciagac gildie
    $data = json_decode(file_get_contents("php://input"));

    // pobierz nazwe serwera o wejsciowym id
    $world = new World($db);
    $world->setId(intval($data->idWorld));
    $world->readById();
    $worldName = $world->getName();

    // ekstrakcja
    $extract = new Extract();
    $extract->getServers(); // aby utworzyl znow foldery
    if($data->onlineListTopic == 1) {
        $extract->extractOnlineList($worldName);
        $world->setOnlinePlayersReady(1);
    }
    if($data->highscoreTopic == 1) {
        $extract->extractHighscores($worldName);
        $world->setHighscoresReady(1);
    }
    $world->setLastOperation(1);
    //zapis co zostalo ekstraktowane
    $world->update();

    // Koniec mierzenia czasu
    $stoper_stop = microtime(true);
    $executionTime = round(($stoper_stop - $stoper_start), 3);

    // dodaj do historii
    $extractHistory = new ExtractHistory($db);
    $extractHistory->setIdUser($_SESSION['idUser'])
                ->setIdWorld($data->idWorld)
                ->setFileDownloaded($extract->getFileCreatedCounter())
                ->setExecutionTime($executionTime)
                ->setOnlinePlayers($data->onlineListTopic)
                ->setHighscores($data->highscoreTopic)
                ->setGuilds(0);
    $extractHistory->create();

    //response
    http_response_code(200);
    exit(json_encode(array("fileCreated" => $extract->getFileCreatedCounter(), 
                            "executionTime" => $executionTime)));
} else {
    http_response_code(400);
    exit(json_encode(array("message" => "Nie masz uprawnien")));
}