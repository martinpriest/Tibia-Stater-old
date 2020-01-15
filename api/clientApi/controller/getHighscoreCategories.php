<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// INCLUDUJ POTRZEBNE PLIKI
include_once '../../adminApi/config/db-warehouse.php';
include_once '../model/highscore_category.php';

$database = new DatabaseW();
$db = $database->getConnection();

$highscoreCategory = new HighscoreCategory($db);
$stmt = $highscoreCategory->readAll();

$num = $stmt->rowCount();

if($num > 0) {
    $highscoreCategory_arr = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $arr_item = array();
        $arr_item["id"] = $id;
        $arr_item["name"] = $name;

        array_push($highscoreCategory_arr, $arr_item);
    }
    http_response_code(200);
    echo json_encode($highscoreCategory_arr);
} else {
    echo "Nie wykonano jeszcze procesu ekstrakcji";
}