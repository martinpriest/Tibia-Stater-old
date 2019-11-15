<?php
session_start();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if (isset($_SESSION['active'])) {
    http_response_code(200);
    exit(json_encode(array("message" => "Jestes zalogowany")));
} else {
    http_response_code(400);
    exit(json_encode(array("message" => "Nie jestes zalogowany")));
}