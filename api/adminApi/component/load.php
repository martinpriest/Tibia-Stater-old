<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/api/adminApi/config/db-warehouse.php');
require_once($root . '/api/adminApi/component/loadSupportFunction.php');
require_once($root . '/api/clientApi/model/player.php');
require_once($root . '/api/clientApi/model/player_transaction.php');
require_once($root . '/api/clientApi/model/highscore_transaction.php');


class Load {
    private $conn;
    private $connDB_W;
    // private $root;

    private $recordInserted;
    private $recordUpdated;

    private $executionTime;

    public function __construct($db) {
        if(get_class($db) == "PDO") $this->conn = $db;
        else exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__ . ", ")));
        // $root = $_SERVER['DOCUMENT_ROOT'];

        $this->recordInserted = 0;
        $this->recordUpdated = 0;
        $this->executionTime = 0;
    }

    public function getRecordInserted(): ?int {
        return $this->recordInserted;
    }

    public function getRecordUpdated(): ?int {
        return $this->recordUpdated;
    }

    // jak pojawi sie nowy location to dodaj location, jak swiat istnieje to tez continue
    private function loadWorlds() {
        $root = $_SERVER['DOCUMENT_ROOT'];
        $worldListFile = file_get_contents("{$root}/api/adminApi/data/worldList.json");
        $worldArr = json_decode($worldListFile, true);
        if(empty($worldArr)) return false;
        foreach($worldArr as $world) {
            $query = "INSERT INTO worlds
                    SET name = :name,
                        idLocation = :idLocation
                    ON DUPLICATE KEY UPDATE idLocation = :idLocation";
            $stmt = $this->conn->prepare($query);
            // sanityzacja zmiennych obiektu
            $world['name']=htmlspecialchars(strip_tags($world['name']));
            $world['location']=htmlspecialchars(strip_tags($world['location']));

            if($world['location'] == "North America") $world['location'] = 1;
            else if($world['location'] == "South America") $world['location'] = 2;
            else if($world['location'] == "Europe") $world['location'] = 3;
            else {
                exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
            }

            //wstawianie zmiennych obiketu do zapytania
            $stmt->bindParam(':name', $world['name'], PDO::PARAM_STR);
            $stmt->bindParam(':idLocation', $world['location'], PDO::PARAM_INT);
            //jesli zapytanie sie wykona poprawnie zwroc true
            if($stmt->execute()) {
                continue;
            } else {
                http_response_code(503);
                exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
            }
        }
        return true;
    }

    private static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    private function loadPlayers($world) {
        $root = $_SERVER['DOCUMENT_ROOT'];
        // 1. Wczytaj plik z graczami
        $playerListFile = file_get_contents("{$root}/api/adminApi/data/{$world}/afterTransform.json");
        $playersArr = json_decode($playerListFile, true);
       
        $databaseW = new DatabaseW();
        $dbW = $databaseW->getConnection();

        $idTime = addNewTime();
        // 2. Iterujemy po wszystkich graczach z pliku JSON
        foreach($playersArr as $key => $player) {
            $tempPlayer = new Player($dbW);
            
            // PRZYPORZADKOWANIE WEJSCIOWEGO GRACZA DO PRZYPADKU WSTAWIANIA
            // 1. Nick jest w bazie w tabeli graczy (stary gracz, aktualizujemy tylko dane)
            if($tempPlayer->setName($key)->playerExists()) {
                $dbPlayerState = 1;
            } else { // 2. Nicku nie ma w tabeli graczy
                // 2.1. Nie ma former names (nowy gracz, nie dodajemy nickow)
                if(!array_key_exists("formerNames", $player)) $dbPlayerState = 2;
                // 2.2. Gracz ma former names, a jego pierwszy formerName nie istnieje w tabeli players (nowy gracz, dodajemy formery)
                else if(array_key_exists("formerNames", $player)
                        && !$tempPlayer->setName($player["formerNames"][0])->playerExists()) $dbPlayerState = 3;
                // 2.2. Gracz ma former names, a jego pierwszy formerName istnieje w tabeli players (stary gracz, zamieniamy mu nicki)
                else if(array_key_exists("formerNames", $player)
                        && $tempPlayer->setName($player["formerNames"][0])->playerExists()) $dbPlayerState = 4;
            }
            
            if($dbPlayerState == 1) {
                $tempPlayer->readById();
                if($tempPlayer->getIdWorld() != $player["idWorld"]) {
                    addPlayerWorldHistory($idTime, $tempPlayer->getId(), $tempPlayer->getIdWorld());
                    $this->recordInserted++;
                    updatePlayerWorld($tempPlayer->getId(), $player["idWorld"]);
                    $this->recordUpdated++;
                }
    
                if($tempPlayer->getSex() != $player["sex"]) {
                    addPlayerSexHistory($idTime, $tempPlayer->getId(), $tempPlayer->getSex());
                    $this->recordInserted++;
                    updatePlayerSex($tempPlayer->getId(), $player["sex"]);
                    $this->recordUpdated++;
                }
            } else if($dbPlayerState == 2) {
                $tempPlayer->setName($key)
                    ->setIdWorld($player["idWorld"])
                    ->setIdVocation($player["idVocation"])
                    ->setTitle($player["title"])
                    ->setSex($player["sex"])
                    ->setStatus($player["status"])
                    ->create();
                $this->recordInserted++;
                if(array_key_exists("idFormerWorld", $player)) {
                    addPlayerWorldHistory($idTime, $tempPlayer->getId(), $tempPlayer->getIdWorld());
                    $this->recordInserted++;
                }
            } else if($dbPlayerState == 3) {
                $tempPlayer->setName($key)
                    ->setIdWorld($player["idWorld"])
                    ->setIdVocation($player["idVocation"])
                    ->setTitle($player["title"])
                    ->setSex($player["sex"])
                    ->setStatus($player["status"])
                    ->create();
                $this->recordInserted++;
                $tempId = $tempPlayer->getId();
                foreach($player["formerNames"] as $formerName) {
                    addPlayerNameHistory($idTime, $tempId, $formerName);
                    $this->recordInserted++;
                }

                if(array_key_exists("idFormerWorld", $player)) {
                    addPlayerWorldHistory($idTime, $tempPlayer->getId(), $tempPlayer->getIdWorld());
                    $this->recordInserted++;
                }
            } else if($dbPlayerState == 4) {
                addPlayerNameHistory($idTime, $tempPlayer->getId(), $player["formerNames"][0]);
                $this->recordInserted++;
                $tempPlayer->setName($key)->updateName();
                $this->recordUpdated++;
            }

            // player transaction
            $playerTransaction = new PlayerTransaction($dbW);
            $playerTransaction->setIdTime($idTime)
                            ->setIdWorld($tempPlayer->getIdWorld())
                            ->setIdPlayer($tempPlayer->getId())
                            ->setIdResidence($player["idResidence"])
                            ->setLevel($player["level"])
                            ->setAchievmentPoint($player["achievmentPoints"])
                            ->setTimeOnline(0)
                            ->create();
            $this->recordInserted++;

            // highscore tansaction grubo pominieta zasada DRY :) (na szybko)
            $highscoreTransaction = new HighscoreTransaction($dbW);
            if(array_key_exists("achievements", $player)) {
                $highscoreTransaction->setIdHighscoreCategory(1)
                                    ->setRankPosition($player["achievements"]["rankPosition"])
                                    ->setRankValue($player["achievements"]["rankValue"])
                                    ->setIdTime($idTime)
                                    ->setIdWorld($tempPlayer->getIdWorld())
                                    ->setIdPlayer($tempPlayer->getId())
                                    ->setIdVocation($tempPlayer->getIdVocation())
                                    ->create();
                $this->recordInserted++;
            }
            if(array_key_exists("axe", $player)) {
                $highscoreTransaction->setIdHighscoreCategory(2)
                                    ->setRankPosition($player["axe"]["rankPosition"])
                                    ->setRankValue($player["axe"]["rankValue"])
                                    ->setIdTime($idTime)
                                    ->setIdWorld($tempPlayer->getIdWorld())
                                    ->setIdPlayer($tempPlayer->getId())
                                    ->setIdVocation($tempPlayer->getIdVocation())
                                    ->create();
                $this->recordInserted++;
            }
            if(array_key_exists("club", $player)) {
                $highscoreTransaction->setIdHighscoreCategory(3)
                                    ->setRankPosition($player["club"]["rankPosition"])
                                    ->setRankValue($player["club"]["rankValue"])
                                    ->setIdTime($idTime)
                                    ->setIdWorld($tempPlayer->getIdWorld())
                                    ->setIdPlayer($tempPlayer->getId())
                                    ->setIdVocation($tempPlayer->getIdVocation())
                                    ->create();
                $this->recordInserted++;
            }
            if(array_key_exists("distance", $player)) {
                $highscoreTransaction->setIdHighscoreCategory(4)
                                    ->setRankPosition($player["distance"]["rankPosition"])
                                    ->setRankValue($player["distance"]["rankValue"])
                                    ->setIdTime($idTime)
                                    ->setIdWorld($tempPlayer->getIdWorld())
                                    ->setIdPlayer($tempPlayer->getId())
                                    ->setIdVocation($tempPlayer->getIdVocation())
                                    ->create();
                $this->recordInserted++;
            }
            if(array_key_exists("experience", $player)) {
                $highscoreTransaction->setIdHighscoreCategory(5)
                                    ->setRankPosition($player["experience"]["rankPosition"])
                                    ->setRankValue($player["experience"]["rankValue"])
                                    ->setIdTime($idTime)
                                    ->setIdWorld($tempPlayer->getIdWorld())
                                    ->setIdPlayer($tempPlayer->getId())
                                    ->setIdVocation($tempPlayer->getIdVocation())
                                    ->create();
                $this->recordInserted++;
            }
            if(array_key_exists("fishing", $player)) {
                $highscoreTransaction->setIdHighscoreCategory(6)
                                    ->setRankPosition($player["fishing"]["rankPosition"])
                                    ->setRankValue($player["fishing"]["rankValue"])
                                    ->setIdTime($idTime)
                                    ->setIdWorld($tempPlayer->getIdWorld())
                                    ->setIdPlayer($tempPlayer->getId())
                                    ->setIdVocation($tempPlayer->getIdVocation())
                                    ->create();
                $this->recordInserted++;
            }
            if(array_key_exists("fist", $player)) {
                $highscoreTransaction->setIdHighscoreCategory(7)
                                    ->setRankPosition($player["fist"]["rankPosition"])
                                    ->setRankValue($player["fist"]["rankValue"])
                                    ->setIdTime($idTime)
                                    ->setIdWorld($tempPlayer->getIdWorld())
                                    ->setIdPlayer($tempPlayer->getId())
                                    ->setIdVocation($tempPlayer->getIdVocation())
                                    ->create();
                $this->recordInserted++;
            }
            if(array_key_exists("loyalty", $player)) {
                $highscoreTransaction->setIdHighscoreCategory(8)
                                    ->setRankPosition($player["loyalty"]["rankPosition"])
                                    ->setRankValue($player["loyalty"]["rankValue"])
                                    ->setIdTime($idTime)
                                    ->setIdWorld($tempPlayer->getIdWorld())
                                    ->setIdPlayer($tempPlayer->getId())
                                    ->setIdVocation($tempPlayer->getIdVocation())
                                    ->create();
                $this->recordInserted++;
            }
            if(array_key_exists("magic", $player)) {
                $highscoreTransaction->setIdHighscoreCategory(9)
                                    ->setRankPosition($player["magic"]["rankPosition"])
                                    ->setRankValue($player["magic"]["rankValue"])
                                    ->setIdTime($idTime)
                                    ->setIdWorld($tempPlayer->getIdWorld())
                                    ->setIdPlayer($tempPlayer->getId())
                                    ->setIdVocation($tempPlayer->getIdVocation())
                                    ->create();
                $this->recordInserted++;
            }
            if(array_key_exists("shielding", $player)) {
                $highscoreTransaction->setIdHighscoreCategory(10)
                                    ->setRankPosition($player["shielding"]["rankPosition"])
                                    ->setRankValue($player["shielding"]["rankValue"])
                                    ->setIdTime($idTime)
                                    ->setIdWorld($tempPlayer->getIdWorld())
                                    ->setIdPlayer($tempPlayer->getId())
                                    ->setIdVocation($tempPlayer->getIdVocation())
                                    ->create();
                $this->recordInserted++;
            }
            if(array_key_exists("sword", $player)) {
                $highscoreTransaction->setIdHighscoreCategory(11)
                                    ->setRankPosition($player["sword"]["rankPosition"])
                                    ->setRankValue($player["sword"]["rankValue"])
                                    ->setIdTime($idTime)
                                    ->setIdWorld($tempPlayer->getIdWorld())
                                    ->setIdPlayer($tempPlayer->getId())
                                    ->setIdVocation($tempPlayer->getIdVocation())
                                    ->create();
                $this->recordInserted++;
            }
        }
    }

    // private function loadHighscores() {

    // }

    public function loadAll($world) {
        $this->loadWorlds();
        $this->loadPlayers($world);

        $root = $_SERVER['DOCUMENT_ROOT'];
        $path = "{$root}/api/adminApi/data/{$world}";
        $this->deleteDir($path);
    }
}