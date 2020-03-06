<?php
declare(strict_types=1);
// constant
define("API_PATH", $_SERVER['DOCUMENT_ROOT'] . '/api2');
define("DATA_PATH", API_PATH . '/data');

function getWorldPath(string $worldName) : string {
    $worldPath = DATA_PATH . "/{$worldName}";
    return $worldPath;
}

class AppConfig {
    public static $API_PATH;
    public static $DATA_PATH;

    // DB indexed arrays
    private $worldArr;
    private $worldLocationArr;
    private $playerArr;
    private $residenceArr;
    private $vocationArr;
    private $highscoreTypeArr;

    private $property;
    private static $instance;

    private function __construct() {
        self::$API_PATH = $_SERVER['DOCUMENT_ROOT'] . '/api2';
        self::$DATA_PATH = self::$API_PATH . '/data';
    }

    public static function getInstance() : self {
        if(empty(self::$instance)) {
            self::$instance = new AppConfig();
        }
        return self::$instance;
    }

    public function setProperty($key, $value)
    {
        $this->property[$key] = $value;
    }

    public function getProperty($key)
    {
        return $this->property[$key];
    }

    public function getWorldPath(string $worldName) {
        return self::$DATA_PATH . '/' . $worldName;
    }
}

AppConfig::getInstance();