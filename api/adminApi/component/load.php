<?php

class Load {
    private $conn;
    private $connDB_W;
    // private $root;

    private $fileDeleted;
    private $fileReaded;

    private $recordInserted;
    private $recordUpdated;

    private $executionTime;

    public function __construct($db) {
        if(get_class($db) == "PDO") $this->conn = $db;
        else exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__ . ", ")));
        // $root = $_SERVER['DOCUMENT_ROOT'];
        $fileDeleted = 0;
        $fileReaded = 0;
        $recordInserted = 0;
        $recordUpdated = 0;
        $executionTime = 0;
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

    private function loadPlayers($world) {
        $root = $_SERVER['DOCUMENT_ROOT'];
        // 1. Wczytaj plik z graczami
        $playerListFile = file_get_contents("{$root}/api/adminApi/data/{$world}/characters.json");
        $playersArr = json_decode($playerListFile, true);
        // Pobierz ostatni rekord z tabeli time i sprawdz czy jest dniem dzisiejszym. Jeśli nie to dodaj rekord i pobierz lastInsertedId()

        // 2. Pobierz aktualną listę: światów, miast, graczy aby porównać czy istnieje

        // $worldArr = new World();
        // $worldArr->read();

        // $residenceArr = new Residence();
        // $residenceArr->read();

        // $vocationArr = new Vocation();
        // $vocationArr->read();
        // testowo zanim nie zrobilem klas odwzorowania z bazy danych
        $residenceArr = array(
            1 => "Thais",
            2 => "Carlin",
            3 => "Venore"
        );
        // Iterujemy po wszystkich graczach z pliku JSON
        foreach($playersArr as $player) {
            //$playerToInsert = new Player();

            // pzypisanie id miasta z bazy danych
            if(false !== $idResidence = array_search($player["residence"], $residenceArr)) {
                // Jest juz w bazie danych czyli wstawiamy idResidence do Playera
                echo "Miasto jest w tablicy. Jego id to {$idResidence}.\n";
            } else {
                // Nie ma miasta w bazie danych. Dodajemy więc miasto do bazy danych i pobieramy jego ID. Pobrane ID miasta wstawiamy do playera
                echo "Miasta nie ma w tablicy. Jest z " . $player["residence"] . "\n";
            }
            // tak samo jak wyzej zrobic z profesja gracza
        }
        





        // var_dump($playersArr["Aass Bryggeri"]);
        // echo $playersArr["Aass Bryggeri"]["residence"];
    }

    private function loadHighscores() {

    }

    public function loadAll($world) {
        $this->loadWorlds();
        $this->loadPlayers($world);
    }
}