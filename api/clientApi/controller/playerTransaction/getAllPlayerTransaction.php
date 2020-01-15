<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// hurtownia danych
include_once '../../../adminApi/config/db-warehouse.php';
// fakt
include_once '../../model/player_transaction.php';
// wymiary
include_once '../../model/time.php';
include_once '../../model/residence.php';
include_once '../../model/world.php';
include_once '../../model/player.php';
// slowniki wymiarow
include_once '../../model/vocation.php';

$database = new DatabaseW();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));
$world = new WorldW($db);
$world->setName($data->idWorld);
$world->readByName();

$playerTransaction = new PlayerTransaction($db);
$playerTransaction->setIdWorld($world->getId());

$stmt = $playerTransaction->readAllPlayersByWorld();
$num = $stmt->rowCount();

$player_transaction_arr = array();
if($num > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $time = new Time($db);
        $time->setId($idTime);
        $time->readById();
        $year = $time->getYear(); // DANA DO WYPCHENICIA
        $month = $time->getMonth(); // DANA DO WYPCHENICIA
        $dayOfMonth = $time->getDayOfMonth(); // DANA DO WYPCHENICIA

        $player = new Player($db);
        $player->setId($idPlayer);
        $player->readById();
        $playerName = $player->getName(); // DANA DO WYPCHENICIA
        $playerStatus = $player->getStatus(); // DANA DO WYPCHENICIA

        $vocation = new Vocation($db);
        $vocation->setId($player->getIdVocation());
        $vocation->readById();
        $vocationName = $vocation->getName(); // DANA DO WYPCHENICIA

        $residence = new Residence($db);
        $residence->setId($idResidence);
        $residence->readById();
        $residenceName = $residence->getName(); // DANA DO WYPCHENICIA

        $playerLevel = $level; // DANA DO WYPCHENICIA



        $player_item = array(
            "idPlayer" => $idPlayer,
            "name" => $playerName,
            "vocation" => $vocationName,
            "level" => $playerLevel,
            "status" => $playerStatus,
            "residence" => $residenceName,
            "year" => $year,
            "month" => $month,
            "dayOfMonth" => $dayOfMonth
        );
        array_push($player_transaction_arr, $player_item);
    }
    http_response_code(200);
    echo json_encode($player_transaction_arr);
} else {
    http_response_code(200);
    echo json_encode(array("message" => "No records"));
}