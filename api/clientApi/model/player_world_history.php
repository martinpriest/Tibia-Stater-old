<?php

class PlayerWorldHistory {
    private $conn;
    private $table_name = "player_world_history";

    private $id;
    private $idTime;
    private $idPlayer;
    private $idFormerWorld;

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

    // idFormerWorld
    public function getIdFormerWorld(): ?int {
        return $this->idFormerWorld;
    }
    public function setIdFormerWorld(int $idFormerWorld): self {
        if (is_numeric($idFormerWorld)) {
            $this->idFormerWorld = $idFormerWorld;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawidFormerWorldlowy format danych")));
        }
    }

    // create
    public function create() {
        $query = "INSERT INTO {$this->table_name}
                SET idTime = :idTime,
                    idPlayer = :idPlayer,
                    idFormerWorld = :idFormerWorld";

        $stmt = $this->conn->prepare($query);

        $this->idTime = htmlspecialchars(strip_tags($this->idTime));
        $this->idPlayer = htmlspecialchars(strip_tags($this->idPlayer));
        $this->idFormerWorld = htmlspecialchars(strip_tags($this->idFormerWorld));

        $stmt->bindParam(':idTime', $this->idTime);
        $stmt->bindParam(':idPlayer', $this->idPlayer);
        $stmt->bindParam(':idFormerWorld', $this->idFormerWorld);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
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