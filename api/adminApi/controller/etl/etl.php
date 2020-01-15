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

include_once '../../model/world.php';

require_once '../../component/extract.php';
include_once '../../model/extractHistory.php';

require_once '../../component/transform.php';
include_once '../../model/transformHistory.php'; // dodanie rekordu po kazdej ekstrakcji

require_once '../../component/load.php';
include_once '../../model/loadHistory.php'; // dodanie rekordu po kazdej ekstrakcji

if($_SESSION['active']) {
    $stoper_start = microtime(true);
    //OBIEKT POLACZENIA
    $database = new Database();
    $db = $database->getConnection();

    $databaseW = new DatabaseW();
    $dbW = $databaseW->getConnection();
    
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
    $extractTime = round(($stoper_stop - $stoper_start), 3);

    // dodaj do historii
    $extractHistory = new ExtractHistory($db);
    $extractHistory->setIdUser($_SESSION['idUser'])
                ->setIdWorld($data->idWorld)
                ->setFileDownloaded($extract->getFileCreatedCounter())
                ->setExecutionTime($extractTime)
                ->setOnlinePlayers($data->onlineListTopic)
                ->setHighscores($data->highscoreTopic)
                ->setGuilds(0);
    $extractHistory->create();

    // TRANSFORM

    $stoper_start = microtime(true);

    //OBIEKT POLACZENIA
    $transform = new Transform();
    $transform->transformAllFiles($worldName);

    $stoper_stop = microtime(true);
    $transformTime = round(($stoper_stop - $stoper_start), 3);

    $transformHistory = new TransformHistory($db);
    $transformHistory->setIdUser($_SESSION['idUser'])
                    ->setIdWorld(intval($data->idWorld))
                    ->setFileParsed($transform->getFilesReadCounter())
                    ->setExecutionTime($transformTime);
    $transformHistory->create();

    $world->setLastOperation(2);
    $world->update();

    // LOAD

    $load = new Load($dbW);
    $load->loadAll($worldName);

    $stoper_stop = microtime(true);
    $loadTime = round(($stoper_stop - $stoper_start), 3);

    // dodanie rekordu do logu
    $loadHistory  = new LoadHistory($db);
    $loadHistory->setIdUser($_SESSION['idUser'])
                ->setRecordsInserted($load->getRecordInserted())
                ->setRecordsUpdated($load->getRecordUpdated())
                ->setExecutionTime($loadTime)
                ->create();

    $world->setLastOperation(0);
    $world->update();

    exit(json_encode(array("fileCreated" => $extract->getFileCreatedCounter(),
                            "extractTime" => $extractTime,

                            "transformTime" => $transformTime,

                            "recordInserted" => $load->getRecordInserted(),
                            "recordUpdated" => $load->getRecordUpdated(),
                            "loadTime" => $loadTime)));

} else {
    http_response_code(400);
    exit(json_encode(array("message" => "Nie masz uprawnien")));
}