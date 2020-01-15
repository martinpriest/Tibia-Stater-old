<?php

class PlayerNameHistory {
    private $conn;
    private $table_name = "player_name_history";

    private $id;
    private $idTime;
    private $idPlayer;
    private $formerName;

    // konstruktor
    public function __construct($db) {
        if(get_class($db) == "PDO") $this->conn = $db;
    }

    // id
    public function getId(): ?int {
        return $this->id;
    }
    public function setId(int $id): self {
        if (is_numeric($id)) {
            $this->id = $id;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawidlowy format danych")));
        }
    }

    // idTime
    public function getIdTime(): ?int {
        return $this->idTime;
    }
    public function setIdTime(int $idTime): self {
        if (is_numeric($idTime)) {
            $this->idTime = $idTime;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawidTimelowy format danych")));
        }
    }

    // idPlayer
    public function getIdPlayer(): ?int {
        return $this->idPlayer;
    }
    public function setIdPlayer(int $idPlayer): self {
        if (is_numeric($idPlayer)) {
            $this->idPlayer = $idPlayer;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawidPlayerlowy format danych")));
        }
    }

    // FormerName
    public function getFormerName(): ?string {
        return $this->formerName;
    }
    public function setFormerName(string $formerName): self {
        if (empty($formerName)) {
            http_response_code(400);
            exit(json_encode(array("message" => "Wprowadź formerName!")));
        } else if (is_numeric($formerName)) {
            http_response_code(400);
            exit(json_encode(array("message" => "FormerName nie może być liczbą!")));
        } else if (strlen($formerName) < 2 || strlen($formerName) > 32) {
            http_response_code(400);
            exit(json_encode(array("message" => "FormerName musi mieć od 4 do 32 znaków!")));
        } else {
            $this->formerName = $formerName;
            return $this;
        }
    }

    // create
    public function create() {
        $query = "INSERT INTO {$this->table_name}
                SET idTime = :idTime,
                    idPlayer = :idPlayer,
                    formerName = :formerName";

        $stmt = $this->conn->prepare($query);

        $this->idTime = htmlspecialchars(strip_tags($this->idTime));
        $this->idPlayer = htmlspecialchars(strip_tags($this->idPlayer));
        $this->formerName = htmlspecialchars(strip_tags($this->formerName));

        $stmt->bindParam(':idTime', $this->idTime);
        $stmt->bindParam(':idPlayer', $this->idPlayer);
        $stmt->bindParam(':formerName', $this->formerName);

        try {
            if($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            } else {
                http_response_code(503);
                throw new Exception ("blah");
                exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
            }
        } catch (Exception $e) {
            echo "BLAD W FORMER NAME \n \n";
        }
    }

    // readByIdPlayer
    public function readByIdPlayer() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE idPlayer = :idPlayer";

        $this->idPlayer = htmlspecialchars(strip_tags($this->idPlayer));
        $stmt->bindParam(':idPlayer', $this->idPlayer);

        if($stmt->execute()) {
            return $stmt;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

}